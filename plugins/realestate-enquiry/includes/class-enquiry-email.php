<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Ree_Enquiry_Email {

    private $admin_email;

    private $site_name;

    public function __construct() {
        $this->admin_email = get_option( 'admin_email' );
        $this->site_name   = get_bloginfo( 'name' );
    }

    public function send_admin_notification( $data ) {
        $admin_email = $this->admin_email;

        if ( empty( $admin_email ) ) {
            return false;
        }

        $subject = __( 'New Real Estate Customer Enquiry', 'realestate-enquiry' );

        $enquiry_type_label = $this->get_enquiry_type_label( $data['enquiry_type'] );
        $contact_label      = $this->get_contact_method_label( $data['contact_method'] );
        $status_label       = $this->get_status_label( $data['status'] );

        $message  = $this->get_admin_email_body( $data, $enquiry_type_label, $contact_label, $status_label );

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . $data['full_name'] . ' <' . $data['email'] . '>',
        );

        $result = wp_mail( $admin_email, $subject, $message, $headers );

        if ( ! $result ) {
            error_log( 'Ree_Enquiry_Email: Failed to send admin notification for enquiry.' );
        }

        return $result;
    }

    public function send_customer_acknowledgement( $data ) {
        if ( empty( $data['email'] ) || ! is_email( $data['email'] ) ) {
            return false;
        }

        $subject = __( 'Thank you for contacting 11AA Real Estate', 'realestate-enquiry' );

        $enquiry_type_label = $this->get_enquiry_type_label( $data['enquiry_type'] );

        $message = $this->get_customer_email_body( $data, $enquiry_type_label );

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
        );

        $result = wp_mail( $data['email'], $subject, $message, $headers );

        if ( ! $result ) {
            error_log( 'Ree_Enquiry_Email: Failed to send customer acknowledgement for: ' . sanitize_email( $data['email'] ) );
        }

        return $result;
    }

    private function get_admin_email_body( $data, $enquiry_type_label, $contact_label, $status_label ) {
        $viewing_date = $data['viewing_date'] ? esc_html( $data['viewing_date'] ) : __( 'Not specified', 'realestate-enquiry' );

        $body = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, Helvetica, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden;">
<tr>
<td style="background-color:#1a1a2e; padding:20px 30px;">
<h1 style="color:#d4af37; margin:0; font-size:22px;">' . esc_html__( 'New Customer Enquiry', 'realestate-enquiry' ) . '</h1>
</td>
</tr>
<tr>
<td style="padding:30px;">
<p style="color:#555; margin-top:0;">' . esc_html__( 'A new enquiry has been received through the website.', 'realestate-enquiry' ) . '</p>
<table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e0e0e0; border-radius:4px;">
<tr style="background-color:#f9f9f9;">
<td style="font-weight:bold; width:180px; color:#333;">' . esc_html__( 'Full Name:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">' . esc_html( $data['full_name'] ) . '</td>
</tr>
<tr>
<td style="font-weight:bold; color:#333;">' . esc_html__( 'Email:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;"><a href="mailto:' . esc_attr( $data['email'] ) . '">' . esc_html( $data['email'] ) . '</a></td>
</tr>
<tr style="background-color:#f9f9f9;">
<td style="font-weight:bold; color:#333;">' . esc_html__( 'Telephone:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">' . esc_html( $data['telephone'] ) . '</td>
</tr>
<tr>
<td style="font-weight:bold; color:#333;">' . esc_html__( 'Contact Method:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">' . esc_html( $contact_label ) . '</td>
</tr>';

        if ( ! empty( $data['property_id'] ) || ! empty( $data['property_name'] ) ) {
            $body .= '
<tr style="background-color:#f9f9f9;">
<td style="font-weight:bold; color:#333;">' . esc_html__( 'Property:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">';
            if ( ! empty( $data['property_id'] ) ) {
                $body .= esc_html( $data['property_id'] );
            }
            if ( ! empty( $data['property_id'] ) && ! empty( $data['property_name'] ) ) {
                $body .= ' - ';
            }
            if ( ! empty( $data['property_name'] ) ) {
                $body .= esc_html( $data['property_name'] );
            }
            $body .= '</td>
</tr>';
        }

        $body .= '
<tr>
<td style="font-weight:bold; color:#333;">' . esc_html__( 'Enquiry Type:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">' . esc_html( $enquiry_type_label ) . '</td>
</tr>
<tr style="background-color:#f9f9f9;">
<td style="font-weight:bold; color:#333;">' . esc_html__( 'Viewing Date:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">' . esc_html( $viewing_date ) . '</td>
</tr>
<tr>
<td style="font-weight:bold; color:#333;">' . esc_html__( 'Message:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">' . nl2br( esc_html( $data['message'] ) ) . '</td>
</tr>
<tr style="background-color:#f9f9f9;">
<td style="font-weight:bold; color:#333;">' . esc_html__( 'Date/Time:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">' . esc_html( current_time( 'F j, Y \a\t g:i A' ) ) . '</td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="background-color:#f0f0f0; padding:15px 30px; text-align:center;">
<p style="color:#999; margin:0; font-size:12px;">' . esc_html( $this->site_name ) . ' &mdash; ' . esc_html__( 'Enquiry Management System', 'realestate-enquiry' ) . '</p>
</td>
</tr>
</table>
</td></tr>
</table>
</body>
</html>';

        return $body;
    }

    private function get_customer_email_body( $data, $enquiry_type_label ) {
        $body = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, Helvetica, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden;">
<tr>
<td style="background-color:#1a1a2e; padding:20px 30px;">
<h1 style="color:#d4af37; margin:0; font-size:22px;">' . esc_html__( 'Thank You for Contacting Us', 'realestate-enquiry' ) . '</h1>
</td>
</tr>
<tr>
<td style="padding:30px;">
<p style="color:#555; margin-top:0;">' . sprintf( esc_html__( 'Dear %s,', 'realestate-enquiry' ), esc_html( $data['full_name'] ) ) . '</p>
<p style="color:#555;">' . esc_html__( 'Thank you for reaching out to 11AA Real Estate. We have received your enquiry and our team will review it shortly.', 'realestate-enquiry' ) . '</p>
<table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e0e0e0; border-radius:4px; margin:20px 0;">
<tr style="background-color:#f9f9f9;">
<td style="font-weight:bold; width:180px; color:#333;">' . esc_html__( 'Enquiry Type:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">' . esc_html( $enquiry_type_label ) . '</td>
</tr>';

        if ( ! empty( $data['property_id'] ) || ! empty( $data['property_name'] ) ) {
            $body .= '
<tr>
<td style="font-weight:bold; color:#333;">' . esc_html__( 'Property:', 'realestate-enquiry' ) . '</td>
<td style="color:#555;">';
            if ( ! empty( $data['property_id'] ) ) {
                $body .= esc_html( $data['property_id'] );
            }
            if ( ! empty( $data['property_id'] ) && ! empty( $data['property_name'] ) ) {
                $body .= ' - ';
            }
            if ( ! empty( $data['property_name'] ) ) {
                $body .= esc_html( $data['property_name'] );
            }
            $body .= '</td>
</tr>';
        }

        $body .= '
</table>
<p style="color:#555;">' . esc_html__( 'Our team typically responds within 24-48 business hours. If your enquiry is urgent, please do not hesitate to call us directly.', 'realestate-enquiry' ) . '</p>
<p style="color:#555;">' . esc_html__( 'If you have any additional questions, simply reply to this email.', 'realestate-enquiry' ) . '</p>
<p style="color:#555;">' . esc_html__( 'Warm regards,', 'realestate-enquiry' ) . '<br>
<strong>' . esc_html( $this->site_name ) . '</strong></p>
</td>
</tr>
<tr>
<td style="background-color:#f0f0f0; padding:15px 30px; text-align:center;">
<p style="color:#999; margin:0; font-size:12px;">' . esc_html( $this->site_name ) . ' &mdash; ' . esc_html__( 'Your Trusted Real Estate Partner', 'realestate-enquiry' ) . '</p>
</td>
</tr>
</table>
</td></tr>
</table>
</body>
</html>';

        return $body;
    }

    private function get_enquiry_type_label( $type ) {
        $types = array(
            'property_information' => __( 'Property Information', 'realestate-enquiry' ),
            'schedule_viewing'     => __( 'Schedule Viewing', 'realestate-enquiry' ),
            'purchase'             => __( 'Purchase', 'realestate-enquiry' ),
            'rental'               => __( 'Rental', 'realestate-enquiry' ),
            'sell_my_property'     => __( 'Sell My Property', 'realestate-enquiry' ),
            'general_enquiry'      => __( 'General Enquiry', 'realestate-enquiry' ),
        );

        return isset( $types[ $type ] ) ? $types[ $type ] : $type;
    }

    private function get_contact_method_label( $method ) {
        $methods = array(
            'phone'    => __( 'Phone', 'realestate-enquiry' ),
            'email'    => __( 'Email', 'realestate-enquiry' ),
            'whatsapp' => __( 'WhatsApp', 'realestate-enquiry' ),
            'any'      => __( 'Any', 'realestate-enquiry' ),
        );

        return isset( $methods[ $method ] ) ? $methods[ $method ] : $method;
    }

    private function get_status_label( $status ) {
        $statuses = array(
            'new'                => __( 'New', 'realestate-enquiry' ),
            'contacted'          => __( 'Contacted', 'realestate-enquiry' ),
            'viewing_scheduled'  => __( 'Viewing Scheduled', 'realestate-enquiry' ),
            'negotiation'        => __( 'Negotiation', 'realestate-enquiry' ),
            'closed'             => __( 'Closed', 'realestate-enquiry' ),
        );

        return isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status;
    }
}
