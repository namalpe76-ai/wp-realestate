<?php
/**
 * Plugin Name: 11AA Real Estate DateTime
 * Description: Live date and time display for Sri Lanka timezone
 * Version: 1.0.0
 * Author: 11AA Real Estate
 * Text Domain: realestate-datetime
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'REALESTATE_DATETIME_VERSION', '1.0.0' );
define( 'REALESTATE_DATETIME_PATH', plugin_dir_path( __FILE__ ) );
define( 'REALESTATE_DATETIME_URL', plugin_dir_url( __FILE__ ) );

require_once REALESTATE_DATETIME_PATH . 'includes/class-datetime-display.php';

function realestate_datetime_init() {
    $display = new REALESTATE_DateTime_Display();
    $display->init();
}
add_action( 'init', 'realestate_datetime_init' );
