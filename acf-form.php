<?php
/**
 * Functions required for ACF Registration Form
 *
 * @package MU HR Training
 */

use Carbon\Carbon;

/**
 * Remove ACF's default styling for forms.
 */
function mu_hr_training_acf_form_deregister_styles() {
	wp_deregister_style( 'acf-global' );
	wp_deregister_style( 'acf-input' );
	wp_register_style( 'acf-global', false, true, 'all' );
	wp_register_style( 'acf-input', false, true, 'all' );

}
add_action( 'wp_enqueue_scripts', 'mu_hr_training_acf_form_deregister_styles' );

/**
 * Register acf_form_head
 */
function mu_hr_training_form_head() {
	acf_form_head();
}
add_action( 'init', 'mu_hr_training_form_head' );

/**
 * Add title and registration date to register post type
 *
 * @param integer $post_id The ID of the post.
 */
function mu_hr_registration_submitted_registration( $post_id ) {
	if ( 'mu-registrations' !== get_post_type( $post_id ) || is_admin() ) {
		return;
	}

	wp_update_post(
		array(
			'ID'         => $post_id,
			'post_title' => 'Registration from ' . get_field( 'muhr_registration_first_name', $post_id ) . ' ' . get_field( 'muhr_registration_last_name', $post_id ),
			'meta_input' => array(
				'muhr_registration_registration_date' => Carbon::now()->timezone( 'America/Detroit' ),
			),
		),
	);

	if ( get_field( 'muhr_registration_email_address', $post_id ) ) {
		$training_session = get_post( get_field( 'muhr_registration_training_session', $post_id ) );

		$course_name       = $training_session->post_title;
		$course_location   = get_field( 'mu_training_training_location', $training_session->ID );
		$course_day        = Carbon::parse( get_field( 'mu_training_start_time', $training_session->ID ) )->format( 'F j, Y' );
		$course_start_time = Carbon::parse( get_field( 'mu_training_start_time', $training_session->ID ) )->format( 'g:i a' );
		$course_end_time   = Carbon::parse( get_field( 'mu_training_end_time', $training_session->ID ) )->format( 'g:i a' );

		$email_body  = 'You have successfully registered for ' . $course_name . ' at ' . $course_location . ' on ' . $course_day . ' at ' . $course_start_time . ' - ' . $course_end_time;
		$email_body .= ".\r\r";
		$email_body .= 'For any questions please contact Human Resources.';

		$headers = 'From: human-resources@marshall.edu';
		// mail( get_field( 'muhr_registration_email_address', $post_id ), 'HR Training Registration', $email_body, $headers );
	}
}
add_action( 'acf/save_post', 'mu_hr_registration_submitted_registration', 15 );