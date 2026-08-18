<?php
/**
 * Plugin Name: 11AA Real Estate Analytics
 * Description: Real visitor tracking and analytics counter
 * Version: 1.0.0
 * Author: 11AA Real Estate
 * Text Domain: realestate-analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'REALESTATE_ANALYTICS_VERSION', '1.0.0' );
define( 'REALESTATE_ANALYTICS_PATH', plugin_dir_path( __FILE__ ) );
define( 'REALESTATE_ANALYTICS_URL', plugin_dir_url( __FILE__ ) );

require_once REALESTATE_ANALYTICS_PATH . 'includes/class-visitor-tracker.php';
require_once REALESTATE_ANALYTICS_PATH . 'includes/class-visitor-stats.php';
require_once REALESTATE_ANALYTICS_PATH . 'admin/class-analytics-admin.php';

register_activation_hook( __FILE__, 'realestate_analytics_activate' );
register_deactivation_hook( __FILE__, 'realestate_analytics_deactivate' );

function realestate_analytics_activate() {
    $tracker = new REALESTATE_Visitor_Tracker();
    $tracker->create_table();

    if ( ! get_option( 'realestate_happy_customers' ) ) {
        update_option( 'realestate_happy_customers', 0 );
    }
}

function realestate_analytics_deactivate() {
    wp_clear_scheduled_hook( 'realestate_analytics_cleanup' );
}

function realestate_analytics_init() {
    $tracker = new REALESTATE_Visitor_Tracker();
    $tracker->track_visit();

    $stats = new REALESTATE_Visitor_Stats();
    $stats->register_routes();
}
add_action( 'init', 'realestate_analytics_init' );

function realestate_analytics_enqueue_frontend() {
    wp_enqueue_script(
        'realestate-analytics-counter',
        REALESTATE_ANALYTICS_URL . 'assets/js/analytics-counter.js',
        array(),
        REALESTATE_ANALYTICS_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'realestate_analytics_enqueue_frontend' );

function realestate_analytics_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'show' => 'all',
        ),
        $atts
    );

    $api = new REALESTATE_Visitor_Stats();
    $stats = $api->get_stats();

    ob_start();
    ?>
    <div class="realestate-analytics-counter" data-show="<?php echo esc_attr( $atts['show'] ); ?>">
        <?php if ( in_array( $atts['show'], array( 'all', 'visitors' ), true ) ) : ?>
            <div class="analytics-stat-item">
                <span class="analytics-number" data-target="<?php echo esc_attr( $stats['total_visitors'] ); ?>">0</span>
                <span class="analytics-label">Total Visitors</span>
            </div>
        <?php endif; ?>
        <?php if ( in_array( $atts['show'], array( 'all', 'pageviews' ), true ) ) : ?>
            <div class="analytics-stat-item">
                <span class="analytics-number" data-target="<?php echo esc_attr( $stats['total_page_views'] ); ?>">0</span>
                <span class="analytics-label">Page Views</span>
            </div>
        <?php endif; ?>
        <?php if ( in_array( $atts['show'], array( 'all', 'properties' ), true ) ) : ?>
            <div class="analytics-stat-item">
                <span class="analytics-number" data-target="<?php echo esc_attr( $stats['properties_listed'] ); ?>">0</span>
                <span class="analytics-label">Properties Listed</span>
            </div>
        <?php endif; ?>
        <?php if ( in_array( $atts['show'], array( 'all', 'sold' ), true ) ) : ?>
            <div class="analytics-stat-item">
                <span class="analytics-number" data-target="<?php echo esc_attr( $stats['properties_sold'] ); ?>">0</span>
                <span class="analytics-label">Properties Sold</span>
            </div>
        <?php endif; ?>
        <?php if ( in_array( $atts['show'], array( 'all', 'customers' ), true ) ) : ?>
            <div class="analytics-stat-item">
                <span class="analytics-number" data-target="<?php echo esc_attr( $stats['happy_customers'] ); ?>">0</span>
                <span class="analytics-label">Happy Customers</span>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'realestate_stats', 'realestate_analytics_shortcode' );
