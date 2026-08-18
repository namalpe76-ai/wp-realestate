<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

if ( ! $id ) {
    echo '<div class="notice notice-error"><p>' . esc_html__( 'Invalid enquiry ID.', 'realestate-enquiry' ) . '</p></div>';
    return;
}

$enquiry = $this->storage->get( $id );

if ( ! $enquiry ) {
    echo '<div class="notice notice-error"><p>' . esc_html__( 'Enquiry not found.', 'realestate-enquiry' ) . '</p></div>';
    return;
}

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

$contact_labels = array(
    'phone'    => __( 'Phone', 'realestate-enquiry' ),
    'email'    => __( 'Email', 'realestate-enquiry' ),
    'whatsapp' => __( 'WhatsApp', 'realestate-enquiry' ),
    'any'      => __( 'Any', 'realestate-enquiry' ),
);

$status_info  = isset( $status_labels[ $enquiry->status ] ) ? $status_labels[ $enquiry->status ] : array( 'label' => $enquiry->status, 'color' => '#6c757d' );
$type_label   = isset( $enquiry_type_labels[ $enquiry->enquiry_type ] ) ? $enquiry_type_labels[ $enquiry->enquiry_type ] : $enquiry->enquiry_type;
$contact_text = isset( $contact_labels[ $enquiry->contact_method ] ) ? $contact_labels[ $enquiry->enquiry_type ] : $enquiry->contact_method;

$back_url = admin_url( 'admin.php?page=realestate-enquiries' );
?>

<div class="wrap ree-admin-wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Enquiry Details', 'realestate-enquiry' ); ?></h1>
    <a href="<?php echo esc_url( $back_url ); ?>" class="page-title-action"><?php esc_html_e( 'Back to List', 'realestate-enquiry' ); ?></a>
    <hr class="wp-header-end" />

    <div class="ree-detail-container">
        <div class="ree-detail-main">
            <div class="postbox">
                <h2 class="hndle"><span><?php esc_html_e( 'Contact Information', 'realestate-enquiry' ); ?></span></h2>
                <div class="inside">
                    <table class="form-table ree-detail-table">
                        <tr>
                            <th><?php esc_html_e( 'Full Name', 'realestate-enquiry' ); ?></th>
                            <td><strong><?php echo esc_html( $enquiry->full_name ); ?></strong></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Email', 'realestate-enquiry' ); ?></th>
                            <td>
                                <a href="mailto:<?php echo esc_attr( $enquiry->email ); ?>"><?php echo esc_html( $enquiry->email ); ?></a>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Telephone', 'realestate-enquiry' ); ?></th>
                            <td>
                                <a href="tel:<?php echo esc_attr( $enquiry->telephone ); ?>"><?php echo esc_html( $enquiry->telephone ); ?></a>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Preferred Contact Method', 'realestate-enquiry' ); ?></th>
                            <td><?php echo esc_html( $contact_text ); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="postbox">
                <h2 class="hndle"><span><?php esc_html_e( 'Enquiry Details', 'realestate-enquiry' ); ?></span></h2>
                <div class="inside">
                    <table class="form-table ree-detail-table">
                        <tr>
                            <th><?php esc_html_e( 'Enquiry Type', 'realestate-enquiry' ); ?></th>
                            <td><?php echo esc_html( $type_label ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Property ID', 'realestate-enquiry' ); ?></th>
                            <td><?php echo esc_html( $enquiry->property_id ? $enquiry->property_id : '—' ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Property Name', 'realestate-enquiry' ); ?></th>
                            <td><?php echo esc_html( $enquiry->property_name ? $enquiry->property_name : '—' ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Preferred Viewing Date', 'realestate-enquiry' ); ?></th>
                            <td><?php echo esc_html( $enquiry->viewing_date ? wp_date( 'F j, Y', strtotime( $enquiry->viewing_date ) ) : '—' ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Message', 'realestate-enquiry' ); ?></th>
                            <td>
                                <div class="ree-message-box">
                                    <?php echo nl2br( esc_html( $enquiry->message ) ); ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="postbox">
                <h2 class="hndle"><span><?php esc_html_e( 'Meta', 'realestate-enquiry' ); ?></span></h2>
                <div class="inside">
                    <table class="form-table ree-detail-table">
                        <tr>
                            <th><?php esc_html_e( 'Created', 'realestate-enquiry' ); ?></th>
                            <td><?php echo esc_html( wp_date( 'F j, Y \a\t g:i A', strtotime( $enquiry->created_at ) ) ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Last Updated', 'realestate-enquiry' ); ?></th>
                            <td><?php echo esc_html( wp_date( 'F j, Y \a\t g:i A', strtotime( $enquiry->updated_at ) ) ); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="ree-detail-sidebar">
            <div class="postbox">
                <h2 class="hndle"><span><?php esc_html_e( 'Status', 'realestate-enquiry' ); ?></span></h2>
                <div class="inside">
                    <div class="ree-current-status" style="background-color:<?php echo esc_attr( $status_info['color'] ); ?>; color:#fff; padding:10px 16px; border-radius:4px; text-align:center; font-weight:bold; font-size:14px; margin-bottom:12px;">
                        <?php echo esc_html( $status_info['label'] ); ?>
                    </div>

                    <form id="ree-status-form" class="ree-status-form">
                        <input type="hidden" name="id" value="<?php echo esc_attr( $enquiry->id ); ?>" />
                        <select name="status" class="ree-status-select" style="width:100%;">
                            <?php foreach ( $status_labels as $key => $info ) : ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $enquiry->status, $key ); ?>>
                                    <?php echo esc_html( $info['label'] ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <br /><br />
                        <button type="submit" class="button button-primary" style="width:100%;"><?php esc_html_e( 'Update Status', 'realestate-enquiry' ); ?></button>
                    </form>
                </div>
            </div>

            <div class="postbox">
                <h2 class="hndle"><span><?php esc_html_e( 'Quick Actions', 'realestate-enquiry' ); ?></span></h2>
                <div class="inside">
                    <p>
                        <a href="mailto:<?php echo esc_attr( $enquiry->email ); ?>?subject=<?php echo rawurlencode( 'Re: Your enquiry with 11AA Real Estate' ); ?>" class="button" style="width:100%; text-align:center; margin-bottom:8px;">
                            <?php esc_html_e( 'Reply via Email', 'realestate-enquiry' ); ?>
                        </a>
                    </p>
                    <p>
                        <a href="tel:<?php echo esc_attr( $enquiry->telephone ); ?>" class="button" style="width:100%; text-align:center; margin-bottom:8px;">
                            <?php esc_html_e( 'Call Customer', 'realestate-enquiry' ); ?>
                        </a>
                    </p>
                    <p>
                        <button type="button" class="button button-link-delete ree-delete-btn" data-id="<?php echo esc_attr( $enquiry->id ); ?>" style="width:100%; text-align:center;">
                            <?php esc_html_e( 'Delete Enquiry', 'realestate-enquiry' ); ?>
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ree-detail-container {
    display: flex;
    gap: 20px;
    margin-top: 16px;
}
.ree-detail-main {
    flex: 1;
}
.ree-detail-sidebar {
    width: 280px;
    flex-shrink: 0;
}
.ree-detail-sidebar .postbox {
    margin-bottom: 16px;
}
.ree-detail-table th {
    width: 200px;
    font-weight: 600;
    color: #1d2327;
}
.ree-detail-table td {
    color: #3c434a;
}
.ree-message-box {
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 12px;
    line-height: 1.6;
    max-height: 300px;
    overflow-y: auto;
    white-space: pre-wrap;
}
.ree-status-select {
    height: auto !important;
}
@media (max-width: 960px) {
    .ree-detail-container {
        flex-direction: column;
    }
    .ree-detail-sidebar {
        width: 100%;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    $('#ree-status-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = {
            action: 'ree_update_status',
            nonce: ree_admin.nonce,
            id: form.find('input[name="id"]').val(),
            status: form.find('select[name="status"]').val()
        };
        $.post(ree_admin.ajax_url, data, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data.message || ree_admin.i18n.error);
            }
        }).fail(function() {
            alert(ree_admin.i18n.error);
        });
    });
});
</script>
