<?php
/**
 * Custom shortcodes used by the plugin.
 *
 * @package MU HR Training
 */

require WP_PLUGIN_DIR . '/mu-hr-train/vendor/autoload.php';
use Carbon\Carbon;

/**
 * Show users the registration for or if full show a message apologizing the course is full.
 *
 * @param array  $atts The array of attributes included with the shortcode.
 * @param string $content The HTML string for the shortcode.
 * @return string
 */
function mu_hr_registration_register_shortcode( $atts, $content = null ) {
	$data = shortcode_atts(
		array(
			'id'    => '',
			'class' => '',
		),
		$atts
	);

	$html = '';

	if ( ! get_query_var( 'courseid' ) ) {
		return 'Sorry that course was not found.';
	} else {

		$training_session = get_post( get_query_var( 'courseid' ) );
		$seats_total      = get_post_meta( get_query_var( 'courseid' ), 'mu_training_training_seats', true );

		$registrations = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'mu-registrations',
				'meta_key'    => 'muhr_registration_training_session',
				'meta_value'  => get_query_var( 'courseid' ),
			)
		);

		wp_reset_postdata();

		if ( intval( count( $registrations ) ) >= intval( $seats_total ) ) {
			return 'Sorry registration for this training is full.';
		}

		$fields = array(
			'field_61ae472969cf9', // first name.
			'field_61ae473469cfa', // last name.
			'field_61ae4759ee64d', // department.
			'field_61ae473a69cfb', // email address.
			'field_61ae474469cfc', // phone number.
		);

		$training_info = 'Registering for ' . esc_attr( $training_session->post_title );

		if ( get_field( 'mu_training_start_time', $training_session->ID ) && get_field( 'mu_training_end_time', $training_session->ID ) ) {
			$training_info .= ' on ' . esc_attr( Carbon::parse( get_field( 'mu_training_start_time', $training_session->ID ) )->format( 'F j, g:ia' ) ) . ' - ' . esc_attr( Carbon::parse( get_field( 'mu_training_end_time', $training_session->ID ) )->format( 'g:ia' ) );
		}

		if ( get_field( 'mu_training_training_location', $training_session->ID ) ) {
			$training_info .= ' at ' . esc_attr( get_field( 'mu_training_training_location', $training_session->ID ) );
		}

		$training_info .= '.';

		acf_form(
			array(
				'id'                 => 'new-registration',
				'post_id'            => 'new_post',
				'new_post'           => array(
					'post_type'   => 'mu-registrations',
					'post_status' => 'publish',
				),
				'return'             => home_url( 'training/confirmation/' ),
				'fields'             => array(
					'field_61b761c508b7f', // mu id number.
					'field_61ae472969cf9', // first name.
					'field_61ae473469cfa', // last name.
					'field_61ae4759ee64d', // department.
					'field_61ae473a69cfb', // email address.
					'field_61ae474469cfc', // phone number.
				),
				'submit_value'       => 'Register',
				'html_after_fields'  => '<input type="hidden" name="acf[field_61ae470969cf8]" value="' . esc_attr( get_query_var( 'courseid' ) ) . '" />',
				'html_before_fields' => '<div>' . $training_info . '</div>',
			)
		);
	}
}
add_shortcode( 'mu-hr-register', 'mu_hr_registration_register_shortcode' );

/**
 * Show users the registration for or if full show a message apologizing the course is full.
 *
 * @param array  $atts The array of attributes included with the shortcode.
 * @param string $content The HTML string for the shortcode.
 * @return string
 */
function mu_hr_registration_registration_list( $atts, $content = null ) {
	$data = shortcode_atts(
		array(
			'id'    => '',
			'class' => '',
		),
		$atts
	);

	if ( ! get_query_var( 'courseid' ) ) {
		return 'Sorry that course was not found.';
	} else {
		$training_session = get_post( get_query_var( 'courseid' ) );

		$registrations = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'mu-registrations',
				'meta_query'  => array(
					'relation'   => 'AND',
					'last_name'  => array(
						'key' => 'muhr_registration_last_name',
					),
					'first_name' => array(
						'key' => 'muhr_registration_first_name',
					),
					'session_id' => array(
						'key'   => 'muhr_registration_training_session',
						'value' => get_query_var( 'courseid' ),
					),
				),
				'orderby'     => array(
					'last_name'  => 'asc',
					'first_name' => 'asc',
				),
			)
		);

		$html  = '<h2>Individuals Registered for ' . esc_attr( $training_session->post_title ) . '</h2>';
		$html .= '<div class="large-table">';
		$html .= '<table class="table w-full table-striped table-bordered">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th>Last Name</th>';
		$html .= '<th>First Name</th>';
		$html .= '<th>Department</th>';
		$html .= '<th>Email Address</th>';
		$html .= '<th>MU ID Number</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		foreach ( $registrations as $registration ) {
			$html .= '<tr>';
			$html .= '<td>' . get_field( 'muhr_registration_last_name', $registration->ID ) . '</td>';
			$html .= '<td>' . get_field( 'muhr_registration_first_name', $registration->ID ) . '</td>';
			$html .= '<td>' . get_field( 'muhr_registration_department', $registration->ID ) . '</td>';
			$html .= '<td><a href="mailto:' . get_field( 'muhr_registration_email_address', $registration->ID ) . '">' . get_field( 'muhr_registration_email_address', $registration->ID ) . '</a></td>';
			$html .= '<td>' . get_field( 'muhr_registration_mu_id', $registration->ID ) . '</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';

		wp_reset_postdata();
	}
	return $html;
}
add_shortcode( 'mu-hr-registration-list', 'mu_hr_registration_registration_list' );
