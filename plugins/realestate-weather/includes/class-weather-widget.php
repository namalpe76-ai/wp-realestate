<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class REALESTATE_Weather_Widget {

    private $api;

    public function __construct() {
        $this->api = new REALESTATE_Weather_API();

        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'wp_ajax_realestate_weather_refresh', array( $this, 'ajax_refresh_weather' ) );
    }

    public function register_routes() {
        add_shortcode( 'weather_widget', array( $this, 'render_shortcode' ) );
    }

    public function render_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'location' => '',
                'unit'     => '',
            ),
            $atts
        );

        wp_enqueue_style( 'realestate-weather-widget' );
        wp_enqueue_script( 'realestate-weather-widget' );

        $weather = $this->api->get_weather();

        ob_start();
        ?>
        <div class="realestate-weather-widget" id="realestate-weather-widget">
            <?php if ( is_wp_error( $weather ) ) : ?>
                <div class="weather-error-state">
                    <div class="weather-error-icon">&#9888;</div>
                    <p class="weather-error-message"><?php echo esc_html( $weather->get_error_message() ); ?></p>
                    <button class="weather-refresh-btn" data-action="refresh">&#8635; <?php esc_html_e( 'Retry', 'realestate-weather' ); ?></button>
                </div>
            <?php else : ?>
                <div class="weather-card">
                    <div class="weather-header">
                        <div class="weather-location">
                            <span class="location-name"><?php echo esc_html( $weather['location_name'] ); ?></span>
                            <?php if ( ! empty( $weather['country'] ) ) : ?>
                                <span class="location-country"><?php echo esc_html( $weather['country'] ); ?></span>
                            <?php endif; ?>
                        </div>
                        <button class="weather-refresh-btn" data-action="refresh" title="<?php esc_attr_e( 'Refresh weather', 'realestate-weather' ); ?>">&#8635;</button>
                    </div>

                    <div class="weather-main">
                        <div class="weather-icon-temp">
                            <img class="weather-icon" src="<?php echo esc_url( $weather['icon_url'] ); ?>" alt="<?php echo esc_attr( $weather['condition'] ); ?>" width="80" height="80" />
                            <div class="weather-temperature">
                                <span class="temp-value"><?php echo esc_html( $weather['temperature'] ); ?></span>
                                <span class="temp-unit">&deg;<?php echo esc_html( $weather['unit_suffix'] ); ?></span>
                            </div>
                        </div>
                        <div class="weather-condition"><?php echo esc_html( $weather['condition'] ); ?></div>
                    </div>

                    <div class="weather-details">
                        <div class="weather-detail-item">
                            <span class="detail-icon">&#128167;</span>
                            <span class="detail-label"><?php esc_html_e( 'Humidity', 'realestate-weather' ); ?></span>
                            <span class="detail-value"><?php echo esc_html( $weather['humidity'] ); ?>%</span>
                        </div>
                        <div class="weather-detail-item">
                            <span class="detail-icon">&#127788;</span>
                            <span class="detail-label"><?php esc_html_e( 'Wind', 'realestate-weather' ); ?></span>
                            <span class="detail-value"><?php echo esc_html( $weather['wind_speed'] ); ?> <?php echo esc_html( $weather['wind_unit'] ); ?></span>
                        </div>
                        <?php if ( ! empty( $weather['feels_like'] ) ) : ?>
                            <div class="weather-detail-item">
                                <span class="detail-icon">&#128525;</span>
                                <span class="detail-label"><?php esc_html_e( 'Feels Like', 'realestate-weather' ); ?></span>
                                <span class="detail-value"><?php echo esc_html( $weather['feels_like'] ); ?>&deg;<?php echo esc_html( $weather['unit_suffix'] ); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="weather-footer">
                        <span class="weather-updated">
                            <?php
                            printf(
                                esc_html__( 'Updated: %s', 'realestate-weather' ),
                                esc_html( human_time_diff( $weather['last_timestamp'] ?? $weather['last_updated_timestamp'], current_time( 'timestamp' ) ) . ' ago' )
                            );
                            ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function add_settings_page() {
        add_options_page(
            __( 'Weather Settings', 'realestate-weather' ),
            __( 'Weather Settings', 'realestate-weather' ),
            'manage_options',
            'realestate-weather-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'realestate_weather_options', 'realestate_weather_api_key', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        register_setting( 'realestate_weather_options', 'realestate_weather_location', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'Colombo,LK',
        ) );

        register_setting( 'realestate_weather_options', 'realestate_weather_unit', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'metric',
        ) );

        register_setting( 'realestate_weather_options', 'realestate_weather_cache_time', array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 1800,
        ) );
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Weather Settings', 'realestate-weather' ); ?></h1>

            <form method="post" action="options.php" class="weather-settings-form">
                <?php settings_fields( 'realestate_weather_options' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="realestate_weather_api_key"><?php esc_html_e( 'OpenWeatherMap API Key', 'realestate-weather' ); ?></label>
                        </th>
                        <td>
                            <input type="password"
                                id="realestate_weather_api_key"
                                name="realestate_weather_api_key"
                                value="<?php echo esc_attr( get_option( 'realestate_weather_api_key', '' ) ); ?>"
                                class="regular-text"
                                placeholder="<?php esc_attr_e( 'Enter your API key', 'realestate-weather' ); ?>" />
                            <p class="description">
                                <?php
                                printf(
                                    esc_html__( 'Get a free API key from %sOpenWeatherMap%s.', 'realestate-weather' ),
                                    '<a href="https://openweathermap.org/api" target="_blank" rel="noopener noreferrer">',
                                    '</a>'
                                );
                                ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="realestate_weather_location"><?php esc_html_e( 'Location', 'realestate-weather' ); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                id="realestate_weather_location"
                                name="realestate_weather_location"
                                value="<?php echo esc_attr( get_option( 'realestate_weather_location', 'Colombo,LK' ) ); ?>"
                                class="regular-text"
                                placeholder="City,CountryCode" />
                            <p class="description"><?php esc_html_e( 'Format: City,CountryCode (e.g., Colombo,LK)', 'realestate-weather' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="realestate_weather_unit"><?php esc_html_e( 'Temperature Unit', 'realestate-weather' ); ?></label>
                        </th>
                        <td>
                            <select id="realestate_weather_unit" name="realestate_weather_unit">
                                <option value="metric" <?php selected( get_option( 'realestate_weather_unit', 'metric' ), 'metric' ); ?>><?php esc_html_e( 'Celsius (°C)', 'realestate-weather' ); ?></option>
                                <option value="imperial" <?php selected( get_option( 'realestate_weather_unit', 'metric' ), 'imperial' ); ?>><?php esc_html_e( 'Fahrenheit (°F)', 'realestate-weather' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="realestate_weather_cache_time"><?php esc_html_e( 'Cache Duration (seconds)', 'realestate-weather' ); ?></label>
                        </th>
                        <td>
                            <input type="number"
                                id="realestate_weather_cache_time"
                                name="realestate_weather_cache_time"
                                value="<?php echo esc_attr( get_option( 'realestate_weather_cache_time', 1800 ) ); ?>"
                                min="300"
                                max="86400"
                                step="300" />
                            <p class="description"><?php esc_html_e( 'Default: 1800 (30 minutes). Min: 300, Max: 86400.', 'realestate-weather' ); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button( __( 'Save Settings', 'realestate-weather' ) ); ?>
            </form>

            <hr />

            <h2><?php esc_html_e( 'Test Connection', 'realestate-weather' ); ?></h2>
            <p><?php esc_html_e( 'Test your API connection to verify settings are correct.', 'realestate-weather' ); ?></p>
            <button type="button" class="button button-secondary" id="weather-test-btn">
                <?php esc_html_e( 'Test Connection', 'realestate-weather' ); ?>
            </button>
            <span id="weather-test-result"></span>

            <script>
            jQuery(document).ready(function($) {
                $('#weather-test-btn').on('click', function() {
                    var btn = $(this);
                    var result = $('#weather-test-result');
                    btn.prop('disabled', true).text('<?php echo esc_js( __( 'Testing...', 'realestate-weather' ) ); ?>');
                    result.text('');

                    var data = {
                        action: 'realestate_weather_refresh',
                        nonce: '<?php echo esc_js( wp_create_nonce( 'realestate_weather_nonce' ) ); ?>',
                        test: 1
                    };

                    $.post(ajaxurl, data, function(response) {
                        btn.prop('disabled', false).text('<?php echo esc_js( __( 'Test Connection', 'realestate-weather' ) ); ?>');
                        if (response.success) {
                            result.html('<span style="color: #00a32a;">&#10004; <?php echo esc_js( __( 'Connection successful!', 'realestate-weather' ) ); ?></span> ' + response.data.location_name + ' ' + response.data.temperature + '&deg;');
                        } else {
                            result.html('<span style="color: #d63638;">&#10008; ' + response.data + '</span>');
                        }
                    }).fail(function() {
                        btn.prop('disabled', false).text('<?php echo esc_js( __( 'Test Connection', 'realestate-weather' ) ); ?>');
                        result.html('<span style="color: #d63638;">&#10008; <?php echo esc_js( __( 'Request failed.', 'realestate-weather' ) ); ?></span>');
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    public function ajax_refresh_weather() {
        if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'realestate_weather_nonce' ) ) {
            wp_send_json_error( __( 'Invalid nonce.', 'realestate-weather' ) );
        }

        delete_transient( 'realestate_weather_' . md5(
            get_option( 'realestate_weather_location', 'Colombo,LK' ) . get_option( 'realestate_weather_unit', 'metric' )
        ) );

        $weather = $this->api->get_weather();

        if ( is_wp_error( $weather ) ) {
            wp_send_json_error( $weather->get_error_message() );
        }

        wp_send_json_success( $weather );
    }
}
