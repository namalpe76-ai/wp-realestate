<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Property_Meta_Boxes {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_property', array( $this, 'save_meta_boxes' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_media' ) );
    }

    public function admin_enqueue_media( $hook ) {
        if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
            return;
        }
        global $post_type;
        if ( 'property' !== $post_type ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'property_details',
            __( 'Property Details', 'realestate-core' ),
            array( $this, 'render_property_details' ),
            'property',
            'normal',
            'high'
        );

        add_meta_box(
            'property_location_meta',
            __( 'Location & Map', 'realestate-core' ),
            array( $this, 'render_property_location' ),
            'property',
            'normal',
            'high'
        );

        add_meta_box(
            'property_features_meta',
            __( 'Property Features', 'realestate-core' ),
            array( $this, 'render_property_features' ),
            'property',
            'normal',
            'default'
        );

        add_meta_box(
            'property_gallery',
            __( 'Property Gallery', 'realestate-core' ),
            array( $this, 'render_property_gallery' ),
            'property',
            'normal',
            'default'
        );
    }

    /**
     * Get property ID (auto-generated).
     */
    private function get_property_id_number( $post_id ) {
        $existing = get_post_meta( $post_id, '_property_id_number', true );
        if ( $existing ) {
            return $existing;
        }
        global $wpdb;
        $max = $wpdb->get_var(
            "SELECT MAX(CAST(SUBSTRING(pm.meta_value, 4) AS UNSIGNED)) FROM {$wpdb->postmeta} pm
             JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_property_id_number' AND p.post_type = 'property' AND p.post_status = 'publish'"
        );
        $next = $max ? (int) $max + 1 : 1;
        update_post_meta( $post_id, '_property_id_number', $next );
        return $next;
    }

    public function render_property_details( $post ) {
        wp_nonce_field( 'property_details_nonce', 'property_details_nonce_field' );

        $property_id_num = get_post_meta( $post->ID, '_property_id_number', true );
        if ( ! $property_id_num ) {
            $property_id_num = $this->get_property_id_number( $post->ID );
        }
        $property_display_id = 'RE-' . str_pad( $property_id_num, 3, '0', STR_PAD_LEFT );

        $price            = get_post_meta( $post->ID, '_property_price', true );
        $currency         = get_post_meta( $post->ID, '_property_currency', true );
        $bedrooms         = get_post_meta( $post->ID, '_property_bedrooms', true );
        $bathrooms        = get_post_meta( $post->ID, '_property_bathrooms', true );
        $parking          = get_post_meta( $post->ID, '_property_parking', true );
        $land_size        = get_post_meta( $post->ID, '_property_land_size', true );
        $land_size_unit   = get_post_meta( $post->ID, '_property_land_size_unit', true );
        $building_size    = get_post_meta( $post->ID, '_property_building_size', true );
        $building_size_unit = get_post_meta( $post->ID, '_property_building_size_unit', true );
        $address          = get_post_meta( $post->ID, '_property_address', true );

        ?>
        <style>
            .property-details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
            .property-details-grid .full-width { grid-column: 1 / -1; }
            .property-details-grid label { display: block; font-weight: 600; margin-bottom: 5px; }
            .property-details-grid input[type="text"],
            .property-details-grid input[type="number"],
            .property-details-grid select { width: 100%; padding: 6px; }
            .property-id-display { background: #f0f0f0; padding: 8px 12px; border-radius: 4px; font-weight: 700; font-size: 1.1em; }
            .input-group { display: flex; gap: 8px; }
            .input-group input { flex: 1; }
            .input-group select { width: 120px; flex: none; }
        </style>
        <div class="property-details-grid">
            <div class="full-width">
                <label><?php esc_html_e( 'Property ID', 'realestate-core' ); ?></label>
                <div class="property-id-display"><?php echo esc_html( $property_display_id ); ?></div>
                <input type="hidden" name="_property_display_id" value="<?php echo esc_attr( $property_display_id ); ?>">
            </div>

            <div>
                <label for="property_price"><?php esc_html_e( 'Price', 'realestate-core' ); ?></label>
                <div class="input-group">
                    <input type="number" id="property_price" name="_property_price" value="<?php echo esc_attr( $price ); ?>" step="0.01" min="0" placeholder="<?php esc_attr_e( 'Enter price', 'realestate-core' ); ?>">
                    <select id="property_currency" name="_property_currency">
                        <option value="LKR" <?php selected( $currency, 'LKR' ); ?>><?php esc_html_e( 'LKR', 'realestate-core' ); ?></option>
                        <option value="USD" <?php selected( $currency, 'USD' ); ?>><?php esc_html_e( 'USD', 'realestate-core' ); ?></option>
                        <option value="GBP" <?php selected( $currency, 'GBP' ); ?>><?php esc_html_e( 'GBP', 'realestate-core' ); ?></option>
                        <option value="EUR" <?php selected( $currency, 'EUR' ); ?>><?php esc_html_e( 'EUR', 'realestate-core' ); ?></option>
                        <option value="AUD" <?php selected( $currency, 'AUD' ); ?>><?php esc_html_e( 'AUD', 'realestate-core' ); ?></option>
                    </select>
                </div>
            </div>

            <div>
                <label for="property_bedrooms"><?php esc_html_e( 'Bedrooms', 'realestate-core' ); ?></label>
                <input type="number" id="property_bedrooms" name="_property_bedrooms" value="<?php echo esc_attr( $bedrooms ); ?>" min="0" step="1">
            </div>

            <div>
                <label for="property_bathrooms"><?php esc_html_e( 'Bathrooms', 'realestate-core' ); ?></label>
                <input type="number" id="property_bathrooms" name="_property_bathrooms" value="<?php echo esc_attr( $bathrooms ); ?>" min="0" step="1">
            </div>

            <div>
                <label for="property_parking"><?php esc_html_e( 'Parking Spaces', 'realestate-core' ); ?></label>
                <input type="number" id="property_parking" name="_property_parking" value="<?php echo esc_attr( $parking ); ?>" min="0" step="1">
            </div>

            <div>
                <label for="property_land_size"><?php esc_html_e( 'Land Size', 'realestate-core' ); ?></label>
                <div class="input-group">
                    <input type="number" id="property_land_size" name="_property_land_size" value="<?php echo esc_attr( $land_size ); ?>" step="0.01" min="0" placeholder="<?php esc_attr_e( 'Size', 'realestate-core' ); ?>">
                    <select id="property_land_size_unit" name="_property_land_size_unit">
                        <option value="perches" <?php selected( $land_size_unit, 'perches' ); ?>><?php esc_html_e( 'Perches', 'realestate-core' ); ?></option>
                        <option value="acres" <?php selected( $land_size_unit, 'acres' ); ?>><?php esc_html_e( 'Acres', 'realestate-core' ); ?></option>
                        <option value="sqft" <?php selected( $land_size_unit, 'sqft' ); ?>><?php esc_html_e( 'sqft', 'realestate-core' ); ?></option>
                        <option value="sqm" <?php selected( $land_size_unit, 'sqm' ); ?>><?php esc_html_e( 'sqm', 'realestate-core' ); ?></option>
                    </select>
                </div>
            </div>

            <div>
                <label for="property_building_size"><?php esc_html_e( 'Building Size', 'realestate-core' ); ?></label>
                <div class="input-group">
                    <input type="number" id="property_building_size" name="_property_building_size" value="<?php echo esc_attr( $building_size ); ?>" step="0.01" min="0" placeholder="<?php esc_attr_e( 'Size', 'realestate-core' ); ?>">
                    <select id="property_building_size_unit" name="_property_building_size_unit">
                        <option value="sqft" <?php selected( $building_size_unit, 'sqft' ); ?>><?php esc_html_e( 'sqft', 'realestate-core' ); ?></option>
                        <option value="sqm" <?php selected( $building_size_unit, 'sqm' ); ?>><?php esc_html_e( 'sqm', 'realestate-core' ); ?></option>
                    </select>
                </div>
            </div>

            <div class="full-width">
                <label for="property_address"><?php esc_html_e( 'Address', 'realestate-core' ); ?></label>
                <input type="text" id="property_address" name="_property_address" value="<?php echo esc_attr( $address ); ?>" style="width:100%;" placeholder="<?php esc_attr_e( 'Full property address', 'realestate-core' ); ?>">
            </div>
        </div>
        <?php
    }

    public function render_property_location( $post ) {
        wp_nonce_field( 'property_location_nonce', 'property_location_nonce_field' );

        $google_map_url = get_post_meta( $post->ID, '_property_google_map_url', true );
        ?>
        <style>
            .property-map-field label { display: block; font-weight: 600; margin-bottom: 5px; }
            .property-map-field input { width: 100%; padding: 6px; }
            .property-map-field .description { margin-top: 5px; color: #666; font-style: italic; }
            .map-preview { margin-top: 10px; border: 1px solid #ddd; padding: 5px; display: none; }
            .map-preview iframe { width: 100%; height: 300px; border: 0; }
        </style>
        <div class="property-map-field">
            <label for="property_google_map_url"><?php esc_html_e( 'Google Maps Embed URL', 'realestate-core' ); ?></label>
            <input type="url" id="property_google_map_url" name="_property_google_map_url" value="<?php echo esc_url( $google_map_url ); ?>" placeholder="https://www.google.com/maps/embed?pb=...">
            <p class="description"><?php esc_html_e( 'Paste the Google Maps embed URL from the share/embed option in Google Maps.', 'realestate-core' ); ?></p>
            <?php if ( $google_map_url ) : ?>
                <div class="map-preview" id="map-preview" style="display:block;">
                    <iframe src="<?php echo esc_url( $google_map_url ); ?>" allowfullscreen="" loading="lazy"></iframe>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function render_property_features( $post ) {
        wp_nonce_field( 'property_features_nonce', 'property_features_nonce_field' );

        $features = get_post_meta( $post->ID, '_property_features', true );
        if ( ! is_array( $features ) ) {
            $features = array();
        }

        $all_features = array(
            'swimming_pool'    => __( 'Swimming Pool', 'realestate-core' ),
            'garden'           => __( 'Garden', 'realestate-core' ),
            'garage'           => __( 'Garage', 'realestate-core' ),
            'air_conditioning' => __( 'Air Conditioning', 'realestate-core' ),
            'security_system'  => __( 'Security System', 'realestate-core' ),
            'balcony'          => __( 'Balcony', 'realestate-core' ),
            'servant_quarters' => __( 'Servant Quarters', 'realestate-core' ),
            'gym'              => __( 'Gym', 'realestate-core' ),
            'laundry'          => __( 'Laundry', 'realestate-core' ),
            'store_room'       => __( 'Store Room', 'realestate-core' ),
        );

        ?>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
            <?php foreach ( $all_features as $key => $label ) : ?>
                <label style="display: flex; align-items: center; gap: 6px; padding: 4px 0;">
                    <input type="checkbox" name="_property_features[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $features, true ) ); ?>>
                    <?php echo esc_html( $label ); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }

    public function render_property_gallery( $post ) {
        wp_nonce_field( 'property_gallery_nonce', 'property_gallery_nonce_field' );

        $gallery_ids = get_post_meta( $post->ID, '_property_gallery', true );
        if ( ! is_array( $gallery_ids ) ) {
            $gallery_ids = array();
        }
        ?>
        <div id="property-gallery-container">
            <input type="hidden" id="property_gallery_ids" name="_property_gallery" value="<?php echo esc_attr( implode( ',', $gallery_ids ) ); ?>">
            <div id="property-gallery-preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                <?php foreach ( $gallery_ids as $img_id ) :
                    $img_src = wp_get_attachment_image_url( $img_id, 'thumbnail' );
                    if ( $img_src ) : ?>
                        <div class="gallery-item" data-id="<?php echo esc_attr( $img_id ); ?>" style="position:relative; width:120px; height:120px;">
                            <img src="<?php echo esc_url( $img_src ); ?>" style="width:100%; height:100%; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                            <button type="button" class="remove-gallery-item" style="position:absolute; top:2px; right:2px; background:red; color:#fff; border:none; border-radius:50%; width:22px; height:22px; cursor:pointer; font-size:12px; line-height:20px; text-align:center;">&times;</button>
                        </div>
                    <?php endif;
                endforeach; ?>
            </div>
            <button type="button" id="add-gallery-images" class="button"><?php esc_html_e( 'Add Gallery Images', 'realestate-core' ); ?></button>
        </div>
        <script>
        jQuery(document).ready(function($) {
            var frame;
            $('#add-gallery-images').on('click', function(e) {
                e.preventDefault();
                if (frame) { frame.open(); return; }
                frame = wp.media({ title: '<?php echo esc_js( __( 'Select Gallery Images', 'realestate-core' ) ); ?>', button: { text: '<?php echo esc_js( __( 'Add to Gallery', 'realestate-core' ) ); ?>' }, multiple: true });
                frame.on('select', function() {
                    var attachments = frame.state().get('selection').toJSON();
                    var ids = $('#property_gallery_ids').val() ? $('#property_gallery_ids').val().split(',') : [];
                    attachments.forEach(function(att) {
                        if (ids.indexOf(String(att.id)) === -1) {
                            ids.push(att.id);
                            var thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
                            $('#property-gallery-preview').append('<div class="gallery-item" data-id="' + att.id + '" style="position:relative;width:120px;height:120px;"><img src="' + thumb + '" style="width:100%;height:100%;object-fit:cover;border-radius:4px;border:1px solid #ddd;"><button type="button" class="remove-gallery-item" style="position:absolute;top:2px;right:2px;background:red;color:#fff;border:none;border-radius:50%;width:22px;height:22px;cursor:pointer;font-size:12px;line-height:20px;text-align:center;">&times;</button></div>');
                        }
                    });
                    $('#property_gallery_ids').val(ids.join(','));
                });
                frame.open();
            });
            $(document).on('click', '.remove-gallery-item', function(e) {
                e.preventDefault();
                var $item = $(this).closest('.gallery-item');
                var id = $item.data('id').toString();
                var ids = $('#property_gallery_ids').val().split(',').filter(function(i) { return i !== id; });
                $('#property_gallery_ids').val(ids.join(','));
                $item.remove();
            });
        });
        </script>
        <?php
    }

    public function save_meta_boxes( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save property details.
        if ( isset( $_POST['property_details_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['property_details_nonce_field'] ) ), 'property_details_nonce' ) ) {
            $text_fields = array(
                '_property_display_id'      => '_property_display_id',
                '_property_currency'        => '_property_currency',
                '_property_land_size_unit'  => '_property_land_size_unit',
                '_property_building_size_unit' => '_property_building_size_unit',
                '_property_address'         => '_property_address',
            );
            foreach ( $text_fields as $key ) {
                if ( isset( $_POST[ $key ] ) ) {
                    update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
                }
            }

            $number_fields = array(
                '_property_price',
                '_property_bedrooms',
                '_property_bathrooms',
                '_property_parking',
                '_property_land_size',
                '_property_building_size',
            );
            foreach ( $number_fields as $key ) {
                if ( isset( $_POST[ $key ] ) ) {
                    update_post_meta( $post_id, $key, absint( $_POST[ $key ] ) );
                }
            }
        }

        // Save location.
        if ( isset( $_POST['property_location_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['property_location_nonce_field'] ) ), 'property_location_nonce' ) ) {
            if ( isset( $_POST['_property_google_map_url'] ) ) {
                update_post_meta( $post_id, '_property_google_map_url', esc_url_raw( wp_unslash( $_POST['_property_google_map_url'] ) ) );
            }
        }

        // Save features.
        if ( isset( $_POST['property_features_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['property_features_nonce_field'] ) ), 'property_features_nonce' ) ) {
            $features = array();
            if ( isset( $_POST['_property_features'] ) && is_array( $_POST['_property_features'] ) ) {
                $allowed_features = array( 'swimming_pool', 'garden', 'garage', 'air_conditioning', 'security_system', 'balcony', 'servant_quarters', 'gym', 'laundry', 'store_room' );
                $features = array_map( 'sanitize_text_field', wp_unslash( $_POST['_property_features'] ) );
                $features = array_intersect( $features, $allowed_features );
            }
            update_post_meta( $post_id, '_property_features', $features );
        }

        // Save gallery.
        if ( isset( $_POST['property_gallery_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['property_gallery_nonce_field'] ) ), 'property_gallery_nonce' ) ) {
            $gallery = array();
            if ( isset( $_POST['_property_gallery'] ) && '' !== $_POST['_property_gallery'] ) {
                $gallery_raw = sanitize_text_field( wp_unslash( $_POST['_property_gallery'] ) );
                $gallery = array_filter( array_map( 'absint', explode( ',', $gallery_raw ) ) );
            }
            update_post_meta( $post_id, '_property_gallery', $gallery );
        }
    }
}
