<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class REALESTATE_Visitor_Tracker {

    private $table_name;
    private $salt;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'realestate_visitors';
        $this->salt       = defined( 'LOGGED_IN_KEY' ) ? LOGGED_IN_KEY : 'realestate_default_salt';
    }

    public function create_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ip_hash VARCHAR(64) NOT NULL,
            user_agent TEXT,
            page_url VARCHAR(500) NOT NULL,
            page_title VARCHAR(255) DEFAULT '',
            referrer VARCHAR(500) DEFAULT '',
            is_unique TINYINT(1) NOT NULL DEFAULT 1,
            session_id VARCHAR(64) NOT NULL,
            country VARCHAR(100) DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_ip_hash (ip_hash),
            KEY idx_session_id (session_id),
            KEY idx_created_at (created_at),
            KEY idx_page_url (page_url(191))
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        update_option( 'realestate_analytics_db_version', '1.0' );
    }

    public function track_visit() {
        if ( is_admin() || wp_doing_ajax() ) {
            return;
        }

        if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'bot' ) !== false ) {
            return;
        }

        $ip_hash     = $this->hash_ip( $this->get_client_ip() );
        $user_agent  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
        $page_url    = $this->get_current_url();
        $page_title  = get_the_title() ? get_the_title() : wp_strip_all_tags( get_the_title() );
        $referrer    = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
        $session_id  = $this->get_session_id();
        $is_unique   = $this->is_unique_visitor( $ip_hash );

        $this->log_visit( $ip_hash, $user_agent, $page_url, $page_title, $referrer, $is_unique, $session_id );
    }

    private function hash_ip( $ip ) {
        return md5( $ip . $this->salt );
    }

    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        );

        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
                if ( strpos( $ip, ',' ) !== false ) {
                    $ip = trim( explode( ',', $ip )[0] );
                }
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }

        return '127.0.0.1';
    }

    private function get_current_url() {
        if ( empty( $_SERVER['HTTP_HOST'] ) || empty( $_SERVER['REQUEST_URI'] ) ) {
            return '';
        }
        $scheme = is_ssl() ? 'https' : 'http';
        return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    private function get_session_id() {
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }

        if ( empty( $_SESSION['realestate_visitor_id'] ) ) {
            $_SESSION['realestate_visitor_id'] = wp_generate_password( 32, false );
        }

        return $_SESSION['realestate_visitor_id'];
    }

    private function is_unique_visitor( $ip_hash ) {
        global $wpdb;

        $today = current_time( 'Y-m-d' );

        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE ip_hash = %s AND DATE(created_at) = %s",
                $ip_hash,
                $today
            )
        );

        return (int) $existing === 0;
    }

    private function log_visit( $ip_hash, $user_agent, $page_url, $page_title, $referrer, $is_unique, $session_id ) {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table_name,
            array(
                'ip_hash'     => $ip_hash,
                'user_agent'  => $user_agent,
                'page_url'    => $page_url,
                'page_title'  => $page_title,
                'referrer'    => $referrer,
                'is_unique'   => $is_unique ? 1 : 0,
                'session_id'  => $session_id,
                'created_at'  => current_time( 'mysql' ),
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
            )
        );

        if ( $result ) {
            delete_transient( 'realestate_analytics_stats' );
            delete_transient( 'realestate_analytics_daily_' . current_time( 'Y-m-d' ) );
        }

        return $result;
    }

    public function get_total_unique_visitors() {
        global $wpdb;

        $result = $wpdb->get_var( "SELECT COUNT(DISTINCT ip_hash) FROM {$this->table_name}" );
        return (int) $result;
    }

    public function get_total_page_views() {
        global $wpdb;

        $result = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name}" );
        return (int) $result;
    }

    public function get_today_visitors() {
        global $wpdb;

        $today = current_time( 'Y-m-d' );

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT ip_hash) FROM {$this->table_name} WHERE DATE(created_at) = %s",
                $today
            )
        );

        return (int) $result;
    }

    public function get_month_visitors() {
        global $wpdb;

        $month = current_time( 'Y-m' );

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT ip_hash) FROM {$this->table_name} WHERE DATE_FORMAT(created_at, '%%Y-%%m') = %s",
                $month
            )
        );

        return (int) $result;
    }

    public function get_last_7_days_visits() {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT DATE(created_at) as visit_date, COUNT(DISTINCT ip_hash) as visitor_count
            FROM {$this->table_name}
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY visit_date ASC"
        );

        $data = array();
        for ( $i = 6; $i >= 0; $i-- ) {
            $date  = date( 'Y-m-d', strtotime( "-{$i} days" ) );
            $label = date( 'M d', strtotime( $date ) );
            $count = 0;

            foreach ( $results as $row ) {
                if ( $row->visit_date === $date ) {
                    $count = (int) $row->visitor_count;
                    break;
                }
            }

            $data[] = array(
                'date'    => $label,
                'count'   => $count,
            );
        }

        return $data;
    }
}
