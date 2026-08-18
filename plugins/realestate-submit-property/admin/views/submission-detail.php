<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = absint( $_GET['id'] ?? 0 );
if ( ! $post_id ) {
	echo '<div class="wrap"><p>' . esc_html__( 'Submission not found.', 'realestate-submit-property' ) . '</p></div>';
	return;
}

$data = RESP_Submission_Admin::get_submission_data( $post_id );
if ( empty( $data['owner_name'] ) ) {
	echo '<div class="wrap"><p>' . esc_html__( 'Submission not found.', 'realestate-submit-property' ) . '</p></div>';
	return;
}

$statuses = RESP_Submission_Admin::get_status_options();
$current_status = $data['submission_status'] ?? 'pending_review';
$admin_nonce = wp_create_nonce( 'resp_admin_action' );

$type_labels = array(
	'house' => 'House', 'apartment' => 'Apartment', 'land' => 'Land',
	'commercial' => 'Commercial', 'office' => 'Office', 'shop' => 'Shop',
	'warehouse' => 'Warehouse', 'villa' => 'Villa',
);
$unit_labels = array( 'perches' => 'Perches', 'acres' => 'Acres', 'sqft' => 'sqft', 'sqm' => 'sqm' );

$images = array();
if ( ! empty( $data['property_images'] ) && is_array( $data['property_images'] ) ) {
	foreach ( $data['property_images'] as $att_id ) {
		$url = wp_get_attachment_url( $att_id );
		if ( $url ) {
			$images[] = $url;
		}
	}
}
?>

<div class="wrap resp-admin-wrap resp-detail-wrap">
	<h1>
		<a href="edit.php?post_type=property_submission" class="wp-heading-inline">&laquo; <?php esc_html_e( 'Back to Submissions', 'realestate-submit-property' ); ?></a>
	</h1>

	<div class="resp-detail-header">
		<h2><?php printf( esc_html__( 'Submission #%d', 'realestate-submit-property' ), $post_id ); ?></h2>
		<span class="resp-status-badge resp-status-<?php echo esc_attr( $current_status ); ?>">
			<?php echo esc_html( $statuses[ $current_status ] ?? $current_status ); ?>
		</span>
	</div>

	<div class="resp-detail-grid">
		<div class="resp-detail-card">
			<h3><?php esc_html_e( 'Owner Information', 'realestate-submit-property' ); ?></h3>
			<table class="widefat">
				<tr><td><strong><?php esc_html_e( 'Name:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $data['owner_name'] ); ?></td></tr>
				<tr><td><strong><?php esc_html_e( 'Telephone:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $data['owner_telephone'] ); ?></td></tr>
				<tr><td><strong><?php esc_html_e( 'Email:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $data['owner_email'] ); ?></td></tr>
			</table>
		</div>

		<div class="resp-detail-card">
			<h3><?php esc_html_e( 'Property Details', 'realestate-submit-property' ); ?></h3>
			<table class="widefat">
				<tr><td><strong><?php esc_html_e( 'Type:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $type_labels[ $data['property_type'] ] ?? $data['property_type'] ); ?></td></tr>
				<tr><td><strong><?php esc_html_e( 'Location:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $data['property_location'] ); ?></td></tr>
				<tr><td><strong><?php esc_html_e( 'Address:', 'realestate-submit-property' ); ?></strong></td><td><?php echo nl2br( esc_html( $data['property_address'] ) ); ?></td></tr>
				<tr><td><strong><?php esc_html_e( 'Expected Price:', 'realestate-submit-property' ); ?></strong></td><td>LKR <?php echo esc_html( number_format( (float) $data['expected_price'] ) ); ?></td></tr>
				<?php if ( ! empty( $data['land_size'] ) ) : ?>
					<tr><td><strong><?php esc_html_e( 'Land Size:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $data['land_size'] . ' ' . ( $unit_labels[ $data['land_size_unit'] ] ?? $data['land_size_unit'] ) ); ?></td></tr>
				<?php endif; ?>
				<?php if ( ! empty( $data['building_size'] ) ) : ?>
					<tr><td><strong><?php esc_html_e( 'Building Size:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $data['building_size'] . ' ' . ( $unit_labels[ $data['building_size_unit'] ] ?? $data['building_size_unit'] ) ); ?></td></tr>
				<?php endif; ?>
				<tr><td><strong><?php esc_html_e( 'Bedrooms:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $data['bedrooms'] ); ?></td></tr>
				<tr><td><strong><?php esc_html_e( 'Bathrooms:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $data['bathrooms'] ); ?></td></tr>
				<tr><td><strong><?php esc_html_e( 'Parking Spaces:', 'realestate-submit-property' ); ?></strong></td><td><?php echo esc_html( $data['parking_spaces'] ); ?></td></tr>
			</table>
		</div>

		<div class="resp-detail-card resp-detail-full">
			<h3><?php esc_html_e( 'Description', 'realestate-submit-property' ); ?></h3>
			<div class="resp-description-box"><?php echo nl2br( esc_html( $data['property_description'] ) ); ?></div>
		</div>

		<?php if ( ! empty( $images ) ) : ?>
			<div class="resp-detail-card resp-detail-full">
				<h3><?php esc_html_e( 'Property Images', 'realestate-submit-property' ); ?></h3>
				<div class="resp-image-gallery">
					<?php foreach ( $images as $img_url ) : ?>
						<div class="resp-gallery-item">
							<a href="<?php echo esc_url( $img_url ); ?>" target="_blank">
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php esc_attr_e( 'Property Image', 'realestate-submit-property' ); ?>">
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<div class="resp-detail-actions" data-id="<?php echo esc_attr( $post_id ); ?>" data-nonce="<?php echo esc_attr( $admin_nonce ); ?>">
		<h3><?php esc_html_e( 'Actions', 'realestate-submit-property' ); ?></h3>

		<div class="resp-action-buttons">
			<?php if ( $current_status !== 'approved' ) : ?>
				<button type="button" class="button button-primary resp-approve-btn" data-id="<?php echo esc_attr( $post_id ); ?>">
					<?php esc_html_e( 'Approve & Create Listing', 'realestate-submit-property' ); ?>
				</button>
			<?php endif; ?>

			<?php if ( $current_status !== 'rejected' ) : ?>
				<button type="button" class="button resp-reject-btn" data-id="<?php echo esc_attr( $post_id ); ?>">
					<?php esc_html_e( 'Reject', 'realestate-submit-property' ); ?>
				</button>
			<?php endif; ?>

			<button type="button" class="button button-link-delete resp-delete-btn" data-id="<?php echo esc_attr( $post_id ); ?>">
				<?php esc_html_e( 'Delete', 'realestate-submit-property' ); ?>
			</button>
		</div>

		<div class="resp-reject-form" style="display:none;">
			<label for="reject_reason"><strong><?php esc_html_e( 'Rejection Reason:', 'realestate-submit-property' ); ?></strong></label>
			<textarea id="reject_reason" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Provide a reason for rejection...', 'realestate-submit-property' ); ?>"></textarea>
			<br>
			<button type="button" class="button resp-confirm-reject-btn"><?php esc_html_e( 'Confirm Rejection', 'realestate-submit-property' ); ?></button>
			<button type="button" class="button resp-cancel-reject-btn"><?php esc_html_e( 'Cancel', 'realestate-submit-property' ); ?></button>
		</div>

		<div class="resp-action-result" style="display:none;"></div>
	</div>

	<div class="resp-detail-meta">
		<p><strong><?php esc_html_e( 'Submitted:', 'realestate-submit-property' ); ?></strong> <?php echo esc_html( $data['submission_date'] ); ?></p>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	var $actions = $('.resp-detail-actions');
	var nonce = $actions.data('nonce');
	var postId = $actions.data('id');

	$('.resp-reject-btn').on('click', function() {
		$('.resp-reject-form').slideToggle();
	});

	$('.resp-cancel-reject-btn').on('click', function() {
		$('.resp-reject-form').slideUp();
	});

	$('.resp-approve-btn').on('click', function() {
		var btn = $(this);
		btn.prop('disabled', true).text('<?php echo esc_js( __( 'Processing...', 'realestate-submit-property' ) ); ?>');
		$.post(ajaxurl, {
			action: 'resp_approve_submission',
			post_id: postId,
			admin_nonce: nonce
		}, function(response) {
			var $result = $('.resp-action-result').show();
			if (response.success) {
				$result.html('<div class="notice notice-success inline"><p>' + response.data.message + ' <a href="' + response.data.edit_url + '" target="_blank"><?php echo esc_js( __( 'Edit Property', 'realestate-submit-property' ) ); ?></a></p></div>');
				btn.text('<?php echo esc_js( __( 'Approved', 'realestate-submit-property' ) ); ?>');
			} else {
				$result.html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>');
				btn.prop('disabled', false).text('<?php echo esc_js( __( 'Approve & Create Listing', 'realestate-submit-property' ) ); ?>');
			}
		});
	});

	$('.resp-confirm-reject-btn').on('click', function() {
		var reason = $('#reject_reason').val().trim();
		if (!reason) {
			alert('<?php echo esc_js( __( 'Please provide a rejection reason.', 'realestate-submit-property' ) ); ?>');
			return;
		}
		var btn = $(this);
		btn.prop('disabled', true);
		$.post(ajaxurl, {
			action: 'resp_reject_submission',
			post_id: postId,
			reason: reason,
			admin_nonce: nonce
		}, function(response) {
			var $result = $('.resp-action-result').show();
			if (response.success) {
				$result.html('<div class="notice notice-success inline"><p>' + response.data.message + '</p></div>');
				$('.resp-reject-form').slideUp();
			} else {
				$result.html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>');
				btn.prop('disabled', false);
			}
		});
	});

	$('.resp-delete-btn').on('click', function() {
		if (!confirm('<?php echo esc_js( __( 'Are you sure you want to delete this submission? This cannot be undone.', 'realestate-submit-property' ) ); ?>')) {
			return;
		}
		var btn = $(this);
		btn.prop('disabled', true);
		$.post(ajaxurl, {
			action: 'resp_delete_submission',
			post_id: postId,
			admin_nonce: nonce
		}, function(response) {
			if (response.success) {
				window.location.href = 'edit.php?post_type=property_submission';
			} else {
				$('.resp-action-result').show().html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>');
				btn.prop('disabled', false);
			}
		});
	});
});
</script>
