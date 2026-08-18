<?php
/**
 * Plugin Name: 11AA Real Estate Core
 * Description: Core property listing functionality for 11AA Real Estate
 * Version: 1.0.0
 * Author: 11AA Real Estate
 * Text Domain: realestate-core
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'REAL_ESTATE_CORE_VERSION', '1.0.0' );
define( 'REAL_ESTATE_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'REAL_ESTATE_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'REAL_ESTATE_CORE_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main plugin class.
 */
final class Real_Estate_Core {

    /**
     * Single instance.
     *
     * @var Real_Estate_Core|null
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     *
     * @return Real_Estate_Core
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required files.
     */
    private function load_dependencies() {
        require_once REAL_ESTATE_CORE_PATH . 'includes/class-property-post-type.php';
        require_once REAL_ESTATE_CORE_PATH . 'includes/class-property-meta-boxes.php';
        require_once REAL_ESTATE_CORE_PATH . 'includes/class-property-search.php';
        require_once REAL_ESTATE_CORE_PATH . 'includes/class-property-widgets.php';
        require_once REAL_ESTATE_CORE_PATH . 'includes/class-property-email.php';
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'load_textdomain' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        new Property_Post_Type();
        new Property_Meta_Boxes();
        new Property_Search();
        new Property_Widgets();
        new Property_Email();
    }

    /**
     * Load plugin text domain.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'realestate-core', false, dirname( REAL_ESTATE_CORE_BASENAME ) . '/languages' );
    }

    /**
     * Enqueue front-end assets.
     */
    public function enqueue_assets() {
        global $post;

        $is_property = is_singular( 'property' ) || is_post_type_archive( 'property' ) || is_tax( array( 'property_type', 'property_status', 'property_location' ) );

        if ( ! $is_property && ! is_search() ) {
            return;
        }

        wp_enqueue_style(
            'realestate-core-style',
            REAL_ESTATE_CORE_URL . 'assets/css/property.css',
            array(),
            REAL_ESTATE_CORE_VERSION
        );

        wp_enqueue_script(
            'realestate-core-script',
            REAL_ESTATE_CORE_URL . 'assets/js/property.js',
            array( 'jquery' ),
            REAL_ESTATE_CORE_VERSION,
            true
        );

        wp_localize_script( 'realestate-core-script', 'realestateCore', array(
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'restUrl'   => rest_url( 'realestate-core/v1/' ),
            'nonce'     => wp_create_nonce( 'realestate_core_nonce' ),
            'homeUrl'   => home_url( '/' ),
        ) );
    }
}

/**
 * Activation hook.
 */
function realestate_core_activate() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'realestate_core_activate' );

/**
 * Deactivation hook.
 */
function realestate_core_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'realestate_core_deactivate' );

/**
 * Initialize plugin.
 */
function realestate_core_init() {
    return Real_Estate_Core::instance();
}
add_action( 'plugins_loaded', 'realestate_core_init' );
