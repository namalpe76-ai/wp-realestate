<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RESP_Submission_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'load-post-new.php', array( $this, 'redirect_cpt_creation' ) );
		add_action( 'wp_ajax_resp_approve_submission', array( $this, 'ajax_approve_submission' ) );
		add_action( 'wp_ajax_resp_reject_submission', array( $this, 'ajax_reject_submission' ) );
		add_action( 'wp_ajax_resp_delete_submission', array( $this, 'ajax_delete_submission' ) );
		add_action( 'wp_ajax_resp_export_submissions', array( $this, 'ajax_export_submissions' ) );
		add_action( 'wp_ajax_resp_update_status', array( $this, 'ajax_update_status' ) );
	}

	public function add_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=property_submission',
			__( 'Submission Detail', 'realestate-submit-property' ),
			__( 'Submission Detail', 'realestate-submit-property' ),
			'manage_options',
			'resp-submission-detail',
			array( $this, 'render_detail_page' )
		);
	}

	public function render_detail_page() {
		include RESP_PLUGIN_DIR . 'admin/views/submission-detail.php';
	}

	public function redirect_cpt_creation() {
		if ( isset( $_GET['post_type'] ) && 'property_submission' === $_GET['post_type'] ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=property_submission' ) );
			exit;
		}
	}

	public function enqueue_admin_assets( $hook ) {
		if ( strpos( $hook, 'property_submission' ) === false && strpos( $hook, 'resp-submission-detail' ) === false ) {
			return;
		}
		wp_enqueue_style( 'resp-submission-admin', RESP_PLUGIN_URL . 'assets/css/submission-admin.css', array(), RESP_VERSION );
		wp_enqueue_script( 'jquery' );
	}

	public static function get_status_options() {
		return array(
			'pending_review' => __( 'Pending Review', 'realestate-submit-property' ),
			'under_review'   => __( 'Under Review', 'realestate-submit-property' ),
			'approved'       => __( 'Approved', 'realestate-submit-property' ),
			'rejected'       => __( 'Rejected', 'realestate-submit-property' ),
			'contacted'      => __( 'Contacted', 'realestate-submit-property' ),
		);
	}

	public static function get_submission_data( $post_id ) {
		$data = array();
		$meta_keys = array(
			'owner_name', 'owner_telephone', 'owner_email',
			'property_type', 'property_location', 'property_address',
			'expected_price', 'land_size', 'land_size_unit',
			'building_size', 'building_size_unit',
			'bedrooms', 'bathrooms', 'parking_spaces',
			'property_description', 'property_images',
			'submission_status', 'submission_date',
			'rejection_reason',
		);

		foreach ( $meta_keys as $key ) {
			$data[ $key ] = get_post_meta( $post_id, '_resp_' . $key, true );
		}

		return $data;
	}

	public function ajax_approve_submission() {
		check_ajax_referer( 'resp_admin_action', 'admin_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'realestate-submit-property' ) ) );
		}

		$post_id = absint( $_POST['post_id'] ?? 0 );
		$data    = self::get_submission_data( $post_id );

		if ( empty( $data['owner_name'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Submission not found.', 'realestate-submit-property' ) ) );
		}

		$property_types = array(
			'house'      => 'house',
			'apartment'  => 'apartment',
			'land'       => 'land',
			'commercial' => 'commercial',
			'office'     => 'office',
			'shop'       => 'shop',
			'warehouse'  => 'warehouse',
			'villa'      => 'villa',
		);

		$new_post_id = wp_insert_post( array(
			'post_title'  => sprintf( __( '%s - %s', 'realestate-submit-property' ), $data['property_location'], $data['property_type'] ),
			'post_type'   => 'property',
			'post_status' => 'draft',
			'post_content' => $data['property_description'],
		) );

		if ( is_wp_error( $new_post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to create property listing.', 'realestate-submit-property' ) ) );
		}

		$meta = array(
			'property_type'      => $property_types[ $data['property_type'] ] ?? $data['property_type'],
			'property_location'  => $data['property_location'],
			'property_address'   => $data['property_address'],
			'expected_price'     => $data['expected_price'],
			'land_size'          => $data['land_size'],
			'land_size_unit'     => $data['land_size_unit'],
			'building_size'      => $data['building_size'],
			'building_size_unit' => $data['building_size_unit'],
			'bedrooms'           => $data['bedrooms'],
			'bathrooms'          => $data['bathrooms'],
			'parking_spaces'     => $data['parking_spaces'],
			'owner_name'         => $data['owner_name'],
			'owner_telephone'    => $data['owner_telephone'],
			'owner_email'        => $data['owner_email'],
		);

		foreach ( $meta as $key => $value ) {
			update_post_meta( $new_post_id, '_property_' . $key, $value );
		}

		// Attach images.
		if ( ! empty( $data['property_images'] ) && is_array( $data['property_images'] ) ) {
			foreach ( $data['property_images'] as $att_id ) {
				wp_update_post( array(
					'ID'          => $att_id,
					'post_parent' => $new_post_id,
				) );
			}
		}

		update_post_meta( $post_id, '_resp_submission_status', 'approved' );
		wp_update_post( array( 'ID' => $post_id, 'post_status' => 'private' ) );

		wp_send_json_success( array(
			'message'      => __( 'Submission approved and property listing created.', 'realestate-submit-property' ),
			'property_id'  => $new_post_id,
			'edit_url'     => get_edit_post_link( $new_post_id, 'raw' ),
		) );
	}

	public function ajax_reject_submission() {
		check_ajax_referer( 'resp_admin_action', 'admin_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'realestate-submit-property' ) ) );
		}

		$post_id = absint( $_POST['post_id'] ?? 0 );
		$reason  = sanitize_textarea_field( wp_unslash( $_POST['reason'] ?? '' ) );
		$data    = self::get_submission_data( $post_id );

		if ( empty( $data['owner_name'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Submission not found.', 'realestate-submit-property' ) ) );
		}

		update_post_meta( $post_id, '_resp_submission_status', 'rejected' );
		update_post_meta( $post_id, '_resp_rejection_reason', $reason );
		wp_update_post( array( 'ID' => $post_id, 'post_status' => 'private' ) );

		$email = new RESP_Submission_Email();
		$email->send_rejection_email( $post_id, $data, $reason );

		wp_send_json_success( array( 'message' => __( 'Submission rejected. Notification email sent.', 'realestate-submit-property' ) ) );
	}

	public function ajax_delete_submission() {
		check_ajax_referer( 'resp_admin_action', 'admin_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'realestate-submit-property' ) ) );
		}

		$post_id = absint( $_POST['post_id'] ?? 0 );

		$images = get_post_meta( $post_id, '_resp_property_images', true );
		if ( ! empty( $images ) && is_array( $images ) ) {
			foreach ( $images as $att_id ) {
				wp_delete_attachment( $att_id, true );
			}
		}

		wp_delete_post( $post_id, true );

		wp_send_json_success( array( 'message' => __( 'Submission deleted.', 'realestate-submit-property' ) ) );
	}

	public function ajax_export_submissions() {
		check_ajax_referer( 'resp_admin_action', 'admin_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'realestate-submit-property' ) ) );
		}

		$query = new WP_Query( array(
			'post_type'      => 'property_submission',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		$csv_data = array();
		$csv_data[] = array(
			'ID', 'Owner Name', 'Telephone', 'Email', 'Type', 'Location',
			'Price', 'Land Size', 'Building Size', 'Bedrooms', 'Bathrooms',
			'Parking', 'Status', 'Date',
		);

		$type_labels = array(
			'house' => 'House', 'apartment' => 'Apartment', 'land' => 'Land',
			'commercial' => 'Commercial', 'office' => 'Office', 'shop' => 'Shop',
			'warehouse' => 'Warehouse', 'villa' => 'Villa',
		);

		$status_labels = self::get_status_options();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$data = self::get_submission_data( get_the_ID() );
				$csv_data[] = array(
					get_the_ID(),
					$data['owner_name'],
					$data['owner_telephone'],
					$data['owner_email'],
					$type_labels[ $data['property_type'] ] ?? $data['property_type'],
					$data['property_location'],
					$data['expected_price'],
					$data['land_size'] . ' ' . $data['land_size_unit'],
					$data['building_size'] . ' ' . $data['building_size_unit'],
					$data['bedrooms'],
					$data['bathrooms'],
					$data['parking_spaces'],
					$status_labels[ $data['submission_status'] ] ?? $data['submission_status'],
					$data['submission_date'],
				);
			}
			wp_reset_postdata();
		}

		$filename = 'property-submissions-' . gmdate( 'Y-m-d' ) . '.csv';
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		$output = fopen( 'php://output', 'w' );
		foreach ( $csv_data as $row ) {
			fputcsv( $output, $row );
		}
		fclose( $output );
		exit;
	}

	public function ajax_update_status() {
		check_ajax_referer( 'resp_admin_action', 'admin_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'realestate-submit-property' ) ) );
		}

		$post_id = absint( $_POST['post_id'] ?? 0 );
		$status  = sanitize_text_field( wp_unslash( $_POST['status'] ?? '' ) );

		$valid_statuses = array_keys( self::get_status_options() );
		if ( ! in_array( $status, $valid_statuses, true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid status.', 'realestate-submit-property' ) ) );
		}

		update_post_meta( $post_id, '_resp_submission_status', $status );

		wp_send_json_success( array( 'message' => __( 'Status updated.', 'realestate-submit-property' ) ) );
	}
}
