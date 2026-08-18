<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Property_Email {

    public function __construct() {
        add_action( 'realestate_property_published', array( $this, 'property_published_notification' ), 10, 2 );
        add_action( 'realestate_property_submitted', array( $this, 'property_submitted_notification' ), 10, 2 );
        add_action( 'realestate_enquiry_received', array( $this, 'enquiry_notification' ), 10, 2 );
    }

    /**
     * Get the admin email for property notifications.
     */
    private function get_admin_email() {
        return get_option( 'admin_email' );
    }

    /**
     * Get the site name.
     */
    private function get_site_name() {
        return get_bloginfo( 'name' );
    }

    /**
     * Send admin notification when a property is published.
     */
    public function property_published_notification( $post_id, $post ) {
        $admin_email = $this->get_admin_email();
        $site_name   = $this->get_site_name();
        $title       = get_the_title( $post );
        $edit_link   = get_edit_post_link( $post_id, 'raw' );
        $view_link   = get_the_permalink( $post_id );
        $price       = get_post_meta( $post_id, '_property_price', true );
        $currency    = get_post_meta( $post_id, '_property_currency', true );
        $display_id  = get_post_meta( $post_id, '_property_display_id', true );

        $subject = sprintf(
            /* translators: %s: property title */
            __( '[%s] New Property Published: %s', 'realestate-core' ),
            $site_name,
            $title
        );

        $body = $this->get_email_template( array(
            'title'       => sprintf( __( 'New Property Published on %s', 'realestate-core' ), $site_name ),
            'heading'     => sprintf( __( 'A new property has been published: %s', 'realestate-core' ), $title ),
            'content'     => sprintf(
                '<p><strong>%s</strong></p>' .
                '<table style="width:100%;border-collapse:collapse;margin:15px 0;">' .
                '<tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">' . esc_html__( 'Property ID', 'realestate-core' ) . '</td><td style="padding:8px;border:1px solid #ddd;">%s</td></tr>' .
                '<tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">' . esc_html__( 'Title', 'realestate-core' ) . '</td><td style="padding:8px;border:1px solid #ddd;">%s</td></tr>' .
                '<tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">' . esc_html__( 'Price', 'realestate-core' ) . '</td><td style="padding:8px;border:1px solid #ddd;">%s %s</td></tr>' .
                '</table>',
                esc_html( $post->post_content ),
                esc_html( $display_id ),
                esc_html( $title ),
                esc_html( $currency ),
                esc_html( number_format( (float) $price ) )
            ),
            'button_text' => __( 'View Property', 'realestate-core' ),
            'button_url'  => $view_link,
            'secondary_button_text' => __( 'Edit Property', 'realestate-core' ),
            'secondary_button_url'  => $edit_link,
        ) );

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            sprintf( 'From: %s <%s>', $site_name, $admin_email ),
        );

        wp_mail( $admin_email, $subject, $body, $headers );
    }

    /**
     * Send customer acknowledgement when property is submitted.
     */
    public function property_submitted_notification( $post_id, $post ) {
        $customer_email = get_post_meta( $post_id, '_enquiry_email', true );
        if ( empty( $customer_email ) ) {
            return;
        }

        $site_name = $this->get_site_name();
        $title     = get_the_title( $post );

        $subject = sprintf(
            /* translators: %s: site name */
            __( 'Thank you for your property submission on %s', 'realestate-core' ),
            $site_name
        );

        $body = $this->get_email_template( array(
            'title'       => __( 'Property Submission Received', 'realestate-core' ),
            'heading'     => sprintf( __( 'Thank you for submitting your property: %s', 'realestate-core' ), $title ),
            'content'     => sprintf(
                '<p>%s</p>' .
                '<p>%s</p>' .
                '<p>%s</p>',
                esc_html__( 'We have received your property submission and our team will review it shortly.', 'realestate-core' ),
                sprintf(
                    esc_html__( 'Property Title: %s', 'realestate-core' ),
                    esc_html( $title )
                ),
                esc_html__( 'You will receive another notification once your property has been reviewed and published.', 'realestate-core' )
            ),
            'button_text' => __( 'View Property', 'realestate-core' ),
            'button_url'  => get_the_permalink( $post_id ),
        ) );

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            sprintf( 'From: %s <%s>', $site_name, $this->get_admin_email() ),
        );

        wp_mail( $customer_email, $subject, $body, $headers );
    }

    /**
     * Send enquiry notification.
     */
    public function enquiry_notification( $enquiry_data, $property_id ) {
        $admin_email = $this->get_admin_email();
        $site_name   = $this->get_site_name();

        $name    = isset( $enquiry_data['name'] ) ? sanitize_text_field( $enquiry_data['name'] ) : '';
        $email   = isset( $enquiry_data['email'] ) ? sanitize_email( $enquiry_data['email'] ) : '';
        $phone   = isset( $enquiry_data['phone'] ) ? sanitize_text_field( $enquiry_data['phone'] ) : '';
        $message = isset( $enquiry_data['message'] ) ? sanitize_textarea_field( $enquiry_data['message'] ) : '';

        $property_title = get_the_title( $property_id );
        $property_url   = get_the_permalink( $property_id );

        // Notify admin.
        $subject = sprintf(
            /* translators: 1: site name, 2: property title */
            __( '[%1$s] New Enquiry for: %2$s', 'realestate-core' ),
            $site_name,
            $property_title
        );

        $body = $this->get_email_template( array(
            'title'       => __( 'New Property Enquiry', 'realestate-core' ),
            'heading'     => sprintf( __( 'Someone enquired about: %s', 'realestate-core' ), $property_title ),
            'content'     => sprintf(
                '<table style="width:100%;border-collapse:collapse;margin:15px 0;">' .
                '<tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">' . esc_html__( 'Name', 'realestate-core' ) . '</td><td style="padding:8px;border:1px solid #ddd;">%s</td></tr>' .
                '<tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">' . esc_html__( 'Email', 'realestate-core' ) . '</td><td style="padding:8px;border:1px solid #ddd;"><a href="mailto:%s">%s</a></td></tr>' .
                '<tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">' . esc_html__( 'Phone', 'realestate-core' ) . '</td><td style="padding:8px;border:1px solid #ddd;"><a href="tel:%s">%s</a></td></tr>' .
                '<tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">' . esc_html__( 'Message', 'realestate-core' ) . '</td><td style="padding:8px;border:1px solid #ddd;">%s</td></tr>' .
                '</table>',
                esc_html( $name ),
                esc_attr( $email ),
                esc_html( $email ),
                esc_attr( $phone ),
                esc_html( $phone ),
                nl2br( esc_html( $message ) )
            ),
            'button_text' => __( 'View Property', 'realestate-core' ),
            'button_url'  => $property_url,
        ) );

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            sprintf( 'From: %s <%s>', $site_name, $admin_email ),
            sprintf( 'Reply-To: %s <%s>', $name, $email ),
        );

        wp_mail( $admin_email, $subject, $body, $headers );

        // Auto-reply to customer.
        if ( ! empty( $email ) ) {
            $reply_subject = sprintf(
                /* translators: 1: site name, 2: property title */
                __( '[%1$s] We received your enquiry about: %2$s', 'realestate-core' ),
                $site_name,
                $property_title
            );

            $reply_body = $this->get_email_template( array(
                'title'       => __( 'Enquiry Received', 'realestate-core' ),
                'heading'     => sprintf( __( 'Thank you for your enquiry about: %s', 'realestate-core' ), $property_title ),
                'content'     => sprintf(
                    '<p>%s</p>' .
                    '<p>%s</p>' .
                    '<p>%s</p>',
                    esc_html__( 'We have received your enquiry and will get back to you shortly.', 'realestate-core' ),
                    sprintf(
                        esc_html__( 'Name: %s', 'realestate-core' ),
                        esc_html( $name )
                    ),
                    esc_html__( 'Our team will review your enquiry and respond as soon as possible.', 'realestate-core' )
                ),
                'button_text' => __( 'View Property', 'realestate-core' ),
                'button_url'  => $property_url,
            ) );

            $reply_headers = array(
                'Content-Type: text/html; charset=UTF-8',
                sprintf( 'From: %s <%s>', $site_name, $admin_email ),
            );

            wp_mail( $email, $reply_subject, $reply_body, $reply_headers );
        }
    }

    /**
     * Get HTML email template.
     */
    private function get_email_template( $args ) {
        $defaults = array(
            'title'                  => '',
            'heading'                => '',
            'content'                => '',
            'button_text'            => '',
            'button_url'             => '',
            'secondary_button_text'  => '',
            'secondary_button_url'   => '',
        );
        $args = wp_parse_args( $args, $defaults );

        ob_start();
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px 0;">
                <tr>
                    <td align="center">
                        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

                            <!-- Header -->
                            <tr>
                                <td style="background-color:#1a5276;padding:30px 40px;text-align:center;">
                                    <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:700;">
                                        <?php echo esc_html( $this->get_site_name() ); ?>
                                    </h1>
                                </td>
                            </tr>

                            <!-- Title -->
                            <tr>
                                <td style="padding:30px 40px 10px;">
                                    <h2 style="color:#1a5276;margin:0;font-size:20px;">
                                        <?php echo esc_html( $args['title'] ); ?>
                                    </h2>
                                </td>
                            </tr>

                            <!-- Heading -->
                            <?php if ( $args['heading'] ) : ?>
                                <tr>
                                    <td style="padding:0 40px 15px;">
                                        <p style="color:#333333;margin:0;font-size:15px;line-height:1.6;">
                                            <?php echo wp_kses_post( $args['heading'] ); ?>
                                        </p>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <!-- Content -->
                            <tr>
                                <td style="padding:0 40px 25px;">
                                    <div style="color:#555555;font-size:14px;line-height:1.7;">
                                        <?php echo wp_kses_post( $args['content'] ); ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- Primary Button -->
                            <?php if ( $args['button_text'] && $args['button_url'] ) : ?>
                                <tr>
                                    <td style="padding:0 40px 15px;" align="center">
                                        <a href="<?php echo esc_url( $args['button_url'] ); ?>" style="display:inline-block;background-color:#1a5276;color:#ffffff;text-decoration:none;padding:12px 30px;border-radius:5px;font-size:14px;font-weight:600;">
                                            <?php echo esc_html( $args['button_text'] ); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <!-- Secondary Button -->
                            <?php if ( $args['secondary_button_text'] && $args['secondary_button_url'] ) : ?>
                                <tr>
                                    <td style="padding:0 40px 25px;" align="center">
                                        <a href="<?php echo esc_url( $args['secondary_button_url'] ); ?>" style="display:inline-block;background-color:#ffffff;color:#1a5276;text-decoration:none;padding:10px 25px;border-radius:5px;font-size:13px;font-weight:600;border:2px solid #1a5276;">
                                            <?php echo esc_html( $args['secondary_button_text'] ); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <!-- Footer -->
                            <tr>
                                <td style="background-color:#f8f9fa;padding:25px 40px;border-top:1px solid #eeeeee;text-align:center;">
                                    <p style="color:#999999;margin:0 0 5px;font-size:12px;">
                                        <?php
                                        printf(
                                            /* translators: %s: site name */
                                            esc_html__( '%s &mdash; All rights reserved.', 'realestate-core' ),
                                            esc_html( $this->get_site_name() )
                                        );
                                        ?>
                                    </p>
                                    <p style="color:#999999;margin:0;font-size:12px;">
                                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#1a5276;text-decoration:none;">
                                            <?php echo esc_html( $this->get_site_name() ); ?>
                                        </a>
                                    </p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
