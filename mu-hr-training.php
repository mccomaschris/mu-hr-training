<?php
/**
 * MU HR Training
 *
 * Plugin to allow MU Human Resources to list trainings and allow individuals to register for training.
 *
 * @package  MU HR Training
 *
 * Plugin Name:  MU HR Training
 * Plugin URI: https://www.marshall.edu
 * Description: Plugin to allow MU Human Resources to list trainings and allow individuals to register for training.
 * Version: 1.0
 * Author: Marshall University
 */

if ( ! class_exists( 'ACF' ) ) {
	return new WP_Error( 'broke', __( 'Advanced Custom Fields is required for this plugin.', 'mu-hr-training' ) );
}

require WP_PLUGIN_DIR . '/mu-hr-train/vendor/autoload.php';
use Carbon\Carbon;

require plugin_dir_path( __FILE__ ) . '/acf-fields.php';
require plugin_dir_path( __FILE__ ) . '/editor.php';
require plugin_dir_path( __FILE__ ) . '/shortcodes.php';

/**
 * Register a custom post type called "mu-session.
 *
 * @see get_post_type_labels() for label keys.
 */
function mu_hr_training_session_post_type() {
	$labels = array(
		'name'                  => _x( 'Training Sessions', 'Post type general name', 'mu-hr-training' ),
		'singular_name'         => _x( 'Training Session', 'Post type singular name', 'mu-hr-training' ),
		'menu_name'             => _x( 'Training Sessions', 'Admin Menu text', 'mu-hr-training' ),
		'name_admin_bar'        => _x( 'Training Session', 'Add New on Toolbar', 'mu-hr-training' ),
		'add_new'               => __( 'Add New', 'mu-hr-training' ),
		'add_new_item'          => __( 'Add New Training Session', 'mu-hr-training' ),
		'new_item'              => __( 'New Training Session', 'mu-hr-training' ),
		'edit_item'             => __( 'Edit Training Session', 'mu-hr-training' ),
		'view_item'             => __( 'View Training Session', 'mu-hr-training' ),
		'all_items'             => __( 'All Training Sessions', 'mu-hr-training' ),
		'search_items'          => __( 'Search Training Sessions', 'mu-hr-training' ),
		'parent_item_colon'     => __( 'Parent Training Sessions:', 'mu-hr-training' ),
		'not_found'             => __( 'No Training Sessions found.', 'mu-hr-training' ),
		'not_found_in_trash'    => __( 'No Training Sessions found in Trash.', 'mu-hr-training' ),
		'featured_image'        => _x( 'Training Session Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'mu-hr-training' ),
		'set_featured_image'    => _x( 'Set hero image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'mu-hr-training' ),
		'remove_featured_image' => _x( 'Remove hero image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'mu-hr-training' ),
		'use_featured_image'    => _x( 'Use as hero image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'mu-hr-training' ),
		'archives'              => _x( 'Training Session archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'mu-hr-training' ),
		'insert_into_item'      => _x( 'Insert into Training Session', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'mu-hr-training' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this Training Session', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'mu-hr-training' ),
		'filter_items_list'     => _x( 'Filter Training Sessions list', 'Screen reader text for the filter Training Sessions heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'mu-hr-training' ),
		'items_list_navigation' => _x( 'Training Sessions list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'mu-hr-training' ),
		'items_list'            => _x( 'Training Sessions list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'mu-hr-training' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'query_var'           => true,
		'rewrite'             => array( 'slug' => '/session' ),
		'capability_type'     => 'post',
		'has_archive'         => true,
		'hierarchical'        => true,
		'supports'            => array( 'title', 'custom-fields', 'revisions' ),
		'show_in_rest'        => true,
		'exclude_from_search' => false,
		'menu_icon'           => 'dashicons-awards',
	);

	register_post_type( 'mu-session', $args );
}

/**
 * Register a custom post type called "mu-session.
 *
 * @see get_post_type_labels() for label keys.
 */
function mu_hr_training_registration_post_type() {
	$labels = array(
		'name'                  => _x( 'Training Registrations', 'Post type general name', 'mu-hr-training' ),
		'singular_name'         => _x( 'Training Registration', 'Post type singular name', 'mu-hr-training' ),
		'menu_name'             => _x( 'Training Registrations', 'Admin Menu text', 'mu-hr-training' ),
		'name_admin_bar'        => _x( 'Training Registration', 'Add New on Toolbar', 'mu-hr-training' ),
		'add_new'               => __( 'Add New', 'mu-hr-training' ),
		'add_new_item'          => __( 'Add New Training Registration', 'mu-hr-training' ),
		'new_item'              => __( 'New Training Registration', 'mu-hr-training' ),
		'edit_item'             => __( 'Edit Training Registration', 'mu-hr-training' ),
		'view_item'             => __( 'View Training Registration', 'mu-hr-training' ),
		'all_items'             => __( 'All Training Registrations', 'mu-hr-training' ),
		'search_items'          => __( 'Search Training Registrations', 'mu-hr-training' ),
		'parent_item_colon'     => __( 'Parent Training Registrations:', 'mu-hr-training' ),
		'not_found'             => __( 'No Training Registrations found.', 'mu-hr-training' ),
		'not_found_in_trash'    => __( 'No Training Registrations found in Trash.', 'mu-hr-training' ),
		'featured_image'        => _x( 'Training Registration Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'mu-hr-training' ),
		'set_featured_image'    => _x( 'Set hero image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'mu-hr-training' ),
		'remove_featured_image' => _x( 'Remove hero image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'mu-hr-training' ),
		'use_featured_image'    => _x( 'Use as hero image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'mu-hr-training' ),
		'archives'              => _x( 'Training Registration archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'mu-hr-training' ),
		'insert_into_item'      => _x( 'Insert into Training Registration', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'mu-hr-training' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this Training Registration', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'mu-hr-training' ),
		'filter_items_list'     => _x( 'Filter Training Registrations list', 'Screen reader text for the filter Training Registrations heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'mu-hr-training' ),
		'items_list_navigation' => _x( 'Training Registrations list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'mu-hr-training' ),
		'items_list'            => _x( 'Training Registrations list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'mu-hr-training' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'query_var'           => true,
		'rewrite'             => array( 'slug' => '/registration' ),
		'capability_type'     => 'post',
		'has_archive'         => true,
		'hierarchical'        => true,
		'supports'            => array( 'title', 'custom-fields', 'revisions' ),
		'show_in_rest'        => true,
		'exclude_from_search' => false,
		'menu_icon'           => 'dashicons-clipboard',
	);

	register_post_type( 'mu-registrations', $args );
}

/**
 * Add custom taxonomy for top level training names
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function mu_hr_training_custom_taxonomy() {
	$labels = array(
		'name'              => _x( 'Trainings', 'taxonomy general name', 'mu-hr-training' ),
		'singular_name'     => _x( 'Training', 'taxonomy singular name', 'mu-hr-training' ),
		'search_items'      => __( 'Search Trainings', 'mu-hr-training' ),
		'all_items'         => __( 'All Trainings', 'mu-hr-training' ),
		'parent_item'       => __( 'Parent Training', 'mu-hr-training' ),
		'parent_item_colon' => __( 'Parent Training:', 'mu-hr-training' ),
		'edit_item'         => __( 'Edit Training', 'mu-hr-training' ),
		'update_item'       => __( 'Update Training', 'mu-hr-training' ),
		'add_new_item'      => __( 'Add New Training', 'mu-hr-training' ),
		'new_item_name'     => __( 'New Training Name', 'mu-hr-training' ),
		'menu_name'         => __( 'All Trainings', 'mu-hr-training' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'hr-training' ),
	);

	register_taxonomy( 'mu-training', array( 'mu-session' ), $args );
}

/**
 * Flush rewrites whenever the plugin is activated.
 */
function mu_hr_training_activate() {
	flush_rewrite_rules( false );
}
register_activation_hook( __FILE__, 'mu_hr_training_activate' );

/**
 * Flush rewrites whenever the plugin is deactivated, also unregister 'employee' post type and 'department' taxonomy.
 */
function mu_hr_training_deactivate() {
	unregister_post_type( 'mu-session' );
	unregister_post_type( 'mu-registrations' );
	unregister_taxonomy( 'mu-training' );
	flush_rewrite_rules( false );
}
register_deactivation_hook( __FILE__, 'mu_hr_training_deactivate' );

/**
 * Proper way to enqueue scripts and styles
 */
function mu_hr_training_scripts() {
	wp_enqueue_style( 'mu-hr-training', plugin_dir_path( __FILE__ ) . 'css/mu-hr-training.css', '', true );
}
add_action( 'wp_enqueue_scripts', 'mu_hr_training_scripts' );

/**
 * Load Training Template
 *
 * @param string $template The filename of the template for the Training taxonomy.
 * @return string
 */
function hr_training_load_taxonomy_template( $template ) {
	global $post;

	if ( is_tax( 'mu-training' ) && locate_template( array( 'taxonomy-mu-training.php' ) ) !== $template ) {
		return plugin_dir_path( __FILE__ ) . 'templates/taxonomy-mu-training.php';
	}

	return $template;
}
add_filter( 'template_include', 'hr_training_load_taxonomy_template' );

/**
 * Change the default loop query for the Training taxonomy.
 *
 * @param object $query The WP_Query instance (passed by reference).
 */
function mu_hr_training_training_taxonomy_query( $query ) {
	if ( ! is_admin() && is_tax( 'mu-training' ) && $query->is_main_query() ) {
		$query->set( 'meta_key', 'mu_training_start_time' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'ASC' );
		$query->set(
			'meta_query',
			array(
				array(
					'key'     => 'mu_training_start_time',
					'value'   => date( 'Y-m-d H:i:s' ), // phpcs:ignore
					'type'    => 'DATETIME',
					'compare' => '>=',
				),
			)
		);
	}
}
add_action( 'pre_get_posts', 'mu_hr_training_training_taxonomy_query' );

/**
 * Add 'courseID' to the acceptable URL parameters
 *
 * @param array $vars The array of acceptable URL parameters.
 * @return array
 */
function mu_hr_training_query_parameter( $vars ) {
	$vars[] = 'courseID';
	return $vars;
}
add_filter( 'query_vars', 'mu_hr_training_query_parameter' );

/**
 * Remove ACF's default styling for forms.
 */
function mu_hr_training_acf_form_deregister_styles() {
	wp_deregister_style( 'acf-global' );
	wp_deregister_style( 'acf-input' );
	wp_register_style( 'acf-global', false );
	wp_register_style( 'acf-input', false );

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

	// send email
}
add_action( 'acf/save_post', 'mu_hr_registration_submitted_registration', 15 );

/**
 * Check if user is authenticated via CAS.
 * If the user is authenticated check to see if they are the instructor for the training.
 */
function mu_hr_registration_check_cas() {
	if ( is_page( 'registered-list' ) ) {

		if ( ! get_query_var( 'courseID' ) ) {
			return 'Sorry that course was not found.';
		} else {
			$training_session_id = get_query_var( 'courseID' );
		}

		require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

		if ( ! lockr_get_key( 'cas_host' ) ) {
			exit;
		}

		$cas_port    = 443;
		$cas_context = '/cas';

		if ( lockr_get_key( 'cas_port' ) ) {
			$cas_port = intval( lockr_get_key( 'cas_port' ) );
		}

		if ( lockr_get_key( 'cas_context' ) ) {
			$cas_context = lockr_get_key( 'cas_context' );
		}

		phpCAS::setVerbose( true );

		phpCAS::client( CAS_VERSION_3_0, lockr_get_key( 'cas_host' ), 443, '/cas' );
		phpCAS::setNoCasServerValidation();
		phpCAS::forceAuthentication();

		$cas_username      = phpCAS::getUser();
		$instructor        = get_field( 'mu_training_instructor', $training_session_id() );
		// $backup_instructor = get_field( 'mu_training_instructor', $training_session_id() )['backup_instructor_username'];

		// if ( $cas_username === $instructor || $cas_username === $backup_instructor ) {
		// 	die( 'hi! welcome to your class' );
		// } else {
		// 	die( 'you are not the instructor' );
		// }
	}
}
add_action( 'template_redirect', 'mu_hr_registration_check_cas' );

/**
 * Set header on registration lists so Pantheon doesn't cache the page.
 */
function mu_hr_training_no_cache_on_regisration_list_page() {
	$postid = get_queried_object_id();
	if ( is_page( 'registered-list' ) ) {
		header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
	}
}
add_action( 'template_redirect', 'mu_hr_training_no_cache_on_regisration_list_page' );

add_action( 'init', 'mu_hr_training_custom_taxonomy' );
add_action( 'init', 'mu_hr_training_session_post_type' );
add_action( 'init', 'mu_hr_training_registration_post_type' );
