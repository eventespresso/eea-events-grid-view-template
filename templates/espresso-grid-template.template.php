<?php
// Options
$date_option = get_option( 'date_format' );
$time_option = get_option( 'time_format' );
$temp_month = '';
if ( have_posts() ) :
	// allow other stuff
	do_action( 'AHEE__espresso_grid_template_template__before_loop' );
	?>
	<div id="mainwrapper" class="espresso-grid">
	<?php
	// Start the Loop.
	while ( have_posts() ) : the_post();
		// Include the post TYPE-specific template for the content.
		global $post;

		//Debug
		//d( $post );		

		//Create the event link
		$button_text		= !isset($button_text) ? __('Register Now!', 'event_espresso') : $button_text;
		$alt_button_text	= !isset($alt_button_text) ? __('View Details', 'event_espresso') : $alt_button_text;//For alternate registration pages
		$external_url 		= $post->EE_Event->external_url();
		$button_text		= !empty($external_url) ? $alt_button_text : $button_text;
		$registration_url 	= !empty($external_url) ? $post->EE_Event->external_url() : $post->EE_Event->get_permalink();
		$feature_image_url	= $post->EE_Event->feature_image_url();
		
		if(!isset($default_image) || $default_image == '') { 
			$default_image = EE_GRID_TEMPLATE_URL .'/images/default.jpg';
		}
		
		$image = !empty($feature_image_url) ? $feature_image_url : $default_image;
		
		//Debug
		//d( $post );

		
		?>


		<div class="ee_grid_box item">
                <a id="a_register_link-<?php echo $post->ID; ?>" href="<?php echo $registration_url; ?>" class="darken">
                    <img src="<?php echo $image; ?>" alt="" />
                    <span>
                        <h2>
                        <span>

                            <?php
							
								echo $post->post_title.'<br />';
								/*if($event->event_cost === "0.00") {
									echo __('FREE', 'event_espresso');
								}else {
									echo $org_options['currency_symbol'] . $event->event_cost;
								}*/
								echo '<br />';
								espresso_event_date( $date_option, $time_option );
								echo '<br /><br />';
								echo $button_text;
							
							?>

                        </span>
                        </h2>
                    </span>
                </a>
            </div>

		<?php

	endwhile;
	echo '</div>';
	// allow moar other stuff
	do_action( 'AHEE__espresso_grid_template_template__after_loop' );

else :
	// If no content, include the "No posts found" template.
	espresso_get_template_part( 'content', 'none' );

endif;
