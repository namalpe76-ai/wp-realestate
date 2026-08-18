<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class REALESTATE_Visitor_Stats {

    private $tracker;

    public function __construct() {
        $this->tracker = new REALESTATE_Visitor_Tracker();
    }

    public function register_routes() {
        add_action( 'rest_api_init', array( $this, 'register_routes_handler' ) );
    }

    public function register_routes_handler() {
        register_rest_route(
            'realestate-analytics/v1',
            '/stats',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_stats_endpoint' ),
                'permission_callback' => '__return_true',
            )
        );
    }

    public function get_stats_endpoint( $request ) {
        $stats = $this->get_stats();

        return rest_ensure_response( $stats );
    }

    public function get_stats() {
        $cached = get_transient( 'realestate_analytics_stats' );
        if ( false !== $cached ) {
            return $cached;
        }

        $properties_listed = $this->get_properties_count();
        $properties_sold   = $this->get_properties_sold();
        $happy_customers   = (int) get_option( 'realestate_happy_customers', 0 );

        $stats = array(
            'total_visitors'    => $this->tracker->get_total_unique_visitors(),
            'total_page_views'  => $this->tracker->get_total_page_views(),
            'today_visitors'    => $this->tracker->get_today_visitors(),
            'month_visitors'    => $this->tracker->get_month_visitors(),
            'properties_listed' => $properties_listed,
            'properties_sold'   => $properties_sold,
            'happy_customers'   => $happy_customers,
            'last_7_days'       => $this->tracker->get_last_7_days_visits(),
        );

        set_transient( 'realestate_analytics_stats', $stats, 5 * MINUTE_IN_SECONDS );

        return $stats;
    }

    private function get_properties_count() {
        $post_types = array( 'property', 'real_estate', 'post', 'page' );

        $count = 0;
        foreach ( $post_types as $pt ) {
            $post_type_obj = get_post_type_object( $pt );
            if ( $post_type_obj && $post_type_obj->public ) {
                $count += (int) wp_count_posts( $pt )->publish;
            }
        }

        if ( $count === 0 ) {
            $count = (int) wp_count_posts()->publish;
        }

        return $count;
    }

    private function get_properties_sold() {
        global $wpdb;

        $post_types = array( 'property', 'real_estate' );
        $count      = 0;

        foreach ( $post_types as $pt ) {
            $sold = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->posts} p
                    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                    WHERE p.post_type = %s AND p.post_status = 'publish'
                    AND pm.meta_key = 'property_status' AND pm.meta_value = 'sold'",
                    $pt
                )
            );
            $count += (int) $sold;
        }

        return $count;
    }
}
