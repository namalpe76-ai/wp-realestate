<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class REALESTATE_Analytics_Admin {

    private $stats;
    private $tracker;

    public function __construct() {
        $this->stats   = new REALESTATE_Visitor_Stats();
        $this->tracker = new REALESTATE_Visitor_Tracker();

        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_ajax_realestate_update_customers', array( $this, 'ajax_update_customers' ) );
    }

    public function add_admin_menu() {
        add_dashboard_page(
            __( 'Analytics', 'realestate-analytics' ),
            __( 'Analytics', 'realestate-analytics' ),
            'manage_realestate_analytics',
            'realestate-analytics',
            array( $this, 'render_dashboard' )
        );
    }

    public function enqueue_admin_assets( $hook ) {
        if ( 'dashboard_page_realestate-analytics' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'realestate-analytics-admin',
            REALESTATE_ANALYTICS_URL . 'assets/css/analytics-admin.css',
            array(),
            REALESTATE_ANALYTICS_VERSION
        );

        wp_localize_script(
            'jquery',
            'realestateAnalytics',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'realestate_analytics_nonce' ),
            )
        );
    }

    public function render_dashboard() {
        if ( ! current_user_can( 'manage_realestate_analytics' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'realestate-analytics' ) );
        }

        $stats = $this->stats->get_stats();
        ?>
        <div class="wrap realestate-analytics-wrap">
            <h1><?php esc_html_e( '11AA Real Estate Analytics', 'realestate-analytics' ); ?></h1>

            <div class="analytics-cards-grid">
                <div class="analytics-card card-visitors">
                    <div class="card-icon">&#128100;</div>
                    <div class="card-content">
                        <h3><?php esc_html_e( 'Total Unique Visitors', 'realestate-analytics' ); ?></h3>
                        <p class="card-number"><?php echo esc_html( number_format_i18n( $stats['total_visitors'] ) ); ?></p>
                    </div>
                </div>

                <div class="analytics-card card-pageviews">
                    <div class="card-icon">&#128196;</div>
                    <div class="card-content">
                        <h3><?php esc_html_e( 'Total Page Views', 'realestate-analytics' ); ?></h3>
                        <p class="card-number"><?php echo esc_html( number_format_i18n( $stats['total_page_views'] ) ); ?></p>
                    </div>
                </div>

                <div class="analytics-card card-today">
                    <div class="card-icon">&#128197;</div>
                    <div class="card-content">
                        <h3><?php esc_html_e( 'Today\'s Visitors', 'realestate-analytics' ); ?></h3>
                        <p class="card-number"><?php echo esc_html( number_format_i18n( $stats['today_visitors'] ) ); ?></p>
                    </div>
                </div>

                <div class="analytics-card card-month">
                    <div class="card-icon">&#128200;</div>
                    <div class="card-content">
                        <h3><?php esc_html_e( 'This Month', 'realestate-analytics' ); ?></h3>
                        <p class="card-number"><?php echo esc_html( number_format_i18n( $stats['month_visitors'] ) ); ?></p>
                    </div>
                </div>

                <div class="analytics-card card-properties">
                    <div class="card-icon">&#127968;</div>
                    <div class="card-content">
                        <h3><?php esc_html_e( 'Properties Listed', 'realestate-analytics' ); ?></h3>
                        <p class="card-number"><?php echo esc_html( number_format_i18n( $stats['properties_listed'] ) ); ?></p>
                    </div>
                </div>

                <div class="analytics-card card-sold">
                    <div class="card-icon">&#10004;</div>
                    <div class="card-content">
                        <h3><?php esc_html_e( 'Properties Sold', 'realestate-analytics' ); ?></h3>
                        <p class="card-number"><?php echo esc_html( number_format_i18n( $stats['properties_sold'] ) ); ?></p>
                    </div>
                </div>

                <div class="analytics-card card-customers">
                    <div class="card-icon">&#128522;</div>
                    <div class="card-content">
                        <h3><?php esc_html_e( 'Happy Customers', 'realestate-analytics' ); ?></h3>
                        <p class="card-number"><?php echo esc_html( number_format_i18n( $stats['happy_customers'] ) ); ?></p>
                        <form id="update-customers-form" class="card-form">
                            <input type="number" name="happy_customers" value="<?php echo esc_attr( $stats['happy_customers'] ); ?>" min="0" />
                            <?php wp_nonce_field( 'realestate_analytics_nonce', 'analytics_nonce' ); ?>
                            <button type="submit" class="button button-small"><?php esc_html_e( 'Update', 'realestate-analytics' ); ?></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="analytics-chart-section">
                <h2><?php esc_html_e( 'Last 7 Days Visitors', 'realestate-analytics' ); ?></h2>
                <div class="analytics-bar-chart">
                    <?php
                    $daily_data = $stats['last_7_days'];
                    $max_count = max( array_column( $daily_data, 'count' ) );
                    if ( $max_count === 0 ) {
                        $max_count = 1;
                    }
                    ?>
                    <div class="chart-bars">
                        <?php foreach ( $daily_data as $day ) : ?>
                            <div class="chart-bar-wrapper">
                                <div class="chart-bar-value"><?php echo esc_html( $day['count'] ); ?></div>
                                <div class="chart-bar" style="height: <?php echo esc_attr( ( $day['count'] / $max_count ) * 100 ); ?>%;"></div>
                                <div class="chart-bar-label"><?php echo esc_html( $day['date'] ); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="analytics-shortcode-info">
                <h2><?php esc_html_e( 'Shortcode Usage', 'realestate-analytics' ); ?></h2>
                <p><?php esc_html_e( 'Use the following shortcodes to display analytics counters on the front end:', 'realestate-analytics' ); ?></p>
                <code>[realestate_stats show="all"]</code><br/>
                <code>[realestate_stats show="visitors"]</code> |
                <code>[realestate_stats show="pageviews"]</code> |
                <code>[realestate_stats show="properties"]</code> |
                <code>[realestate_stats show="sold"]</code> |
                <code>[realestate_stats show="customers"]</code>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#update-customers-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var count = form.find('input[name="happy_customers"]').val();

                $.post(realestateAnalytics.ajax_url, {
                    action: 'realestate_update_customers',
                    nonce: form.find('input[name="analytics_nonce"]').val(),
                    happy_customers: count
                }, function(response) {
                    if (response.success) {
                        form.find('input[name="happy_customers"]').val(response.data.count);
                        form.closest('.card-content').find('.card-number').text(response.data.count);
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function ajax_update_customers() {
        if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'realestate_analytics_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        if ( ! current_user_can( 'manage_realestate_analytics' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $count = isset( $_POST['happy_customers'] ) ? absint( $_POST['happy_customers'] ) : 0;
        update_option( 'realestate_happy_customers', $count );

        delete_transient( 'realestate_analytics_stats' );

        wp_send_json_success( array( 'count' => $count ) );
    }
}

new REALESTATE_Analytics_Admin();
