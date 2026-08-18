<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
$paged         = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$per_page      = 20;

$args = array(
	'post_type'      => 'property_submission',
	'posts_per_page' => $per_page,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

if ( ! empty( $current_status ) ) {
	$args['meta_query'] = array(
		array(
			'key'   => '_resp_submission_status',
			'value' => $current_status,
		),
	);
}

$query = new WP_Query( $args );
$statuses = RESP_Submission_Admin::get_status_options();
$all_count = wp_count_posts( 'property_submission' );

$type_labels = array(
	'house' => 'House', 'apartment' => 'Apartment', 'land' => 'Land',
	'commercial' => 'Commercial', 'office' => 'Office', 'shop' => 'Shop',
	'warehouse' => 'Warehouse', 'villa' => 'Villa',
);
?>

<div class="wrap resp-admin-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Property Submissions', 'realestate-submit-property' ); ?></h1>
	<a href="#" id="resp-export-btn" class="page-title-action"><?php esc_html_e( 'Export CSV', 'realestate-submit-property' ); ?></a>

	<ul class="subsubsub">
		<li><a href="edit.php?post_type=property_submission" class="<?php echo empty( $current_status ) ? 'current' : ''; ?>"><?php printf( esc_html__( 'All <span class="count">(%d)</span>', 'realestate-submit-property' ), $all_count->total ?? 0 ); ?></a> |</li>
		<?php foreach ( $statuses as $key => $label ) : ?>
			<li>
				<a href="edit.php?post_type=property_submission&status=<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $current_status === $key ? 'current' : '' ); ?>">
					<?php echo esc_html( $label ); ?>
				</a> |
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ( $query->have_posts() ) : ?>
		<table class="wp-list-table widefat fixed striped resp-submissions-table">
			<thead>
				<tr>
					<th class="column-id" width="60"><?php esc_html_e( 'ID', 'realestate-submit-property' ); ?></th>
					<th><?php esc_html_e( 'Owner', 'realestate-submit-property' ); ?></th>
					<th><?php esc_html_e( 'Type', 'realestate-submit-property' ); ?></th>
					<th><?php esc_html_e( 'Location', 'realestate-submit-property' ); ?></th>
					<th><?php esc_html_e( 'Price (LKR)', 'realestate-submit-property' ); ?></th>
					<th><?php esc_html_e( 'Date', 'realestate-submit-property' ); ?></th>
					<th><?php esc_html_e( 'Status', 'realestate-submit-property' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'realestate-submit-property' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php while ( $query->have_posts() ) : $query->the_post();
					$pid   = get_the_ID();
					$data  = RESP_Submission_Admin::get_submission_data( $pid );
					$status = $data['submission_status'] ?? 'pending_review';
				?>
					<tr data-id="<?php echo esc_attr( $pid ); ?>">
						<td><strong><?php echo esc_html( $pid ); ?></strong></td>
						<td>
							<?php echo esc_html( $data['owner_name'] ); ?><br>
							<small><?php echo esc_html( $data['owner_email'] ); ?></small>
						</td>
						<td><?php echo esc_html( $type_labels[ $data['property_type'] ] ?? $data['property_type'] ); ?></td>
						<td><?php echo esc_html( $data['property_location'] ); ?></td>
						<td><?php echo esc_html( number_format( (float) $data['expected_price'] ) ); ?></td>
						<td><?php echo esc_html( $data['submission_date'] ); ?></td>
						<td>
							<select class="resp-status-select" data-id="<?php echo esc_attr( $pid ); ?>">
								<?php foreach ( $statuses as $skey => $slabel ) : ?>
									<option value="<?php echo esc_attr( $skey ); ?>" <?php selected( $status, $skey ); ?>><?php echo esc_html( $slabel ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
						<td>
							<a href="admin.php?page=resp-submission-detail&id=<?php echo esc_attr( $pid ); ?>" class="button button-small"><?php esc_html_e( 'View', 'realestate-submit-property' ); ?></a>
						</td>
					</tr>
				<?php endwhile; wp_reset_postdata(); ?>
			</tbody>
		</table>

		<?php
		$total_pages = $query->max_num_pages;
		if ( $total_pages > 1 ) :
			$pagination_args = array(
				'total'     => $total_pages,
				'current'   => $paged,
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
			);
		?>
			<div class="tablenav bottom">
				<div class="tablenav-pages">
					<?php echo paginate_links( array(
						'base'    => add_query_arg( 'paged', '%#%' ),
						'format'  => '',
						'current' => $paged,
						'total'   => $total_pages,
					) ); ?>
				</div>
			</div>
		<?php endif; ?>

	<?php else : ?>
		<div class="resp-no-submissions">
			<p><?php esc_html_e( 'No property submissions found.', 'realestate-submit-property' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
	$('#resp-export-btn').on('click', function(e) {
		e.preventDefault();
		var nonce = '<?php echo wp_create_nonce( 'resp_admin_action' ); ?>';
		window.location.href = ajaxurl + '?action=resp_export_submissions&admin_nonce=' + nonce;
	});

	$('.resp-status-select').on('change', function() {
		var select = $(this);
		var postId = select.data('id');
		var status = select.val();
		$.post(ajaxurl, {
			action: 'resp_update_status',
			post_id: postId,
			status: status,
			admin_nonce: '<?php echo wp_create_nonce( 'resp_admin_action' ); ?>'
		});
	});
});
</script>
