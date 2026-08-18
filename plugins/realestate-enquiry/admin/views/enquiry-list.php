<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
$current_search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$current_page   = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$orderby        = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'created_at';
$order          = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'DESC';

$result = $this->storage->get_all( array(
    'per_page' => 20,
    'page'     => $current_page,
    'orderby'  => $orderby,
    'order'    => $order,
    'status'   => $current_status,
    'search'   => $current_search,
) );

$status_labels = array(
    'new'                => array( 'label' => __( 'New', 'realestate-enquiry' ), 'color' => '#28a745' ),
    'contacted'          => array( 'label' => __( 'Contacted', 'realestate-enquiry' ), 'color' => '#007bff' ),
    'viewing_scheduled'  => array( 'label' => __( 'Viewing Scheduled', 'realestate-enquiry' ), 'color' => '#fd7e14' ),
    'negotiation'        => array( 'label' => __( 'Negotiation', 'realestate-enquiry' ), 'color' => '#6f42c1' ),
    'closed'             => array( 'label' => __( 'Closed', 'realestate-enquiry' ), 'color' => '#6c757d' ),
);

$enquiry_type_labels = array(
    'property_information' => __( 'Property Information', 'realestate-enquiry' ),
    'schedule_viewing'     => __( 'Schedule Viewing', 'realestate-enquiry' ),
    'purchase'             => __( 'Purchase', 'realestate-enquiry' ),
    'rental'               => __( 'Rental', 'realestate-enquiry' ),
    'sell_my_property'     => __( 'Sell My Property', 'realestate-enquiry' ),
    'general_enquiry'      => __( 'General Enquiry', 'realestate-enquiry' ),
);
?>

<div class="wrap ree-admin-wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Real Estate Enquiries', 'realestate-enquiry' ); ?></h1>
    <a href="#" class="page-title-action ree-export-btn"><?php esc_html_e( 'Export CSV', 'realestate-enquiry' ); ?></a>

    <?php if ( ! empty( $current_search ) || ! empty( $current_status ) ) : ?>
        <span class="subtitle">
            <?php
            if ( ! empty( $current_search ) ) {
                printf(
                    esc_html__( 'Search results for: %s', 'realestate-enquiry' ),
                    '<strong>' . esc_html( $current_search ) . '</strong>'
                );
            }
            if ( ! empty( $current_status ) && isset( $status_labels[ $current_status ] ) ) {
                printf(
                    esc_html__( ' | Status: %s', 'realestate-enquiry' ),
                    esc_html( $status_labels[ $current_status ]['label'] )
                );
            }
            ?>
        </span>
    <?php endif; ?>

    <hr class="wp-header-end" />

    <div class="ree-admin-dashboard">
        <div class="ree-stat-card" style="border-left-color:#007bff;">
            <span class="ree-stat-number"><?php echo esc_html( $this->storage->get_total_count() ); ?></span>
            <span class="ree-stat-label"><?php esc_html_e( 'Total Enquiries', 'realestate-enquiry' ); ?></span>
        </div>
        <?php foreach ( $status_labels as $key => $info ) : ?>
            <div class="ree-stat-card" style="border-left-color:<?php echo esc_attr( $info['color'] ); ?>;">
                <span class="ree-stat-number"><?php echo esc_html( $this->storage->count_by_status( $key ) ); ?></span>
                <span class="ree-stat-label"><?php echo esc_html( $info['label'] ); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="ree-admin-controls">
        <div class="ree-filter-group">
            <form method="get" class="ree-filter-form" style="display:inline;">
                <input type="hidden" name="page" value="realestate-enquiries" />
                <select name="status" class="ree-status-filter">
                    <option value=""><?php esc_html_e( 'All Statuses', 'realestate-enquiry' ); ?></option>
                    <?php foreach ( $status_labels as $key => $info ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_status, $key ); ?>>
                            <?php echo esc_html( $info['label'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="search" name="s" value="<?php echo esc_attr( $current_search ); ?>" placeholder="<?php esc_attr_e( 'Search name, email, property...', 'realestate-enquiry' ); ?>" class="ree-search-input" />
                <button type="submit" class="button"><?php esc_html_e( 'Filter', 'realestate-enquiry' ); ?></button>
                <?php if ( ! empty( $current_search ) || ! empty( $current_status ) ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=realestate-enquiries' ) ); ?>" class="button"><?php esc_html_e( 'Clear', 'realestate-enquiry' ); ?></a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <form id="ree-bulk-form" method="post">
        <?php wp_nonce_field( 'ree_bulk_action', 'ree_bulk_nonce' ); ?>

        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <select name="bulk_action" class="ree-bulk-action-select">
                    <option value=""><?php esc_html_e( 'Bulk Actions', 'realestate-enquiry' ); ?></option>
                    <option value="delete"><?php esc_html_e( 'Delete', 'realestate-enquiry' ); ?></option>
                    <option value="contacted"><?php esc_html_e( 'Mark as Contacted', 'realestate-enquiry' ); ?></option>
                    <option value="viewing_scheduled"><?php esc_html_e( 'Mark as Viewing Scheduled', 'realestate-enquiry' ); ?></option>
                    <option value="negotiation"><?php esc_html_e( 'Mark as Negotiation', 'realestate-enquiry' ); ?></option>
                    <option value="closed"><?php esc_html_e( 'Mark as Closed', 'realestate-enquiry' ); ?></option>
                </select>
                <button type="button" class="button ree-bulk-apply"><?php esc_html_e( 'Apply', 'realestate-enquiry' ); ?></button>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped ree-enquiries-table">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column" style="width:30px;">
                        <input type="checkbox" id="ree-select-all" />
                    </td>
                    <th class="column-id" style="width:50px;">
                        <a href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'id', 'order' => 'ASC' === $order ? 'DESC' : 'ASC' ) ) ); ?>">
                            <?php esc_html_e( 'ID', 'realestate-enquiry' ); ?>
                        </a>
                    </th>
                    <th class="column-name"><?php esc_html_e( 'Name', 'realestate-enquiry' ); ?></th>
                    <th class="column-email"><?php esc_html_e( 'Email', 'realestate-enquiry' ); ?></th>
                    <th class="column-phone" style="width:140px;"><?php esc_html_e( 'Phone', 'realestate-enquiry' ); ?></th>
                    <th class="column-property"><?php esc_html_e( 'Property', 'realestate-enquiry' ); ?></th>
                    <th class="column-type" style="width:150px;"><?php esc_html_e( 'Enquiry Type', 'realestate-enquiry' ); ?></th>
                    <th class="column-date" style="width:140px;">
                        <a href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'created_at', 'order' => 'ASC' === $order ? 'DESC' : 'ASC' ) ) ); ?>">
                            <?php esc_html_e( 'Date', 'realestate-enquiry' ); ?>
                            <?php if ( 'created_at' === $orderby ) : ?>
                                <span class="sorting-indicator <?php echo 'ASC' === $order ? 'asc' : 'desc'; ?>"></span>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="column-status" style="width:130px;"><?php esc_html_e( 'Status', 'realestate-enquiry' ); ?></th>
                    <th class="column-actions" style="width:100px;"><?php esc_html_e( 'Actions', 'realestate-enquiry' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $result['items'] ) ) : ?>
                    <tr>
                        <td colspan="10">
                            <div class="ree-no-results">
                                <?php esc_html_e( 'No enquiries found.', 'realestate-enquiry' ); ?>
                            </div>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $result['items'] as $enquiry ) :
                        $status_key  = $enquiry->status;
                        $status_info = isset( $status_labels[ $status_key ] ) ? $status_labels[ $status_key ] : array( 'label' => $status_key, 'color' => '#6c757d' );
                        $type_label  = isset( $enquiry_type_labels[ $enquiry->enquiry_type ] ) ? $enquiry_type_labels[ $enquiry->enquiry_type ] : $enquiry->enquiry_type;
                    ?>
                        <tr class="ree-enquiry-row" data-id="<?php echo esc_attr( $enquiry->id ); ?>">
                            <th class="check-column" scope="row">
                                <input type="checkbox" name="enquiry_ids[]" value="<?php echo esc_attr( $enquiry->id ); ?>" />
                            </th>
                            <td class="column-id">
                                <strong><?php echo esc_html( $enquiry->id ); ?></strong>
                            </td>
                            <td class="column-name">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=realestate-enquiries&action=view&id=' . $enquiry->id ) ); ?>">
                                    <?php echo esc_html( $enquiry->full_name ); ?>
                                </a>
                            </td>
                            <td class="column-email">
                                <a href="mailto:<?php echo esc_attr( $enquiry->email ); ?>"><?php echo esc_html( $enquiry->email ); ?></a>
                            </td>
                            <td class="column-phone">
                                <?php echo esc_html( $enquiry->telephone ); ?>
                            </td>
                            <td class="column-property">
                                <?php
                                $prop_display = '';
                                if ( ! empty( $enquiry->property_id ) ) {
                                    $prop_display = $enquiry->property_id;
                                }
                                if ( ! empty( $enquiry->property_name ) ) {
                                    $prop_display .= ( $prop_display ? ' - ' : '' ) . $enquiry->property_name;
                                }
                                echo esc_html( $prop_display ? $prop_display : '—' );
                                ?>
                            </td>
                            <td class="column-type"><?php echo esc_html( $type_label ); ?></td>
                            <td class="column-date">
                                <?php echo esc_html( wp_date( 'M j, Y', strtotime( $enquiry->created_at ) ) ); ?>
                            </td>
                            <td class="column-status">
                                <select class="ree-quick-status" data-id="<?php echo esc_attr( $enquiry->id ); ?>">
                                    <?php foreach ( $status_labels as $skey => $sinfo ) : ?>
                                        <option value="<?php echo esc_attr( $skey ); ?>" <?php selected( $status_key, $skey ); ?>>
                                            <?php echo esc_html( $sinfo['label'] ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="column-actions">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=realestate-enquiries&action=view&id=' . $enquiry->id ) ); ?>" class="button button-small" title="<?php esc_attr_e( 'View', 'realestate-enquiry' ); ?>">
                                    <span class="dashicons dashicons-visibility"></span>
                                </a>
                                <button type="button" class="button button-small ree-delete-btn" data-id="<?php echo esc_attr( $enquiry->id ); ?>" title="<?php esc_attr_e( 'Delete', 'realestate-enquiry' ); ?>">
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <select name="bulk_action_bottom" class="ree-bulk-action-select" id="ree-bulk-action-bottom">
                    <option value=""><?php esc_html_e( 'Bulk Actions', 'realestate-enquiry' ); ?></option>
                    <option value="delete"><?php esc_html_e( 'Delete', 'realestate-enquiry' ); ?></option>
                    <option value="contacted"><?php esc_html_e( 'Mark as Contacted', 'realestate-enquiry' ); ?></option>
                    <option value="viewing_scheduled"><?php esc_html_e( 'Mark as Viewing Scheduled', 'realestate-enquiry' ); ?></option>
                    <option value="negotiation"><?php esc_html_e( 'Mark as Negotiation', 'realestate-enquiry' ); ?></option>
                    <option value="closed"><?php esc_html_e( 'Mark as Closed', 'realestate-enquiry' ); ?></option>
                </select>
                <button type="button" class="button ree-bulk-apply-bottom"><?php esc_html_e( 'Apply', 'realestate-enquiry' ); ?></button>
            </div>
        </div>
    </form>

    <?php if ( $result['total_pages'] > 1 ) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <span class="displaying-num"><?php
                    printf(
                        esc_html( _n( '%s item', '%s items', $result['total'], 'realestate-enquiry' ) ),
                        number_format_i18n( $result['total'] )
                    );
                ?></span>
                <span class="pagination-links">
                    <?php if ( $current_page > 1 ) : ?>
                        <a class="prev-page button" href="<?php echo esc_url( add_query_arg( 'paged', $current_page - 1 ) ); ?>">‹</a>
                    <?php endif; ?>

                    <?php
                    $start_page = max( 1, $current_page - 2 );
                    $end_page   = min( $result['total_pages'], $current_page + 2 );
                    for ( $i = $start_page; $i <= $end_page; $i++ ) :
                    ?>
                        <?php if ( $i === $current_page ) : ?>
                            <span class="paging-num"><span class="tablenav-paging-text"><?php echo esc_html( $i ); ?> of <span class="total-pages"><?php echo esc_html( $result['total_pages'] ); ?></span></span></span>
                        <?php else : ?>
                            <a class="page-numbers" href="<?php echo esc_url( add_query_arg( 'paged', $i ) ); ?>"><?php echo esc_html( $i ); ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ( $current_page < $result['total_pages'] ) : ?>
                        <a class="next-page button" href="<?php echo esc_url( add_query_arg( 'paged', $current_page + 1 ) ); ?>">›</a>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.ree-admin-dashboard {
    display: flex;
    gap: 12px;
    margin: 16px 0;
    flex-wrap: wrap;
}
.ree-stat-card {
    background: #fff;
    border-left: 4px solid #007bff;
    padding: 12px 16px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    min-width: 120px;
}
.ree-stat-number {
    display: block;
    font-size: 24px;
    font-weight: 700;
    color: #1d2327;
}
.ree-stat-label {
    display: block;
    font-size: 12px;
    color: #646970;
    margin-top: 2px;
}
.ree-admin-controls {
    margin-bottom: 12px;
}
.ree-filter-form {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}
.ree-search-input {
    min-width: 250px;
}
.ree-no-results {
    text-align: center;
    padding: 40px 20px;
    color: #787c82;
    font-style: italic;
}
.ree-enquiries-table .column-id { text-align: center; }
.column-actions .button { margin: 0 2px; padding: 0 6px; }
.column-actions .dashicons { font-size: 16px; width: 16px; height: 16px; line-height: 16px; }
</style>
