<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Ree_Enquiry_Admin {

    private $storage;

    public function __construct() {
        $this->storage = new Ree_Enquiry_Storage();

        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_ajax_ree_update_status', array( $this, 'ajax_update_status' ) );
        add_action( 'wp_ajax_ree_delete_enquiry', array( $this, 'ajax_delete_enquiry' ) );
        add_action( 'wp_ajax_ree_export_csv', array( $this, 'ajax_export_csv' ) );
    }

    public function add_menu() {
        add_menu_page(
            __( 'Real Estate Enquiries', 'realestate-enquiry' ),
            __( 'Enquiries', 'realestate-enquiry' ),
            'manage_realestate_enquiries',
            'realestate-enquiries',
            array( $this, 'render_admin_page' ),
            'dashicons-email-alt',
            26
        );

        add_submenu_page(
            'realestate-enquiries',
            __( 'All Enquiries', 'realestate-enquiry' ),
            __( 'All Enquiries', 'realestate-enquiry' ),
            'manage_realestate_enquiries',
            'realestate-enquiries',
            array( $this, 'render_admin_page' )
        );
    }

    public function enqueue_admin_assets( $hook ) {
        if ( 'toplevel_page_realestate-enquiries' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'ree-enquiry-admin',
            REE_PLUGIN_URL . 'assets/css/enquiry-admin.css',
            array(),
            REE_VERSION
        );

        wp_enqueue_script(
            'ree-enquiry-admin',
            REE_PLUGIN_URL . 'assets/js/enquiry-admin.js',
            array( 'jquery' ),
            REE_VERSION,
            true
        );

        wp_localize_script( 'ree-enquiry-admin', 'ree_admin', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'ree_admin_nonce' ),
            'i18n'     => array(
                'confirm_delete'  => __( 'Are you sure you want to delete this enquiry? This action cannot be undone.', 'realestate-enquiry' ),
                'confirm_bulk'    => __( 'Are you sure you want to perform this action on the selected enquiries?', 'realestate-enquiry' ),
                'status_updated'  => __( 'Status updated successfully.', 'realestate-enquiry' ),
                'enquiry_deleted' => __( 'Enquiry deleted successfully.', 'realestate-enquiry' ),
                'error'           => __( 'An error occurred. Please try again.', 'realestate-enquiry' ),
                'no_selected'     => __( 'Please select at least one enquiry.', 'realestate-enquiry' ),
            ),
        ) );
    }

    public function render_admin_page() {
        if ( ! current_user_can( 'manage_realestate_enquiries' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'realestate-enquiry' ) );
        }

        if ( isset( $_GET['action'] ) && 'view' === $_GET['action'] && isset( $_GET['id'] ) ) {
            require_once REE_PLUGIN_DIR . 'admin/views/enquiry-detail.php';
            return;
        }

        require_once REE_PLUGIN_DIR . 'admin/views/enquiry-list.php';
    }

    public function ajax_update_status() {
        check_ajax_referer( 'ree_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_realestate_enquiries' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'realestate-enquiry' ) ) );
        }

        $id     = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
        $status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

        if ( ! $id || empty( $status ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid parameters.', 'realestate-enquiry' ) ) );
        }

        $result = $this->storage->update_status( $id, $status );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Status updated successfully.', 'realestate-enquiry' ) ) );
    }

    public function ajax_delete_enquiry() {
        check_ajax_referer( 'ree_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_realestate_enquiries' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'realestate-enquiry' ) ) );
        }

        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid enquiry ID.', 'realestate-enquiry' ) ) );
        }

        $result = $this->storage->delete( $id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Enquiry deleted successfully.', 'realestate-enquiry' ) ) );
    }

    public function ajax_export_csv() {
        check_ajax_referer( 'ree_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_realestate_enquiries' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'realestate-enquiry' ) ) );
        }

        $csv_data = $this->storage->export_csv();

        $filename = 'enquiries-' . gmdate( 'Y-m-d-His' ) . '.csv';

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=' . $filename );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $output = fopen( 'php://output', 'w' );

        foreach ( $csv_data as $row ) {
            fputcsv( $output, $row );
        }

        fclose( $output );
        exit;
    }
}

new Ree_Enquiry_Admin();
