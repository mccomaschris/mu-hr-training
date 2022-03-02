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

	wp_enqueue_style( 'marsha-forms', get_theme_file_uri( 'css/marsha-forms.css' ), '', null, 'all' ); // phpcs:ignore

	$html = '';

	if ( ! get_query_var( 'courseid' ) ) {
		return 'Sorry that course was not found.';
	} else {

		$training_session = get_post( get_query_var( 'courseid' ) );
		$seats_total      = get_post_meta( get_query_var( 'courseid' ), 'mu_training_training_seats', true );

		if ( get_field( 'mu_training_benefits_training', $training_session->ID ) ) {
			$fields = array(
				'field_620fb36179601', // first name.
				'field_620fb8bd54fb8', // last name.
				'field_620fb36e79602', // preferred name.
				'field_620fb3822d9a5', // date of birth.
				'field_620fb39482d12', // email address.
				'field_620fb3a7dc992', // position title.
				'field_620fb3b5f9c95', // annual salary.
				'field_620fb3d67f534', // 9 month faculty.
				'field_620fb4007f535', // date of hire.
				'field_620fb40d7f536', // person completing request.
			);
		} else {
			$fields = array(
				'field_61ae472969cf9', // first name.
				'field_61ae473469cfa', // last name.
				'field_61b761c508b7f', // mu id number.
				'field_61ae4759ee64d', // department.
				'field_61ae473a69cfb', // email address.
				'field_61ae474469cfc', // phone number.
			);
		}

		$registrations = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'mu-registrations',
				'meta_key'    => 'muhr_registration_training_session', // phpcs:ignore
				'meta_value'  => get_query_var( 'courseid' ), // phpcs:ignore
			)
		);

		wp_reset_postdata();

		if ( intval( count( $registrations ) ) >= intval( $seats_total ) ) {
			return 'Sorry registration for this training is full.';
		}

		$training_info = 'Registering for ' . esc_attr( $training_session->post_title );

		if ( get_field( 'mu_training_start_time', $training_session->ID ) && get_field( 'mu_training_end_time', $training_session->ID ) ) {
			$training_info .= ' on ' . esc_attr( Carbon::parse( get_field( 'mu_training_start_time', $training_session->ID ) )->format( 'F j, g:ia' ) ) . ' - ' . esc_attr( Carbon::parse( get_field( 'mu_training_end_time', $training_session->ID ) )->format( 'g:ia' ) );
		}

		if ( get_field( 'mu_training_training_location', $training_session->ID ) ) {
			$training_info .= ' at ' . esc_attr( get_field( 'mu_training_training_location', $training_session->ID ) );
		}

		$training_info .= '.';

		if ( get_field( 'mu_training_benefits_training', $training_session->ID ) && get_field( 'benefits_session_confirmation_url', 'option' ) ) {
			$redirect_url = get_field( 'benefits_session_confirmation_url', 'option' );
		} else {
			$redirect_url = 'training/confirmation';
		}

		acf_form(
			array(
				'id'                 => 'new-registration',
				'post_id'            => 'new_post',
				'new_post'           => array(
					'post_type'   => 'mu-registrations',
					'post_status' => 'publish',
				),
				'return'             => home_url( $redirect_url ),
				'fields'             => $fields,
				'submit_value'       => 'Register',
				'html_after_fields'  => '<input type="hidden" name="acf[field_61ae470969cf8]" value="' . esc_attr( get_query_var( 'courseid' ) ) . '" />',
				'html_before_fields' => '<div class="w-full">' . do_shortcode( '[mu-hr-session-individual class="pb-12"]' ) . '</div>',
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
		if ( current_user_can( 'manage_options' ) ) {
			$html .= '<th>Edit</th>';
		}
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
			if ( current_user_can( 'manage_options' ) ) {
				$html .= '<td><a href="' . esc_url( home_url() ) . '/wp-admin/post.php?post=' . esc_attr( $registration->ID ) . '&action=edit">Edit this Registration</a></td>';
			}
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

/**
 * Show users the registration for or if full show a message apologizing the course is full.
 *
 * @param array  $atts The array of attributes included with the shortcode.
 * @param string $content The HTML string for the shortcode.
 * @return string
 */
function mu_hr_registration_individual_session( $atts, $content = null ) {
	$data = shortcode_atts(
		array(
			'id'                   => '',
			'class'                => '',
			'session_id'           => null,
			'show_register_button' => false,
		),
		$atts
	);

	if ( ! get_query_var( 'courseid' ) ) {
		if ( $data['session_id'] ) {
			$session_id = $data['session_id'];
		} else {
			return 'This session could not be found.';
		}
	} else {
		$session_id = get_query_var( 'courseid' );
	}

	$training_session = get_post( $session_id );

	$registrations = get_posts(
		array(
			'numberposts' => -1,
			'post_type'   => 'mu-registrations',
			'meta_key'    => 'muhr_registration_training_session',
			'meta_value'  => $training_session->ID,
		)
	);

	$seats_total = get_field( 'mu_training_training_seats', $training_session->ID );

	$seats_left = intval( $seats_total ) - intval( count( $registrations ) );

	wp_reset_postdata();

	$output  = '<div id="course' . esc_attr( $training_session->ID ) . '"  class="block">';
	$output .= '<div class="flex flex-col border-gray-100 border rounded my-6">';
	$output .= '<div class="border-b border-gray-100 flex flex-row items-start py-4 px-4 lg:px-6">';

	$output .= '<div class="flex-col flex w-12 lg:w-16 mx-auto">';
	$output .= '<div class="bg-green text-white text-xl font-bold uppercase py-1 rounded-t text-center">' . Carbon::parse( get_field( 'mu_training_start_time', $training_session->ID ) )->format( 'M' ) . '</div>';
	$output .= '<div class="bg-gray-100 text-sm lg:text-xl font-bold uppercase py-1 rounded-b text-center">' . Carbon::parse( get_field( 'mu_training_start_time', $training_session->ID ) )->format( 'j' ) . '</div>';
	$output .= '</div>';

	$output .= '<div class="ml-4 lg:ml-6 flex-1">';
	$output .= '<div class="">';
	$output .= '<span class="font-semibold">' . esc_attr( $training_session->post_title ) . '</span>';
	$output .= '<div class="text-sm"><span class="font-semibold">Location:</span> ' . esc_attr( get_field( 'mu_training_training_location', $training_session->ID ) ) . '</div>';
	$output .= '<div class="text-sm">' . esc_attr( Carbon::parse( get_field( 'mu_training_start_time', $training_session->ID ) )->format( 'F j, g:ia' ) ) . ' - ' . esc_attr( Carbon::parse( get_field( 'mu_training_end_time', $training_session->ID ) )->format( 'g:ia' ) ) . ' · <span class="font-semibold">' . esc_attr( $seats_left ) . '</span> spots remaining</div> <span class="hidden">Seats taken: ' . intval( count( $registrations ) ) . '</span>';
	$output .= '<div class="text-sm"><span class="font-semibold">Instructor:</span> ' . esc_attr( get_field( 'mu_training_instructor', $training_session->ID )['instructor_name'] ) . ' (<a href="' . esc_url( home_url() ) . '/training/registered-list/?courseid=' . esc_attr( $training_session->ID ) . '">Instructor Access</a>)</div>';

	$training = get_term( get_field( 'mu_training_type', $training_session->ID ), 'mu-training' );

	if ( get_field( 'mu_training_course_description', $training_session->ID ) ) {
		$output .= '<div class="my-4">' . wp_kses_post( get_field( 'mu_training_course_description', $training_session->ID ) ) . '</div>';
	} else {
		$output .= '<div class="my-4">' . wp_kses_post( $training->description ) . '</div>';
	}

	$output .= '</div>';

	if ( $data['show_register_button'] ) {
		$output .= '<div class="mt-6">';
		$output .= '<a href="' . esc_url( home_url() ) . '/training/registration/?courseid=' . esc_attr( $training_session ) . '" class="btn btn-green">Register</a>';
		$output .= '</div>';
	}

	$output .= '</div>';
	$output .= '</div>';
	$output .= '</div>';
	$output .= '</div>';
	return $output;
}
add_shortcode( 'mu-hr-session-individual', 'mu_hr_registration_individual_session' );
