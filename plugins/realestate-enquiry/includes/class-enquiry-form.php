<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Ree_Enquiry_Form {

    public function __construct() {
        add_shortcode( 'property_enquiry_form', array( $this, 'render_form' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_ree_submit_enquiry', array( $this, 'ajax_submit' ) );
        add_action( 'wp_ajax_nopriv_ree_submit_enquiry', array( $this, 'ajax_submit' ) );
    }

    public function enqueue_assets() {
        if ( shortcode_exists( 'property_enquiry_form' ) || is_singular() ) {
            wp_enqueue_style(
                'ree-enquiry-form',
                REE_PLUGIN_URL . 'assets/css/enquiry-form.css',
                array(),
                REE_VERSION
            );

            wp_enqueue_script(
                'ree-enquiry-form',
                REE_PLUGIN_URL . 'assets/js/enquiry-form.js',
                array( 'jquery' ),
                REE_VERSION,
                true
            );

            wp_localize_script( 'ree-enquiry-form', 'ree_form', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'ree_enquiry_nonce' ),
                'i18n'     => array(
                    'submitting'   => __( 'Submitting...', 'realestate-enquiry' ),
                    'success_msg'  => __( 'Thank you! Your enquiry has been submitted successfully. We will get back to you shortly.', 'realestate-enquiry' ),
                    'error_msg'    => __( 'Something went wrong. Please try again later.', 'realestate-enquiry' ),
                    'required'     => __( 'This field is required.', 'realestate-enquiry' ),
                    'invalid_email' => __( 'Please enter a valid email address.', 'realestate-enquiry' ),
                    'invalid_phone' => __( 'Please enter a valid phone number.', 'realestate-enquiry' ),
                ),
            ) );
        }
    }

    public function render_form( $atts ) {
        $atts = shortcode_atts( array(
            'property_id'   => '',
            'property_name' => '',
        ), $atts, 'property_enquiry_form' );

        $property_id   = sanitize_text_field( $atts['property_id'] );
        $property_name = sanitize_text_field( $atts['property_name'] );

        if ( empty( $property_id ) && isset( $_GET['property_id'] ) ) {
            $property_id = sanitize_text_field( wp_unslash( $_GET['property_id'] ) );
        }
        if ( empty( $property_name ) && isset( $_GET['property_name'] ) ) {
            $property_name = sanitize_text_field( wp_unslash( $_GET['property_name'] ) );
        }

        $enquiry_types = array(
            'property_information' => __( 'Property Information', 'realestate-enquiry' ),
            'schedule_viewing'     => __( 'Schedule Viewing', 'realestate-enquiry' ),
            'purchase'             => __( 'Purchase', 'realestate-enquiry' ),
            'rental'               => __( 'Rental', 'realestate-enquiry' ),
            'sell_my_property'     => __( 'Sell My Property', 'realestate-enquiry' ),
            'general_enquiry'      => __( 'General Enquiry', 'realestate-enquiry' ),
        );

        ob_start();
        ?>
        <div id="ree-enquiry-wrapper">
            <form id="ree-enquiry-form" class="ree-enquiry-form" novalidate>
                <?php wp_nonce_field( 'ree_enquiry_nonce', 'ree_nonce' ); ?>

                <div class="ree-form-group">
                    <label for="ree-full-name"><?php esc_html_e( 'Full Name', 'realestate-enquiry' ); ?> <span class="ree-required">*</span></label>
                    <input type="text" id="ree-full-name" name="full_name" required maxlength="100" placeholder="<?php esc_attr_e( 'Enter your full name', 'realestate-enquiry' ); ?>" />
                    <span class="ree-error-msg"></span>
                </div>

                <div class="ree-form-group">
                    <label for="ree-email"><?php esc_html_e( 'Email Address', 'realestate-enquiry' ); ?> <span class="ree-required">*</span></label>
                    <input type="email" id="ree-email" name="email" required maxlength="100" placeholder="<?php esc_attr_e( 'Enter your email address', 'realestate-enquiry' ); ?>" />
                    <span class="ree-error-msg"></span>
                </div>

                <div class="ree-form-group">
                    <label for="ree-telephone"><?php esc_html_e( 'Telephone Number', 'realestate-enquiry' ); ?> <span class="ree-required">*</span></label>
                    <input type="tel" id="ree-telephone" name="telephone" required maxlength="30" placeholder="<?php esc_attr_e( 'Enter your phone number', 'realestate-enquiry' ); ?>" />
                    <span class="ree-error-msg"></span>
                </div>

                <div class="ree-form-group">
                    <label><?php esc_html_e( 'Preferred Contact Method', 'realestate-enquiry' ); ?></label>
                    <div class="ree-radio-group">
                        <label class="ree-radio-label">
                            <input type="radio" name="contact_method" value="phone" checked />
                            <?php esc_html_e( 'Phone', 'realestate-enquiry' ); ?>
                        </label>
                        <label class="ree-radio-label">
                            <input type="radio" name="contact_method" value="email" />
                            <?php esc_html_e( 'Email', 'realestate-enquiry' ); ?>
                        </label>
                        <label class="ree-radio-label">
                            <input type="radio" name="contact_method" value="whatsapp" />
                            <?php esc_html_e( 'WhatsApp', 'realestate-enquiry' ); ?>
                        </label>
                        <label class="ree-radio-label">
                            <input type="radio" name="contact_method" value="any" />
                            <?php esc_html_e( 'Any', 'realestate-enquiry' ); ?>
                        </label>
                    </div>
                </div>

                <div class="ree-form-row">
                    <div class="ree-form-group ree-half">
                        <label for="ree-property-id"><?php esc_html_e( 'Property ID', 'realestate-enquiry' ); ?></label>
                        <input type="text" id="ree-property-id" name="property_id" value="<?php echo esc_attr( $property_id ); ?>" maxlength="50" placeholder="<?php esc_attr_e( 'e.g. PRO-001', 'realestate-enquiry' ); ?>" />
                    </div>
                    <div class="ree-form-group ree-half">
                        <label for="ree-property-name"><?php esc_html_e( 'Property Name', 'realestate-enquiry' ); ?></label>
                        <input type="text" id="ree-property-name" name="property_name" value="<?php echo esc_attr( $property_name ); ?>" maxlength="200" placeholder="<?php esc_attr_e( 'e.g. Lakeview Villa', 'realestate-enquiry' ); ?>" />
                    </div>
                </div>

                <div class="ree-form-group">
                    <label for="ree-enquiry-type"><?php esc_html_e( 'Enquiry Type', 'realestate-enquiry' ); ?> <span class="ree-required">*</span></label>
                    <select id="ree-enquiry-type" name="enquiry_type" required>
                        <option value=""><?php esc_html_e( '-- Select Enquiry Type --', 'realestate-enquiry' ); ?></option>
                        <?php foreach ( $enquiry_types as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="ree-error-msg"></span>
                </div>

                <div class="ree-form-group">
                    <label for="ree-viewing-date"><?php esc_html_e( 'Preferred Viewing Date', 'realestate-enquiry' ); ?></label>
                    <input type="date" id="ree-viewing-date" name="viewing_date" />
                </div>

                <div class="ree-form-group">
                    <label for="ree-message"><?php esc_html_e( 'Message', 'realestate-enquiry' ); ?> <span class="ree-required">*</span></label>
                    <textarea id="ree-message" name="message" required rows="5" maxlength="2000" placeholder="<?php esc_attr_e( 'Tell us how we can help you...', 'realestate-enquiry' ); ?>"></textarea>
                    <span class="ree-error-msg"></span>
                </div>

                <!-- Honeypot anti-spam -->
                <div class="ree-hp-field" aria-hidden="true">
                    <label for="ree-website"><?php esc_html_e( 'Website', 'realestate-enquiry' ); ?></label>
                    <input type="text" id="ree-website" name="ree_website" tabindex="-1" autocomplete="off" />
                </div>

                <div class="ree-form-group">
                    <button type="submit" class="ree-submit-btn">
                        <span class="ree-btn-text"><?php esc_html_e( 'Submit Enquiry', 'realestate-enquiry' ); ?></span>
                        <span class="ree-btn-loading" style="display:none;"><?php esc_html_e( 'Submitting...', 'realestate-enquiry' ); ?></span>
                    </button>
                </div>

                <div id="ree-form-messages"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function ajax_submit() {
        check_ajax_referer( 'ree_enquiry_nonce', 'ree_nonce' );

        if ( ! empty( $_POST['ree_website'] ) ) {
            wp_send_json_success( array( 'message' => __( 'Thank you!', 'realestate-enquiry' ) ) );
        }

        $full_name       = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
        $email           = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
        $telephone       = isset( $_POST['telephone'] ) ? sanitize_text_field( wp_unslash( $_POST['telephone'] ) ) : '';
        $contact_method  = isset( $_POST['contact_method'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_method'] ) ) : 'phone';
        $property_id     = isset( $_POST['property_id'] ) ? sanitize_text_field( wp_unslash( $_POST['property_id'] ) ) : '';
        $property_name   = isset( $_POST['property_name'] ) ? sanitize_text_field( wp_unslash( $_POST['property_name'] ) ) : '';
        $enquiry_type    = isset( $_POST['enquiry_type'] ) ? sanitize_text_field( wp_unslash( $_POST['enquiry_type'] ) ) : '';
        $viewing_date    = isset( $_POST['viewing_date'] ) ? sanitize_text_field( wp_unslash( $_POST['viewing_date'] ) ) : '';
        $message         = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

        $errors = $this->validate( $full_name, $email, $telephone, $enquiry_type, $message );

        if ( ! empty( $errors ) ) {
            wp_send_json_error( array( 'errors' => $errors ) );
        }

        $valid_contact_methods = array( 'phone', 'email', 'whatsapp', 'any' );
        if ( ! in_array( $contact_method, $valid_contact_methods, true ) ) {
            $contact_method = 'phone';
        }

        $valid_enquiry_types = array(
            'property_information',
            'schedule_viewing',
            'purchase',
            'rental',
            'sell_my_property',
            'general_enquiry',
        );
        if ( ! in_array( $enquiry_type, $valid_enquiry_types, true ) ) {
            wp_send_json_error( array( 'errors' => array( __( 'Invalid enquiry type.', 'realestate-enquiry' ) ) ) );
        }

        if ( ! empty( $viewing_date ) && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $viewing_date ) ) {
            $viewing_date = '';
        }

        $storage = new Ree_Enquiry_Storage();
        $data    = array(
            'full_name'      => $full_name,
            'email'          => $email,
            'telephone'      => $telephone,
            'contact_method' => $contact_method,
            'property_id'    => $property_id,
            'property_name'  => $property_name,
            'enquiry_type'   => $enquiry_type,
            'viewing_date'   => $viewing_date ? $viewing_date : null,
            'message'        => $message,
            'status'         => 'new',
        );

        $enquiry_id = $storage->insert( $data );

        if ( is_wp_error( $enquiry_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Failed to save your enquiry. Please try again.', 'realestate-enquiry' ) ) );
        }

        $email_handler = new Ree_Enquiry_Email();
        $email_handler->send_admin_notification( $data );
        $email_handler->send_customer_acknowledgement( $data );

        wp_send_json_success( array(
            'message' => __( 'Thank you! Your enquiry has been submitted successfully. We will get back to you shortly.', 'realestate-enquiry' ),
        ) );
    }

    private function validate( $full_name, $email, $telephone, $enquiry_type, $message ) {
        $errors = array();

        if ( empty( $full_name ) ) {
            $errors['full_name'] = __( 'Full name is required.', 'realestate-enquiry' );
        } elseif ( mb_strlen( $full_name ) > 100 ) {
            $errors['full_name'] = __( 'Full name must be 100 characters or less.', 'realestate-enquiry' );
        }

        if ( empty( $email ) ) {
            $errors['email'] = __( 'Email address is required.', 'realestate-enquiry' );
        } elseif ( ! is_email( $email ) ) {
            $errors['email'] = __( 'Please enter a valid email address.', 'realestate-enquiry' );
        }

        if ( empty( $telephone ) ) {
            $errors['telephone'] = __( 'Telephone number is required.', 'realestate-enquiry' );
        } elseif ( mb_strlen( $telephone ) > 30 ) {
            $errors['telephone'] = __( 'Phone number must be 30 characters or less.', 'realestate-enquiry' );
        } elseif ( ! preg_match( '/^[\+]?[\d\s\-\(\)]{7,30}$/', $telephone ) ) {
            $errors['telephone'] = __( 'Please enter a valid phone number.', 'realestate-enquiry' );
        }

        if ( empty( $enquiry_type ) ) {
            $errors['enquiry_type'] = __( 'Please select an enquiry type.', 'realestate-enquiry' );
        }

        if ( empty( $message ) ) {
            $errors['message'] = __( 'Message is required.', 'realestate-enquiry' );
        } elseif ( mb_strlen( $message ) > 2000 ) {
            $errors['message'] = __( 'Message must be 2000 characters or less.', 'realestate-enquiry' );
        }

        return $errors;
    }
}
