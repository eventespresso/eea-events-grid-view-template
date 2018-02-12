<?php
// Options
$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$temp_month = '';
$reg_button_text = !isset($button_text) ? __('Register Now!', 'event_espresso') : $button_text;
$alt_button_text = !isset($alt_button_text) ? __('View Details', 'event_espresso') : $alt_button_text;//For alternate registration pages

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
		$external_url 		= $post->EE_Event->external_url();
		$button_text		= !empty($external_url) ? $alt_button_text : $reg_button_text;
		$registration_url 	= !empty($external_url) ? $post->EE_Event->external_url() : $post->EE_Event->get_permalink();
		$feature_image_url	= $post->EE_Event->feature_image_url();

		if(!isset($default_image) || $default_image == '') {
			$default_image = EE_GRID_TEMPLATE_URL .'images/default.jpg';
		}

		$image = !empty($feature_image_url) ? $feature_image_url : $default_image;

		$datetimes = EEM_Datetime::instance()->get_datetimes_for_event_ordered_by_start_time( $post->ID, $show_expired, false, 1 );

		$datetime = end( $datetimes );
        if ($datetime instanceof EE_Datetime) {

		$startdate = date_i18n( $date_format . ' ' . $time_format, strtotime( $datetime->start_date_and_time('Y-m-d', 'H:i:s') ) );
		?>


		<div class="ee_grid_box_v2 item">
				<img src="<?php echo $image; ?>" alt="" />
				<div onclick="" class="darken ee_overlay">
					<p class="event-link"><?php echo '<a class="register-link button" id="a_register_link-' . $post->ID .'" href="' . $registration_url . '">' . $button_text . '</a>'; ?></p>
					<div class="event-title title"><?php echo $post->post_title; ?></div>
					<p class="start-date"><?php echo $startdate; ?></p>
				</div>
		</div>

		<?php
        }
	endwhile;
	echo '</div>';
	// allow moar other stuff
	do_action( 'AHEE__espresso_grid_template_template__after_loop' );

else :
	// If no content, include the "No posts found" template.
	espresso_get_template_part( 'content', 'none' );

endif;
