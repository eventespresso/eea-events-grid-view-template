<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) {
    exit();
}



/**
 * EES_Espresso_Grid_Template
 *
 * @package            Event Espresso
 * @subpackage         espresso-grid-template
 *                     @ author                Seth Shoultes
 *                     @ version            $VID:$
 */
class EES_Espresso_Grid_Template extends EES_Shortcode
{



    /**
     *    set_hooks - for hooking into EE Core, modules, etc
     *
     * @access    public
     * @return    void
     */
    public static function set_hooks()
    {
    }



    /**
     *    set_hooks_admin - for hooking into EE Admin Core, modules, etc
     *
     * @access    public
     * @return    void
     */
    public static function set_hooks_admin()
    {
    }



    /**
     *    set_definitions
     *
     * @access    public
     * @return    void
     */
    public static function set_definitions()
    {
    }



    /**
     *    run - initial shortcode module setup called during "wp_loaded" hook
     *    this method is primarily used for loading resources that will be required by the shortcode when it is actually processed
     *
     * @access    public
     * @param     WP $WP
     * @return    void
     */
    public function run(WP $WP)
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 10);
        // You might want this, but delete if you don't need the template tags
        EE_Registry::instance()->load_helper('Event_View');
        EE_Registry::instance()->load_helper('Venue_View');
    }



    /**
     *    enqueue_scripts - Load the scripts and css
     *
     * @access    public
     * @return    void
     */
    public function enqueue_scripts()
    {
        //Check to see if the grid_template css file exists in the '/uploads/espresso/' directory
        if (is_readable(EVENT_ESPRESSO_UPLOAD_DIR . 'css' . DS . 'espresso_grid_template.css')) {
            //This is the url to the css file if available
            wp_register_style(
                'espresso_grid_template',
                EVENT_ESPRESSO_UPLOAD_URL . 'css' . DS . 'espresso_grid_template.css'
            );
        } else {
            // EE grid_template style
            wp_register_style(
                'espresso_grid_template',
                EE_GRID_TEMPLATE_URL . 'css' . DS . 'espresso_grid_template.css'
            );
        }
        // grid_template script
        wp_register_script(
            'espresso_grid_template',
            EE_GRID_TEMPLATE_URL . 'scripts' . DS . 'espresso_grid_template.js',
            array('jquery'),
            EE_GRID_TEMPLATE_VERSION,
            true
        );
        // enqueue
        wp_enqueue_style('espresso_grid_template');
        wp_enqueue_script('espresso_grid_template');
    }



    /**
     *    process_shortcode
     *    [ESPRESSO_GRID_TEMPLATE]
     *
     * @access    public
     * @param    array $attributes
     * @return    string
     */
    public function process_shortcode($attributes = array())
    {
        // make sure $attributes is an array
        $attributes = array_merge(
        // defaults
            array(
                'template_file' => 'espresso-grid-template.template.php', //Default template file
                //'title' => NULL,
                'limit'         => 10,
                //'css_class' => NULL,
                'show_expired'  => false,
                'month'         => null,
                'category_slug' => null,
                'order_by'      => 'start_date',
                'sort'          => 'ASC',
                //'show_featured' => '0',
                //'table_header' => '1'
            ),
            (array)$attributes
        );
        // the following get sanitized/whitelisted in EEH_Event_Query
        $custom_sanitization = array(
            'category_slug' => 'skip_sanitization',
            'show_expired'  => 'skip_sanitization',
            'order_by'      => 'skip_sanitization',
            'month'         => 'skip_sanitization',
            'sort'          => 'skip_sanitization',
        );
        if (method_exists('\EES_Shortcode', 'sanitize_attributes')) {
            $attributes = \EES_Shortcode::sanitize_attributes($attributes, $custom_sanitization);
        } else {
            $attributes = $this->sanitize_the_attributes($attributes, $custom_sanitization);
        }
        // run the query
        global $wp_query;
        $wp_query = new EE_Grid_Template_Query($attributes);
        // now filter the array of locations to search for templates
        add_filter(
            'FHEE__EEH_Template__locate_template__template_folder_paths',
            array($this, 'template_folder_paths')
        );
        // load our template
        $grid_template = EEH_Template::locate_template($attributes['template_file'], $attributes);
        // now reset the query and postdata
        wp_reset_query();
        wp_reset_postdata();
        return $grid_template;
    }



    /**
     *    template_folder_paths
     *
     * @access    public
     * @param array $template_folder_paths
     * @return    array
     */
    public function template_folder_paths($template_folder_paths = array())
    {
        $template_folder_paths[] = EE_GRID_TEMPLATE_TEMPLATES;
        return $template_folder_paths;
    }



    /**
     * temporary copy of \EES_Shortcode::sanitize_attributes()
     * for backwards compatibility sake
     *
     * @param array $attributes
     * @param array $custom_sanitization
     * @return array
     */
    private function sanitize_the_attributes(array $attributes, $custom_sanitization = array())
    {
        foreach ($attributes as $key => $value) {
            // is a custom sanitization callback specified ?
            if (isset($custom_sanitization[$key])) {
                $callback = $custom_sanitization[$key];
                if ($callback === 'skip_sanitization') {
                    $attributes[$key] = $value;
                    continue;
                } else if (function_exists($callback)) {
                    $attributes[$key] = $callback($value);
                    continue;
                }
            }
            switch (true) {
                case $value === null :
                case is_int($value) :
                case is_float($value) :
                    // typical booleans
                case in_array($value, array(true, 'true', '1', 'on', 'yes', false, 'false', '0', 'off', 'no'), true) :
                    $attributes[$key] = $value;
                    break;
                case is_string($value) :
                    $attributes[$key] = sanitize_text_field($value);
                    break;
                case is_array($value) :
                    $attributes[$key] = $this->sanitize_the_attributes($attributes);
                    break;
                default :
                    // only remaining data types are Object and Resource
                    // which are not allowed as shortcode attributes
                    $attributes[$key] = null;
                    break;
            }
        }
        return $attributes;
    }



}



/**
 * Class EE_Grid_Template_Query
 * Description
 *
 * @package               Event Espresso
 * @subpackage            core
 * @author                Brent Christensen
 * @since                 4.4
 */
class EE_Grid_Template_Query extends WP_Query
{

    private $_limit        = 10;

    private $_show_expired = false;

    private $_month;

    private $_category_slug;

    private $_order_by;

    private $_sort;



    /**
     * @param array $args
     */
    public function __construct($args = array())
    {
        // incoming args could be a mix of WP query args + EE shortcode args
        foreach ($args as $key => $value) {
            $property = '_' . $key;
            // if the arg is a property of this class, then it's an EE shortcode arg
            if (property_exists($this, $property)) {
                // set the property value
                $this->$property = $value;
                // then remove it from the array of args that will later be passed to WP_Query()
                unset($args[$key]);
            }
        }
        // parse orderby attribute
        if ($this->_order_by !== null) {
            $this->_order_by = explode(',', $this->_order_by);
            $this->_order_by = array_map('trim', $this->_order_by);
        }
        $this->_sort = in_array($this->_sort, array('ASC', 'asc', 'DESC', 'desc'), true)
            ? strtoupper($this->_sort)
            : 'ASC';
        // setup the events list query
        EE_Registry::instance()->load_helper('Event_Query');
        //add query filters
        EEH_Event_Query::add_query_filters();
        // set params that will get used by the filters
        EEH_Event_Query::set_query_params($this->_month, $this->_category_slug, $this->_show_expired, $this->_order_by,
            $this->_sort);
        // the current "page" we are viewing
        $paged = max(1, get_query_var('paged'));
        // Force these args
        $args = array_merge($args, array(
            'post_type'              => 'espresso_events',
            'posts_per_page'         => $this->_limit,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'paged'                  => $paged,
            'offset'                 => ($paged - 1) * $this->_limit
        ));
        // run the query
        parent::__construct($args);
    }



}
// End of file EES_Espresso_Grid_Template.shortcode.php
// Location: /wp-content/plugins/espresso-grid-template/EES_Espresso_Grid_Template.shortcode.php