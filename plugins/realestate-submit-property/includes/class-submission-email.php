<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RESP_Submission_Email {

	public function send_admin_notification( $post_id, $data ) {
		$admin_email = get_option( 'admin_email' );
		$site_name   = get_bloginfo( 'name' );
		$admin_url   = admin_url( 'edit.php?post_type=property_submission' );

		$property_types = array(
			'house'       => __( 'House', 'realestate-submit-property' ),
			'apartment'   => __( 'Apartment', 'realestate-submit-property' ),
			'land'        => __( 'Land', 'realestate-submit-property' ),
			'commercial'  => __( 'Commercial Property', 'realestate-submit-property' ),
			'office'      => __( 'Office', 'realestate-submit-property' ),
			'shop'        => __( 'Shop', 'realestate-submit-property' ),
			'warehouse'   => __( 'Warehouse', 'realestate-submit-property' ),
			'villa'       => __( 'Villa', 'realestate-submit-property' ),
		);

		$type_label = $property_types[ $data['property_type'] ] ?? $data['property_type'];

		$subject = sprintf( __( '[%s] New Property Submission for Review', 'realestate-submit-property' ), $site_name );

		$message  = sprintf( __( 'A new property has been submitted for review on %s.', 'realestate-submit-property' ), $site_name ) . "\n\n";
		$message .= "---\n\n";
		$message .= sprintf( __( 'Submission ID: #%d', 'realestate-submit-property' ), $post_id ) . "\n";
		$message .= sprintf( __( 'Submission Date: %s', 'realestate-submit-property' ), current_time( 'F j, Y g:i A' ) ) . "\n\n";
		$message .= "---\n\n";
		$message .= __( 'OWNER DETAILS', 'realestate-submit-property' ) . "\n";
		$message .= sprintf( __( 'Name: %s', 'realestate-submit-property' ), $data['owner_name'] ) . "\n";
		$message .= sprintf( __( 'Telephone: %s', 'realestate-submit-property' ), $data['owner_telephone'] ) . "\n";
		$message .= sprintf( __( 'Email: %s', 'realestate-submit-property' ), $data['owner_email'] ) . "\n\n";
		$message .= __( 'PROPERTY DETAILS', 'realestate-submit-property' ) . "\n";
		$message .= sprintf( __( 'Type: %s', 'realestate-submit-property' ), $type_label ) . "\n";
		$message .= sprintf( __( 'Location: %s', 'realestate-submit-property' ), $data['property_location'] ) . "\n";
		$message .= sprintf( __( 'Address: %s', 'realestate-submit-property' ), $data['property_address'] ) . "\n";
		$message .= sprintf( __( 'Price: LKR %s', 'realestate-submit-property' ), number_format( $data['expected_price'] ) ) . "\n";

		if ( ! empty( $data['land_size'] ) ) {
			$message .= sprintf( __( 'Land Size: %s %s', 'realestate-submit-property' ), $data['land_size'], $data['land_size_unit'] ) . "\n";
		}
		if ( ! empty( $data['building_size'] ) ) {
			$message .= sprintf( __( 'Building Size: %s %s', 'realestate-submit-property' ), $data['building_size'], $data['building_size_unit'] ) . "\n";
		}

		$message .= sprintf( __( 'Bedrooms: %s', 'realestate-submit-property' ), $data['bedrooms'] ) . "\n";
		$message .= sprintf( __( 'Bathrooms: %s', 'realestate-submit-property' ), $data['bathrooms'] ) . "\n";
		$message .= sprintf( __( 'Parking: %s', 'realestate-submit-property' ), $data['parking_spaces'] ) . "\n\n";
		$message .= __( 'DESCRIPTION', 'realestate-submit-property' ) . "\n";
		$message .= $data['property_description'] . "\n\n";
		$message .= "---\n\n";
		$message .= sprintf( __( 'Review submission: %s', 'realestate-submit-property' ), $admin_url ) . "\n";

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

		wp_mail( $admin_email, $subject, $message, $headers );
	}

	public function send_customer_acknowledgement( $post_id, $data ) {
		$site_name = get_bloginfo( 'name' );
		$subject   = sprintf( __( '[%s] Thank You for Submitting Your Property', 'realestate-submit-property' ), $site_name );

		$message  = sprintf( __( 'Dear %s,', 'realestate-submit-property' ), $data['owner_name'] ) . "\n\n";
		$message .= sprintf( __( 'Thank you for submitting your property to %s.', 'realestate-submit-property' ), $site_name ) . "\n\n";
		$message .= sprintf( __( 'We have received your submission (Reference #%d) and our team will review it shortly.', 'realestate-submit-property' ), $post_id ) . "\n\n";
		$message .= __( 'SUBMISSION SUMMARY', 'realestate-submit-property' ) . "\n";
		$message .= sprintf( __( 'Property Type: %s', 'realestate-submit-property' ), $data['property_type'] ) . "\n";
		$message .= sprintf( __( 'Location: %s', 'realestate-submit-property' ), $data['property_location'] ) . "\n";
		$message .= sprintf( __( 'Expected Price: LKR %s', 'realestate-submit-property' ), number_format( $data['expected_price'] ) ) . "\n\n";
		$message .= __( 'REVIEW PROCESS', 'realestate-submit-property' ) . "\n";
		$message .= __( 'Our team typically reviews submissions within 2-3 business days. During this process, we will:', 'realestate-submit-property' ) . "\n\n";
		$message .= "1. " . __( 'Verify the property details', 'realestate-submit-property' ) . "\n";
		$message .= "2. " . __( 'Review the submitted images', 'realestate-submit-property' ) . "\n";
		$message .= "3. " . __( 'Contact you if we need additional information', 'realestate-submit-property' ) . "\n\n";
		$message .= __( 'Once approved, your property will be live on our website. If you have any questions, please don\'t hesitate to contact us.', 'realestate-submit-property' ) . "\n\n";
		$message .= __( 'Best regards,', 'realestate-submit-property' ) . "\n";
		$message .= $site_name . "\n";

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

		wp_mail( $data['owner_email'], $subject, $message, $headers );
	}

	public function send_rejection_email( $post_id, $data, $reason ) {
		$site_name = get_bloginfo( 'name' );
		$subject   = sprintf( __( '[%s] Property Submission Update', 'realestate-submit-property' ), $site_name );

		$message  = sprintf( __( 'Dear %s,', 'realestate-submit-property' ), $data['owner_name'] ) . "\n\n";
		$message .= sprintf( __( 'Thank you for submitting your property to %s.', 'realestate-submit-property' ), $site_name ) . "\n\n";
		$message .= sprintf( __( 'After careful review, we are unable to approve your property submission (Reference #%d) at this time.', 'realestate-submit-property' ), $post_id ) . "\n\n";
		$message .= __( 'REASON', 'realestate-submit-property' ) . "\n";
		$message .= $reason . "\n\n";
		$message .= __( 'You are welcome to submit your property again once the issues mentioned above have been addressed. If you have any questions, please contact us.', 'realestate-submit-property' ) . "\n\n";
		$message .= __( 'Best regards,', 'realestate-submit-property' ) . "\n";
		$message .= $site_name . "\n";

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

		wp_mail( $data['owner_email'], $subject, $message, $headers );
	}
}
