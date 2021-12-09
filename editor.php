<?php
/**
 * Customization of the editor for Training and Sessions
 *
 * @package MU HR Training
 */

require WP_PLUGIN_DIR . '/mu-hr-train/vendor/autoload.php';
use Carbon\Carbon;

/**
 * Remove YoastSEO metaboxes from Sessions
 */
function mu_hr_training_remove_seo_metaboxes() {
	remove_meta_box( 'wpseo_meta', 'mu-session', 'normal' );
}
add_action( 'add_meta_boxes', 'mu_hr_training_remove_seo_metaboxes', 11 );

/**
 * Set the Session title based on the select Training taxonomy title.
 *
 * @param integer $post_id The ID of the current post.
 */
function mu_hr_training_update_title_and_taxonomy_on_save( $post_id ) {
	if ( 'mu-session' === get_post_type( $post_id ) && get_field( 'mu_training_type', $post_id ) ) {
		wp_set_post_terms( $post_id, get_field( 'mu_training_type', $post_id ), 'mu-training' );

		$the_term = get_term_by( 'id', get_field( 'mu_training_type', $post_id ), 'mu-training' );

		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => $the_term->name,
			),
		);
	}
}
add_action( 'acf/save_post', 'mu_hr_training_update_title_and_taxonomy_on_save', 20 );

/**
 * Set Custom Columns for Sessions
 *
 * @param type $columns Default WordPress post columns.
 */
function mu_hr_trianing_sessions_custom_columns( $columns ) {

	if ( ! is_super_admin() ) {
		unset( $columns['date'] );
		unset( $columns['modified'] );
	}

	unset( $columns['wpseo-score'] );
	unset( $columns['wpseo-score-readability'] );
	unset( $columns['wpseo-title'] );
	unset( $columns['wpseo-metadesc'] );
	unset( $columns['wpseo-focuskw'] );
	unset( $columns['wpseo-links'] );
	unset( $columns['wpseo-linked'] );
	$columns['start_time'] = __( 'Start Time', 'mu-hr-training' );
	$columns['end_time']   = __( 'End Time', 'mu-hr-training' );
	$columns['seats']      = __( 'Seats Available', 'mu-hr-training' );
	return $columns;
}
add_filter( 'manage_mu-session_posts_columns', 'mu_hr_trianing_sessions_custom_columns' );


/**
 * Add values from meta fields to custom columns.
 *
 * @param array   $column Default WordPress post columns.
 * @param integer $post_id The ID of the post.
 */
function mu_hr_training_custom_columns_data( $column, $post_id ) {
	switch ( $column ) {
		case 'start_time':
			echo esc_attr( get_post_meta( $post_id, 'mu_training_start_time', true ) );
			break;
		case 'end_time':
			echo esc_attr( get_post_meta( $post_id, 'mu_training_end_time', true ) );
			break;
		case 'seats':
			echo esc_attr( get_post_meta( $post_id, 'mu_training_training_seats', true ) );
			break;
	}
}
add_action( 'manage_mu-session_posts_custom_column', 'mu_hr_training_custom_columns_data', 10, 2 );
