<?php
/*
  Plugin Name: Event Espresso - Grid View Template (EE 4.4.9+)
  Plugin URI: https://eventespresso.com
  Description: Events Grid View Template is an add-on for Event Espresso that will allow you to show your events in a grid layout. Add the [ESPRESSO_GRID_TEMPLATE] shortcode to a WordPress post or page.
  Shortcode Example: [ESPRESSO_GRID_TEMPLATE]
  Shortcode Parameters: button_text = "Register Now!", alt_button_text = "View Details", default_image = image URL, limit = 10, show_expired = FALSE, month = NULL, category_slug = NULL, order_by = start_date, sort = ASC
  Version: 1.2.4.p
  Author: Event Espresso
  Author URI: https://eventespresso.com
  Copyright 2014 Event Espresso (email : support@eventespresso.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA
 *
 * ------------------------------------------------------------------------
 *
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package		Event Espresso
 * @ author			Event Espresso
 * @ copyright	(c) 2008-2014 Event Espresso  All Rights Reserved.
 * @ license		https://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link				https://eventespresso.com
 * @ version	 	EE4
 *
 * ------------------------------------------------------------------------
 */
// grid_template version
define( 'EE_GRID_TEMPLATE_VERSION', '1.2.4.p' );
define( 'EE_GRID_TEMPLATE_PLUGIN_FILE',  plugin_basename( __FILE__ ) );

function load_espresso_grid_template() {
	if ( class_exists( 'EE_Addon' )) {
		require_once ( plugin_dir_path( __FILE__ ) . 'EE_Grid_Template.class.php' );
		EE_Grid_Template::register_addon();
	}
}
add_action( 'AHEE__EE_System__load_espresso_addons', 'load_espresso_grid_template' );

// End of file espresso_grid_template.php
// Location: wp-content/plugins/espresso-grid-template/espresso_grid_template.php
