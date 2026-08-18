<?php
/**
 * Plugin Name: 11AA Real Estate Enquiries
 * Description: Customer enquiry form and management for 11AA Real Estate
 * Version: 1.0.0
 * Author: 11AA Real Estate
 * Text Domain: realestate-enquiry
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * License: GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'REE_VERSION', '1.0.0' );
define( 'REE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'REE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'REE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

function ree_activate() {
    global $wpdb;

    $table_name      = $wpdb->prefix . 'realestate_enquiries';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        full_name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        telephone varchar(30) NOT NULL,
        contact_method varchar(20) NOT NULL DEFAULT 'phone',
        property_id varchar(50) DEFAULT '',
        property_name varchar(200) DEFAULT '',
        enquiry_type varchar(50) NOT NULL,
        viewing_date date DEFAULT NULL,
        message text NOT NULL,
        status varchar(30) NOT NULL DEFAULT 'new',
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_email (email),
        KEY idx_status (status),
        KEY idx_created_at (created_at),
        KEY idx_property_id (property_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    add_option( 'ree_db_version', REE_VERSION );

    add_role( 'realestate_enquiry_manager', __( 'Enquiry Manager', 'realestate-enquiry' ), array(
        'read'                    => true,
        'manage_realestate_enquiries' => true,
    ) );

    $admin_role = get_role( 'administrator' );
    if ( $admin_role ) {
        $admin_role->add_cap( 'manage_realestate_enquiries' );
    }
}
register_activation_hook( __FILE__, 'ree_activate' );

function ree_deactivate() {
    remove_role( 'realestate_enquiry_manager' );
}
register_deactivation_hook( __FILE__, 'ree_deactivate' );

require_once REE_PLUGIN_DIR . 'includes/class-enquiry-form.php';
require_once REE_PLUGIN_DIR . 'includes/class-enquiry-storage.php';
require_once REE_PLUGIN_DIR . 'includes/class-enquiry-email.php';

if ( is_admin() ) {
    require_once REE_PLUGIN_DIR . 'admin/class-enquiry-admin.php';
}

new Ree_Enquiry_Form();
new Ree_Enquiry_Email();
