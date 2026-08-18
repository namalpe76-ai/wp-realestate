<?php
/**
 * Plugin Name: 11AA Real Estate Submit Property
 * Description: Public property submission form for property owners
 * Version: 1.0.0
 * Author: 11AA Real Estate
 * Text Domain: realestate-submit-property
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RESP_VERSION', '1.0.0' );
define( 'RESP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RESP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RESP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once RESP_PLUGIN_DIR . 'includes/class-submit-form.php';
require_once RESP_PLUGIN_DIR . 'includes/class-submission-handler.php';
require_once RESP_PLUGIN_DIR . 'includes/class-submission-email.php';
require_once RESP_PLUGIN_DIR . 'admin/class-submission-admin.php';

register_activation_hook( __FILE__, 'resp_activate' );
register_deactivation_hook( __FILE__, 'resp_deactivate' );

function resp_activate() {
	$handler = new RESP_Submission_Handler();
	$handler->register_post_type();
	flush_rewrite_rules();
}

function resp_deactivate() {
	flush_rewrite_rules();
}

function resp_init() {
	new RESP_Submit_Form();
	new RESP_Submission_Handler();
	new RESP_Submission_Email();
	if ( is_admin() ) {
		new RESP_Submission_Admin();
	}
}
add_action( 'init', 'resp_init' );

function resp_enqueue_scripts() {
	wp_enqueue_style( 'resp-submit-form', RESP_PLUGIN_URL . 'assets/css/submit-form.css', array(), RESP_VERSION );
	wp_enqueue_script( 'resp-submit-form', RESP_PLUGIN_URL . 'assets/js/submit-form.js', array( 'jquery' ), RESP_VERSION, true );
	wp_localize_script( 'resp-submit-form', 'respData', array(
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'resp_submit_nonce' ),
		'maxFiles' => 10,
		'maxSize'  => 5 * 1024 * 1024,
		'maxTotal' => 50 * 1024 * 1024,
	) );
}
add_action( 'wp_enqueue_scripts', 'resp_enqueue_scripts' );
