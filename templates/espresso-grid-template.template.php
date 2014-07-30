<?php
$temp_month = '';
if ( have_posts() ) :
	// allow other stuff
	do_action( 'AHEE__espresso_grid_template_template__before_loop' );
	echo '<table class="cal-table-list">';
	// Start the Loop.
	while ( have_posts() ) : the_post();
		// Include the post TYPE-specific template for the content.
		global $post;

		//Debug
		//d( $post );
		
		//Check if external URL
		$external_url = $post->EE_Event->external_url();

		//Create the URL to the event
		$registration_url = !empty($external_url) ? $post->EE_Event->external_url() : $post->EE_Event->get_permalink();
		
		//Create the registrer now button
		$live_button 		= '<a id="a_register_link-'.$post->ID.'" href="'.$registration_url.'"><img class="buytix_button" src="'.EE_GRID_TEMPLATE_URL . 'images' . DS .'register-now.png" alt="Buy Tickets"></a>';
		
		//Get the venue for this event
		$venues = espresso_event_venues();
		$venue = array_shift( $venues );
		
		//Debug
		//d( $venue );

		if ( $venue instanceof EE_Venue ) {
			$venue_name = $venue->name();
			$venue_address = $venue->address();
			$venue_city = $venue->city();
			if ($venue->state_obj() instanceof EE_State ) {
				$state = $venue->state_obj()->name();
			}
		}

		 
			$full_month = date("F", strtotime($post->DTT_EVT_start));
			if ($temp_month != $full_month){
				?>
				<tr class="cal-header-month">
					<th class="cal-header-month-name" id="calendar-header-<?php echo $full_month; ?>" colspan="3"><?php echo $full_month; ?></th>
				</tr>
			<?php 
			if(isset($table_header ) && $table_header == '1') { ?>

				<tr class="cal-header">
					<th><?php echo !isset($show_featured) || $show_featured === 'false' ? __('Date','event_espresso') :  '' ?></th>
					<th class="th-event-info"><?php if(isset($change_title)) { echo $change_title; } else { _e('Band / Artist','event_espresso'); } ?></th>
					<th class="th-tickets"><?php _e('Tickets','event_espresso'); ?></th>
				</tr>
				<?php
			}
				$temp_month = $full_month;
			}


		//Start the table
		echo '<tr class="event-row" id="event-row-'. $post->ID .'">';
		if(isset($show_featured ) && $show_featured == '1') { ?>
				<td class="td-fet-image"><div class="">
					<?php echo $post->EE_Event->feature_image('thumbnail'/*, array('align'=>'left', 'style'=>'margin:10px; border:1px solid #ccc')*/); ?>
				</div></td>
		<?php } else { ?>
			<td class="td-date-holder"><div class="dater">
					<div class="cal-day-title"><?php echo date("l", strtotime($post->DTT_EVT_start)); ?></div>
					<div class="cal-day-num"><?php echo date("j", strtotime($post->DTT_EVT_start)); ?></div>
					<div><span><?php echo date("M", strtotime($post->DTT_EVT_start)); ?></span></div>
				<?php } ?>
				</div>
			</td>
		<?php

		echo '<td class="td-event-info"><span class="event-title"><a href="'. $registration_url .'">'.$post->post_title.'</a></span>';
		echo '<p>';

		//Start date/time
		echo date(get_option('date_format'). ' '.get_option('time_format'), strtotime($post->DTT_EVT_start)). '<br />';
		echo (isset($venue_name) && !empty($venue_name)) ? $venue_name : '';
		echo (isset($venue_city) && !empty($venue_city)) ? ', '.$venue_city :'';
		echo (isset($state) && !empty($state)) ? ', '.$state : '';
		echo '</p>';
		//Event description
		$event_desc = explode('<!--more-->', $post->post_content);
		$event_desc = array_shift( $event_desc );
		echo wpautop($event_desc); 
		echo '</td>';
		echo '<td class="td-event-register">'.$live_button.'</td>';
		echo '</tr>';
	endwhile;
	echo '</table>';
	// allow moar other stuff
	do_action( 'AHEE__espresso_grid_template_template__after_loop' );

else :
	// If no content, include the "No posts found" template.
	espresso_get_template_part( 'content', 'none' );

endif;
