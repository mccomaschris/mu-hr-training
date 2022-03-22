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
	if ( ! is_admin() ) {
		acf_form_head();
	}
}
add_action( 'init', 'mu_hr_training_form_head' );

/**
 * Add title and registration date to register post type
 *
 * @param integer $post_id The ID of the post.
 */
function mu_hr_registration_submitted_registration( $post_id ) {
	if ( 'mu-registrations' !== get_post_type( $post_id ) ) {
		return;
	}

	update_field( 'muhr_registration_registration_date', Carbon::now()->timezone( 'America/Detroit' ), $post_id );

	$updated_post = array(
		'ID'         => $post_id,
		'post_title' => 'Registration for ' . get_field( 'muhr_registration_first_name', $post_id ) . ' ' . get_field( 'muhr_registration_last_name', $post_id ),
	);

	remove_action( 'acf/save_post', 'mu_hr_registration_submitted_registration', 20 );

	wp_update_post( $updated_post );

	add_action( 'acf/save_post', 'mu_hr_registration_submitted_registration', 20 );

	if ( get_field( 'muhr_registration_email_address', $post_id ) && ! is_admin() ) {
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
		mail( get_field( 'muhr_registration_email_address', $post_id ), 'HR Training Registration', $email_body, $headers );
	}

	if ( get_field( 'muhr_registration_request_email', $post_id ) && get_field( 'muhr_registration_supervisor_email', $post_id ) ) {

		$email_body  = '';
		$email_body .= '<style type="text/css">';
		$email_body .= '@media only screen and (max-width: 480px){';
		$email_body .= '#templateColumns{';
		$email_body .= 'width:100% !important;';
		$email_body .= '}';
		$email_body .= '.templateColumnContainer{';
		$email_body .= 'display:block !important;';
		$email_body .= 'width:100% !important;';
		$email_body .= '}';
		$email_body .= '.columnImage{';
		$email_body .= 'height:auto !important;';
		$email_body .= 'max-width:480px !important;';
		$email_body .= 'width:100% !important;';
		$email_body .= '}';
		$email_body .= '.leftColumnContent{';
		$email_body .= 'font-size:16px !important;';
		$email_body .= 'line-height:125% !important;';
		$email_body .= '}';
		$email_body .= '.rightColumnContent{';
		$email_body .= 'font-size:16px !important;';
		$email_body .= 'line-height:125% !important;';
		$email_body .= '}';
		$email_body .= '}';
		$email_body .= '</style>';
		$email_body .= '<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateColumns">';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Name</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_first_name', $post_id ) ) . ' ' . esc_attr( get_field( 'muhr_registration_last_name', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Date of Birth</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_birthdate', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Email Address</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_email_address', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Title</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_title', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Annual Salary</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_salary', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Hire Date</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_hiredate', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">9 Month Faculty</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_nine_month', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">How Are You Paid?</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_paid', $post_id )['choices'][ $value ] ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Are you transferring from another state agency that has PEIA?</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_transfer', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		if ( get_field( 'muhr_registration_previous_agency', $post_id ) ) {
			$email_body .= '<tr>';
			$email_body .= '<td align="center" valign="top" width="50%">Are you transferring from another state agency that has PEIA?</td>';
			$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_previous_agency', $post_id ) ) . '</td>';
			$email_body .= '</tr>';
		}
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Name of Person Completing Request</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_request_name', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Email of Person Completing Request</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_request_email', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '<tr>';
		$email_body .= '<td align="center" valign="top" width="50%">Supervisor Email</td>';
		$email_body .= '<td align="center" valign="top" width="50%">' . esc_attr( get_field( 'muhr_registration_supervisor_email', $post_id ) ) . '</td>';
		$email_body .= '</tr>';
		$email_body .= '</table>';

		// $to      = get_field( 'muhr_registration_request_email', $post_id ) . ',' . get_field( 'muhr_registration_supervisor_email', $post_id );
		$to      = 'cmccomas@marshall.edu';
		$headers = 'From: human-resources@marshall.edu';

		mail( $to, 'HR Benefits Registration', $email_body, $headers );
	}
}
add_action( 'acf/save_post', 'mu_hr_registration_submitted_registration', 20 );
