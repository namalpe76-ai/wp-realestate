<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class REALESTATE_Weather_API {

    private $api_key;
    private $location;
    private $unit;
    private $cache_time;
    private $transient_key;

    public function __construct() {
        $this->api_key    = get_option( 'realestate_weather_api_key', '' );
        $this->location   = get_option( 'realestate_weather_location', 'Colombo,LK' );
        $this->unit       = get_option( 'realestate_weather_unit', 'metric' );
        $this->cache_time = (int) get_option( 'realestate_weather_cache_time', 1800 );

        $this->transient_key = 'realestate_weather_' . md5( $this->location . $this->unit );
    }

    public function get_weather() {
        $cached = get_transient( $this->transient_key );
        if ( false !== $cached ) {
            return $cached;
        }

        $weather_data = $this->fetch_weather();
        if ( is_wp_error( $weather_data ) ) {
            $fallback = get_transient( $this->transient_key . '_fallback' );
            if ( false !== $fallback ) {
                return $fallback;
            }

            return $weather_data;
        }

        set_transient( $this->transient_key, $weather_data, $this->cache_time );
        set_transient( $this->transient_key . '_fallback', $weather_data, $this->cache_time * 3 );

        return $weather_data;
    }

    private function fetch_weather() {
        if ( empty( $this->api_key ) ) {
            return new WP_Error(
                'missing_api_key',
                __( 'Please set your OpenWeatherMap API key in Weather Settings.', 'realestate-weather' )
            );
        }

        $unit_param = $this->unit === 'imperial' ? 'imperial' : 'metric';
        $url        = sprintf(
            'https://api.openweathermap.org/data/2.5/weather?q=%s&units=%s&appid=%s',
            rawurlencode( $this->location ),
            $unit_param,
            $this->api_key
        );

        $response = wp_remote_get( $url, array(
            'timeout' => 15,
        ) );

        if ( is_wp_error( $response ) ) {
            return new WP_Error(
                'network_error',
                __( 'Weather service is temporarily unavailable. Please try again later.', 'realestate-weather' )
            );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( $code !== 200 ) {
            $message = isset( $data['message'] ) ? $data['message'] : __( 'Unknown error', 'realestate-weather' );

            if ( $code === 401 ) {
                return new WP_Error(
                    'invalid_key',
                    __( 'Invalid API key. Please check your Weather Settings.', 'realestate-weather' )
                );
            }

            if ( $code === 404 ) {
                return new WP_Error(
                    'location_not_found',
                    __( 'Location not found. Please check your Weather Settings location.', 'realestate-weather' )
                );
            }

            return new WP_Error( 'api_error', $message );
        }

        if ( empty( $data['main'] ) || empty( $data['weather'] ) ) {
            return new WP_Error(
                'incomplete_data',
                __( 'Weather information is incomplete. Please try again later.', 'realestate-weather' )
            );
        }

        $unit_suffix = $this->unit === 'imperial' ? 'F' : 'C';

        return array(
            'temperature'    => round( $data['main']['temp'] ),
            'feels_like'     => round( $data['main']['feels_like'] ),
            'condition'      => isset( $data['weather'][0]['description'] ) ? ucfirst( $data['weather'][0]['description'] ) : '',
            'condition_main'  => isset( $data['weather'][0]['main'] ) ? $data['weather'][0]['main'] : '',
            'icon_url'       => $this->get_icon_url( $data['weather'][0]['icon'] ?? '01d' ),
            'humidity'       => $data['main']['humidity'],
            'wind_speed'     => isset( $data['wind']['speed'] ) ? $data['wind']['speed'] : 0,
            'wind_unit'      => $this->unit === 'imperial' ? 'mph' : 'm/s',
            'location_name'  => isset( $data['name'] ) ? $data['name'] : $this->location,
            'country'        => isset( $data['sys']['country'] ) ? $data['sys']['country'] : '',
            'unit_suffix'    => $unit_suffix,
            'last_updated'   => current_time( 'mysql' ),
            'last_updated_timestamp' => current_time( 'timestamp' ),
            'visibility'     => isset( $data['visibility'] ) ? round( $data['visibility'] / 1000, 1 ) : 0,
            'sunrise'        => isset( $data['sys']['sunrise'] ) ? date( 'H:i', $data['sys']['sunrise'] ) : '',
            'sunset'         => isset( $data['sys']['sunset'] ) ? date( 'H:i', $data['sys']['sunset'] ) : '',
        );
    }

    private function get_icon_url( $icon_code ) {
        return sprintf( 'https://openweathermap.org/img/wn/%s@2x.png', $icon_code );
    }

    public function test_connection() {
        if ( empty( $this->api_key ) ) {
            return new WP_Error(
                'missing_api_key',
                __( 'API key is not set.', 'realestate-weather' )
            );
        }

        $url = sprintf(
            'https://api.openweathermap.org/data/2.5/weather?q=%s&units=metric&appid=%s',
            rawurlencode( $this->location ),
            $this->api_key
        );

        $response = wp_remote_get( $url, array( 'timeout' => 10 ) );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'network_error', $response->get_error_message() );
        }

        $code = wp_remote_retrieve_response_code( $response );

        if ( $code === 200 ) {
            return true;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        $msg  = isset( $body['message'] ) ? $body['message'] : __( 'Unknown error', 'realestate-weather' );

        return new WP_Error( 'api_error', sprintf( __( 'API Error (%d): %s', 'realestate-weather' ), $code, $msg ) );
    }

    public function get_settings_fields() {
        return array(
            'api_key'   => $this->api_key,
            'location'  => $this->location,
            'unit'      => $this->unit,
            'cache_time' => $this->cache_time,
        );
    }
}
