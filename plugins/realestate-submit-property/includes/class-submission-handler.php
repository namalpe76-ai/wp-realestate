<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RESP_Submission_Handler {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'wp_ajax_resp_submit_property', array( $this, 'handle_submission' ) );
		add_action( 'wp_ajax_nopriv_resp_submit_property', array( $this, 'handle_submission' ) );
	}

	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Property Submissions', 'realestate-submit-property' ),
			'singular_name'      => __( 'Property Submission', 'realestate-submit-property' ),
			'menu_name'          => __( 'Property Submissions', 'realestate-submit-property' ),
			'all_items'          => __( 'All Submissions', 'realestate-submit-property' ),
			'add_new'            => __( 'Add New', 'realestate-submit-property' ),
			'add_new_item'       => __( 'Add New Submission', 'realestate-submit-property' ),
			'edit_item'          => __( 'Edit Submission', 'realestate-submit-property' ),
			'new_item'           => __( 'New Submission', 'realestate-submit-property' ),
			'view_item'          => __( 'View Submission', 'realestate-submit-property' ),
			'search_items'       => __( 'Search Submissions', 'realestate-submit-property' ),
			'not_found'          => __( 'No submissions found', 'realestate-submit-property' ),
			'not_found_in_trash' => __( 'No submissions found in trash', 'realestate-submit-property' ),
			'attributes'         => __( 'Submission Attributes', 'realestate-submit-property' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'supports'           => array( 'title' ),
			'has_archive'        => false,
			'rewrite'            => false,
			'query_var'          => false,
			'menu_icon'          => 'dashicons-building',
			'capabilities'       => array(
				'edit_post'          => 'manage_options',
				'read_post'          => 'manage_options',
				'delete_post'        => 'manage_options',
				'edit_posts'         => 'manage_options',
				'edit_others_posts'  => 'manage_options',
				'publish_posts'      => 'manage_options',
				'read_private_posts' => 'manage_options',
			),
		);

		register_post_type( 'property_submission', $args );
	}

	public function handle_submission() {
		check_ajax_referer( 'resp_submit_nonce', 'resp_nonce' );

		// Honeypot check.
		if ( ! empty( $_POST['website'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Spam detected.', 'realestate-submit-property' ) ) );
		}

		// Sanitize all inputs.
		$data = $this->sanitize_submission();

		// Validate required fields.
		$errors = $this->validate_submission( $data );
		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'message' => __( 'Please correct the errors below.', 'realestate-submit-property' ), 'errors' => $errors ) );
		}

		// Handle file uploads.
		$attachment_ids = $this->handle_file_uploads();
		if ( is_wp_error( $attachment_ids ) ) {
			wp_send_json_error( array( 'message' => $attachment_ids->get_error_message() ) );
		}

		// Create the submission post.
		$post_id = wp_insert_post( array(
			'post_title'  => sprintf( __( 'Submission: %s - %s', 'realestate-submit-property' ), $data['owner_name'], $data['property_type'] ),
			'post_type'   => 'property_submission',
			'post_status' => 'pending',
		) );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to save submission. Please try again.', 'realestate-submit-property' ) ) );
		}

		// Store all metadata.
		$meta_fields = array(
			'owner_name'         => $data['owner_name'],
			'owner_telephone'    => $data['owner_telephone'],
			'owner_email'        => $data['owner_email'],
			'property_type'      => $data['property_type'],
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
			'property_description' => $data['property_description'],
			'property_images'    => $attachment_ids,
			'submission_status'  => 'pending_review',
			'submission_date'    => current_time( 'mysql' ),
		);

		foreach ( $meta_fields as $key => $value ) {
			update_post_meta( $post_id, '_resp_' . $key, $value );
		}

		// Send emails.
		$email = new RESP_Submission_Email();
		$email->send_admin_notification( $post_id, $data );
		$email->send_customer_acknowledgement( $post_id, $data );

		wp_send_json_success( array(
			'message' => __( 'Your property has been submitted successfully! We will review it shortly.', 'realestate-submit-property' ),
		) );
	}

	private function sanitize_submission() {
		return array(
			'owner_name'         => sanitize_text_field( wp_unslash( $_POST['owner_name'] ?? '' ) ),
			'owner_telephone'    => sanitize_text_field( wp_unslash( $_POST['owner_telephone'] ?? '' ) ),
			'owner_email'        => sanitize_email( wp_unslash( $_POST['owner_email'] ?? '' ) ),
			'property_type'      => sanitize_text_field( wp_unslash( $_POST['property_type'] ?? '' ) ),
			'property_location'  => sanitize_text_field( wp_unslash( $_POST['property_location'] ?? '' ) ),
			'property_address'   => sanitize_textarea_field( wp_unslash( $_POST['property_address'] ?? '' ) ),
			'expected_price'     => absint( $_POST['expected_price'] ?? 0 ),
			'land_size'          => sanitize_text_field( wp_unslash( $_POST['land_size'] ?? '' ) ),
			'land_size_unit'     => sanitize_text_field( wp_unslash( $_POST['land_size_unit'] ?? 'perches' ) ),
			'building_size'      => sanitize_text_field( wp_unslash( $_POST['building_size'] ?? '' ) ),
			'building_size_unit' => sanitize_text_field( wp_unslash( $_POST['building_size_unit'] ?? 'sqft' ) ),
			'bedrooms'           => absint( $_POST['bedrooms'] ?? 0 ),
			'bathrooms'          => absint( $_POST['bathrooms'] ?? 0 ),
			'parking_spaces'     => absint( $_POST['parking_spaces'] ?? 0 ),
			'property_description' => sanitize_textarea_field( wp_unslash( $_POST['property_description'] ?? '' ) ),
			'gdpr_consent'       => ! empty( $_POST['gdpr_consent'] ),
		);
	}

	private function validate_submission( $data ) {
		$errors = array();

		if ( empty( $data['owner_name'] ) ) {
			$errors['owner_name'] = __( 'Owner name is required.', 'realestate-submit-property' );
		}
		if ( empty( $data['owner_telephone'] ) ) {
			$errors['owner_telephone'] = __( 'Telephone is required.', 'realestate-submit-property' );
		}
		if ( empty( $data['owner_email'] || ! is_email( $data['owner_email'] ) ) ) {
			$errors['owner_email'] = __( 'Valid email is required.', 'realestate-submit-property' );
		}
		if ( empty( $data['property_type'] ) ) {
			$errors['property_type'] = __( 'Property type is required.', 'realestate-submit-property' );
		}
		if ( empty( $data['property_location'] ) ) {
			$errors['property_location'] = __( 'Location is required.', 'realestate-submit-property' );
		}
		if ( empty( $data['property_address'] ) ) {
			$errors['property_address'] = __( 'Full address is required.', 'realestate-submit-property' );
		}
		if ( empty( $data['expected_price'] ) ) {
			$errors['expected_price'] = __( 'Expected price is required.', 'realestate-submit-property' );
		}
		if ( empty( $data['property_description'] ) || strlen( $data['property_description'] ) < 50 ) {
			$errors['property_description'] = __( 'Description must be at least 50 characters.', 'realestate-submit-property' );
		}
		if ( ! $data['gdpr_consent'] ) {
			$errors['gdpr_consent'] = __( 'You must agree to the privacy policy.', 'realestate-submit-property' );
		}

		return $errors;
	}

	private function handle_file_uploads() {
		if ( empty( $_FILES['property_images']['name'][0] ) ) {
			return array();
		}

		$files       = $_FILES['property_images'];
		$count       = count( $files['name'] );
		$allowed     = array( 'jpg', 'jpeg', 'png', 'webp' );
		$max_size    = 5 * 1024 * 1024;
		$max_total   = 50 * 1024 * 1024;
		$total_size  = 0;
		$attachments = array();

		if ( $count > 10 ) {
			return new WP_Error( 'too_many_files', __( 'Maximum 10 files allowed.', 'realestate-submit-property' ) );
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		for ( $i = 0; $i < $count; $i++ ) {
			$file_name = $files['name'][ $i ];
			$file_tmp  = $files['tmp_name'][ $i ];
			$file_size = $files['size'][ $i ];
			$file_err  = $files['error'][ $i ];

			if ( $file_err !== UPLOAD_ERR_OK ) {
				return new WP_Error( 'upload_error', __( 'File upload error occurred.', 'realestate-submit-property' ) );
			}

			$ext = strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) );
			if ( ! in_array( $ext, $allowed, true ) ) {
				return new WP_Error( 'invalid_type', sprintf( __( 'Invalid file type: %s. Allowed: JPG, PNG, WebP.', 'realestate-submit-property' ), $file_name ) );
			}

			if ( $file_size > $max_size ) {
				return new WP_Error( 'file_too_large', sprintf( __( 'File %s exceeds 5MB limit.', 'realestate-submit-property' ), $file_name ) );
			}

			$total_size += $file_size;
			if ( $total_size > $max_total ) {
				return new WP_Error( 'total_too_large', __( 'Total file size exceeds 50MB limit.', 'realestate-submit-property' ) );
			}

			$upload = wp_handle_upload( $files, array( 'test_form' => false ) );

			if ( isset( $upload['error'] ) ) {
				return new WP_Error( 'upload_failed', $upload['error'] );
			}

			$attachment = array(
				'post_title'     => sanitize_file_name( $file_name ),
				'post_mime_type' => $upload['type'],
				'post_status'    => 'inherit',
				'guid'           => $upload['url'],
			);

			$att_id = wp_insert_attachment( $attachment, $upload['file'] );
			if ( ! is_wp_error( $att_id ) ) {
				$attach_data = wp_generate_attachment_metadata( $att_id, $upload['file'] );
				wp_update_attachment_metadata( $att_id, $attach_data );
				$attachments[] = $att_id;
			}
		}

		return $attachments;
	}
}
