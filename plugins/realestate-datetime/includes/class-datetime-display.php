<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class REALESTATE_DateTime_Display {

    public function init() {
        add_shortcode( 'datetime_display', array( $this, 'render_shortcode' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'realestate-datetime-widget',
            REALESTATE_DATETIME_URL . 'assets/css/datetime-widget.css',
            array(),
            REALESTATE_DATETIME_VERSION
        );

        wp_enqueue_script(
            'realestate-datetime-clock',
            REALESTATE_DATETIME_URL . 'assets/js/datetime-clock.js',
            array(),
            REALESTATE_DATETIME_VERSION,
            true
        );
    }

    public function render_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'timezone'  => get_option( 'realestate_datetime_timezone', 'Asia/Colombo' ),
                'date_fmt'  => get_option( 'realestate_datetime_date_format', 'full' ),
                'time_fmt'  => get_option( 'realestate_datetime_time_format', '12h' ),
                'variant'   => 'default',
            ),
            $atts
        );

        $timezone = sanitize_text_field( $atts['timezone'] );
        $date_fmt = sanitize_text_field( $atts['date_fmt'] );
        $time_fmt = sanitize_text_field( $atts['time_fmt'] );
        $variant  = sanitize_text_field( $atts['variant'] );

        $server_time = $this->get_server_time( $timezone, $date_fmt, $time_fmt );

        ob_start();
        ?>
        <div class="realestate-datetime-widget <?php echo esc_attr( 'variant-' . $variant ); ?>"
             data-timezone="<?php echo esc_attr( $timezone ); ?>"
             data-date-format="<?php echo esc_attr( $date_fmt ); ?>"
             data-time-format="<?php echo esc_attr( $time_fmt ); ?>">
            <div class="datetime-display">
                <div class="datetime-day" id="datetime-day"><?php echo esc_html( $server_time['day'] ); ?></div>
                <div class="datetime-date" id="datetime-date"><?php echo esc_html( $server_time['date'] ); ?></div>
                <div class="datetime-time" id="datetime-time"><?php echo esc_html( $server_time['time'] ); ?></div>
            </div>
            <noscript>
                <div class="datetime-noscript">
                    <?php echo esc_html( $server_time['full'] ); ?>
                </div>
            </noscript>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_server_time( $timezone, $date_fmt, $time_fmt ) {
        try {
            $tz  = new DateTimeZone( $timezone );
        } catch ( Exception $e ) {
            $tz = new DateTimeZone( 'Asia/Colombo' );
        }

        $now = new DateTime( 'now', $tz );

        $day_format = 'l';
        $date_format_map = array(
            'full'  => 'd F Y',
            'short' => 'M d, Y',
            'iso'   => 'Y-m-d',
        );
        $date_format = isset( $date_format_map[ $date_fmt ] ) ? $date_format_map[ $date_fmt ] : 'd F Y';

        if ( $time_fmt === '24h' ) {
            $time_format = 'H:i:s';
        } else {
            $time_format = 'h:i:s A';
        }

        return array(
            'day'  => $now->format( $day_format ),
            'date' => $now->format( $date_format ),
            'time' => $now->format( $time_format ),
            'full' => $now->format( $day_format . ', ' . $date_format . ' | ' . $time_format ),
        );
    }

    public function add_settings_page() {
        add_options_page(
            __( 'DateTime Settings', 'realestate-datetime' ),
            __( 'DateTime Settings', 'realestate-datetime' ),
            'manage_options',
            'realestate-datetime-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'realestate_datetime_options', 'realestate_datetime_timezone', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'Asia/Colombo',
        ) );

        register_setting( 'realestate_datetime_options', 'realestate_datetime_date_format', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'full',
        ) );

        register_setting( 'realestate_datetime_options', 'realestate_datetime_time_format', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '12h',
        ) );
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $timezones = array(
            'Asia/Colombo'    => 'Sri Lanka (Asia/Colombo)',
            'Asia/Kolkata'    => 'India (Asia/Kolkata)',
            'Asia/Dubai'      => 'UAE (Asia/Dubai)',
            'Asia/Singapore'  => 'Singapore (Asia/Singapore)',
            'Asia/Tokyo'      => 'Japan (Asia/Tokyo)',
            'Asia/Shanghai'   => 'China (Asia/Shanghai)',
            'Asia/Kathmandu'  => 'Nepal (Asia/Kathmandu)',
            'Asia/Dhaka'      => 'Bangladesh (Asia/Dhaka)',
            'Europe/London'   => 'UK (Europe/London)',
            'Europe/Paris'    => 'France (Europe/Paris)',
            'Europe/Berlin'   => 'Germany (Europe/Berlin)',
            'America/New_York' => 'US Eastern (America/New_York)',
            'America/Chicago'  => 'US Central (America/Chicago)',
            'America/Denver'   => 'US Mountain (America/Denver)',
            'America/Los_Angeles' => 'US Pacific (America/Los_Angeles)',
            'Australia/Sydney'    => 'Australia (Australia/Sydney)',
            'Pacific/Auckland'    => 'New Zealand (Pacific/Auckland)',
            'UTC'                 => 'UTC',
        );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'DateTime Settings', 'realestate-datetime' ); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields( 'realestate_datetime_options' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="realestate_datetime_timezone"><?php esc_html_e( 'Timezone', 'realestate-datetime' ); ?></label>
                        </th>
                        <td>
                            <select id="realestate_datetime_timezone" name="realestate_datetime_timezone">
                                <?php foreach ( $timezones as $value => $label ) : ?>
                                    <option value="<?php echo esc_attr( $value ); ?>"
                                        <?php selected( get_option( 'realestate_datetime_timezone', 'Asia/Colombo' ), $value ); ?>>
                                        <?php echo esc_html( $label ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Select the timezone for the date/time display.', 'realestate-datetime' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="realestate_datetime_date_format"><?php esc_html_e( 'Date Format', 'realestate-datetime' ); ?></label>
                        </th>
                        <td>
                            <select id="realestate_datetime_date_format" name="realestate_datetime_date_format">
                                <option value="full" <?php selected( get_option( 'realestate_datetime_date_format', 'full' ), 'full' ); ?>><?php esc_html_e( 'Full - 18 August 2026', 'realestate-datetime' ); ?></option>
                                <option value="short" <?php selected( get_option( 'realestate_datetime_date_format', 'full' ), 'short' ); ?>><?php esc_html_e( 'Short - Aug 18, 2026', 'realestate-datetime' ); ?></option>
                                <option value="iso" <?php selected( get_option( 'realestate_datetime_date_format', 'full' ), 'iso' ); ?>><?php esc_html_e( 'ISO - 2026-08-18', 'realestate-datetime' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="realestate_datetime_time_format"><?php esc_html_e( 'Time Format', 'realestate-datetime' ); ?></label>
                        </th>
                        <td>
                            <select id="realestate_datetime_time_format" name="realestate_datetime_time_format">
                                <option value="12h" <?php selected( get_option( 'realestate_datetime_time_format', '12h' ), '12h' ); ?>><?php esc_html_e( '12-hour (10:50 PM)', 'realestate-datetime' ); ?></option>
                                <option value="24h" <?php selected( get_option( 'realestate_datetime_time_format', '12h' ), '24h' ); ?>><?php esc_html_e( '24-hour (22:50)', 'realestate-datetime' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php submit_button( __( 'Save Settings', 'realestate-datetime' ) ); ?>
            </form>

            <hr />

            <h2><?php esc_html_e( 'Preview', 'realestate-datetime' ); ?></h2>
            <p><?php esc_html_e( 'Here is how the datetime widget will appear:', 'realestate-datetime' ); ?></p>

            <div style="background: #1a1a2e; padding: 30px; border-radius: 12px; display: inline-block;">
                <?php echo do_shortcode( '[datetime_display]' ); ?>
            </div>

            <h3><?php esc_html_e( 'Shortcode Usage', 'realestate-datetime' ); ?></h3>
            <p><?php esc_html_e( 'Use the following shortcode in Elementor or any page builder:', 'realestate-datetime' ); ?></p>
            <code>[datetime_display]</code><br/><br/>
            <code>[datetime_display timezone="Asia/Colombo" time_fmt="12h" variant="dark"]</code>
        </div>
        <?php
    }
}
