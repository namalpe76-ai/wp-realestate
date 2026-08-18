<?php
/**
 * Plugin Name: 11AA Real Estate Weather
 * Description: Weather widget with OpenWeatherMap API integration
 * Version: 1.0.0
 * Author: 11AA Real Estate
 * Text Domain: realestate-weather
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'REALESTATE_WEATHER_VERSION', '1.0.0' );
define( 'REALESTATE_WEATHER_PATH', plugin_dir_path( __FILE__ ) );
define( 'REALESTATE_WEATHER_URL', plugin_dir_url( __FILE__ ) );

require_once REALESTATE_WEATHER_PATH . 'includes/class-weather-api.php';
require_once REALESTATE_WEATHER_PATH . 'includes/class-weather-widget.php';

register_activation_hook( __FILE__, 'realestate_weather_activate' );

function realestate_weather_activate() {
    $defaults = array(
        'realestate_weather_api_key'     => '',
        'realestate_weather_location'    => 'Colombo,LK',
        'realestate_weather_unit'        => 'metric',
        'realestate_weather_cache_time'  => 1800,
    );

    foreach ( $defaults as $key => $value ) {
        if ( false === get_option( $key ) ) {
            add_option( $key, $value );
        }
    }
}

function realestate_weather_init() {
    $widget = new REALESTATE_Weather_Widget();
    $widget->register_routes();
}
add_action( 'init', 'realestate_weather_init' );

function realestate_weather_enqueue_shortcode_assets() {
    wp_enqueue_style(
        'realestate-weather-widget',
        REALESTATE_WEATHER_URL . 'assets/css/weather-widget.css',
        array(),
        REALESTATE_WEATHER_VERSION
    );

    wp_enqueue_script(
        'realestate-weather-widget',
        REALESTATE_WEATHER_URL . 'assets/js/weather-widget.js',
        array( 'jquery' ),
        REALESTATE_WEATHER_VERSION,
        true
    );

    wp_localize_script(
        'realestate-weather-widget',
        'realestateWeather',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'realestate_weather_nonce' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'realestate_weather_enqueue_shortcode_assets' );
