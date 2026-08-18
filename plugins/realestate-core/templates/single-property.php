<?php
/**
 * Template Name: Single Property
 * Template Post Type: property
 *
 * @package realestate-core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        $post_id        = get_the_ID();
        $property_id    = get_post_meta( $post_id, '_property_display_id', true );
        $price          = get_post_meta( $post_id, '_property_price', true );
        $currency       = get_post_meta( $post_id, '_property_currency', true );
        $bedrooms       = get_post_meta( $post_id, '_property_bedrooms', true );
        $bathrooms      = get_post_meta( $post_id, '_property_bathrooms', true );
        $parking        = get_post_meta( $post_id, '_property_parking', true );
        $land_size      = get_post_meta( $post_id, '_property_land_size', true );
        $land_size_unit = get_post_meta( $post_id, '_property_land_size_unit', true );
        $building_size  = get_post_meta( $post_id, '_property_building_size', true );
        $building_unit  = get_post_meta( $post_id, '_property_building_size_unit', true );
        $address        = get_post_meta( $post_id, '_property_address', true );
        $map_url        = get_post_meta( $post_id, '_property_google_map_url', true );
        $features       = get_post_meta( $post_id, '_property_features', true );
        $gallery_ids    = get_post_meta( $post_id, '_property_gallery', true );
        $types          = wp_get_post_terms( $post_id, 'property_type', array( 'fields' => 'all' ) );
        $statuses       = wp_get_post_terms( $post_id, 'property_status', array( 'fields' => 'all' ) );
        $locations      = wp_get_post_terms( $post_id, 'property_location', array( 'fields' => 'all' ) );

        if ( ! is_array( $features ) ) {
            $features = array();
        }
        if ( ! is_array( $gallery_ids ) ) {
            $gallery_ids = array();
        }

        $feature_labels = array(
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

        $share_url   = urlencode( get_the_permalink() );
        $share_title = urlencode( get_the_title() );
        $whatsapp_url = 'https://wa.me/?text=' . $share_title . '%20' . $share_url;
        ?>
        <div class="re-single-property">

            <!-- Breadcrumb -->
            <nav class="re-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'realestate-core' ); ?>">
                <ol class="re-breadcrumb__list" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li class="re-breadcrumb__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="item"><span itemprop="name"><?php esc_html_e( 'Home', 'realestate-core' ); ?></span></a>
                        <meta itemprop="position" content="1">
                    </li>
                    <li class="re-breadcrumb__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>" itemprop="item"><span itemprop="name"><?php esc_html_e( 'Properties', 'realestate-core' ); ?></span></a>
                        <meta itemprop="position" content="2">
                    </li>
                    <?php if ( ! empty( $types ) && ! is_wp_error( $types ) ) : ?>
                        <li class="re-breadcrumb__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="<?php echo esc_url( get_term_link( $types[0] ) ); ?>" itemprop="item"><span itemprop="name"><?php echo esc_html( $types[0]->name ); ?></span></a>
                            <meta itemprop="position" content="3">
                        </li>
                    <?php endif; ?>
                    <li class="re-breadcrumb__item re-breadcrumb__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php the_title(); ?></span>
                        <meta itemprop="position" content="<?php echo ! empty( $types ) ? '4' : '3'; ?>">
                    </li>
                </ol>
            </nav>

            <!-- Gallery -->
            <div class="re-property-gallery">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="re-property-gallery__main">
                        <a href="<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ); ?>" class="re-property-gallery__lightbox" data-gallery="main">
                            <?php the_post_thumbnail( 'large', array( 'class' => 're-property-gallery__main-img', 'loading' => 'eager' ) ); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $gallery_ids ) ) : ?>
                    <div class="re-property-gallery__thumbs">
                        <?php foreach ( $gallery_ids as $img_id ) :
                            $thumb_url = wp_get_attachment_image_url( $img_id, 'medium' );
                            $full_url  = wp_get_attachment_image_url( $img_id, 'full' );
                            $alt       = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
                            if ( $thumb_url ) : ?>
                                <a href="<?php echo esc_url( $full_url ); ?>" class="re-property-gallery__thumb" data-gallery="gallery">
                                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
                                </a>
                            <?php endif;
                        endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="re-property-content">

                <!-- Main Content -->
                <div class="re-property-content__main">

                    <!-- Header -->
                    <div class="re-property-header">
                        <div class="re-property-header__top">
                            <?php if ( ! empty( $property_id ) ) : ?>
                                <span class="re-property-header__id"><?php echo esc_html( $property_id ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $statuses ) && ! is_wp_error( $statuses ) ) : ?>
                                <span class="re-property-header__status"><?php echo esc_html( $statuses[0]->name ); ?></span>
                            <?php endif; ?>
                        </div>

                        <h1 class="re-property-header__title"><?php the_title(); ?></h1>

                        <?php if ( $address ) : ?>
                            <p class="re-property-header__address">
                                <span class="dashicons dashicons-location" aria-hidden="true"></span>
                                <?php echo esc_html( $address ); ?>
                            </p>
                        <?php endif; ?>

                        <div class="re-property-header__price">
                            <?php if ( $price ) : ?>
                                <span class="re-property-header__price-value">
                                    <?php echo esc_html( $currency ); ?>
                                    <?php echo esc_html( number_format( (float) $price ) ); ?>
                                </span>
                            <?php endif; ?>

                            <?php if ( ! empty( $statuses ) && ! is_wp_error( $statuses ) && 'for-rent' === $statuses[0]->slug ) : ?>
                                <span class="re-property-header__price-period"><?php esc_html_e( '/month', 'realestate-core' ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Key Features -->
                    <div class="re-property-features-grid">
                        <?php if ( $bedrooms ) : ?>
                            <div class="re-property-features-grid__item">
                                <span class="dashicons dashicons-admin-home" aria-hidden="true"></span>
                                <span class="re-property-features-grid__value"><?php echo esc_html( $bedrooms ); ?></span>
                                <span class="re-property-features-grid__label"><?php esc_html_e( 'Bedrooms', 'realestate-core' ); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ( $bathrooms ) : ?>
                            <div class="re-property-features-grid__item">
                                <span class="dashicons dashicons-admin-comments" aria-hidden="true"></span>
                                <span class="re-property-features-grid__value"><?php echo esc_html( $bathrooms ); ?></span>
                                <span class="re-property-features-grid__label"><?php esc_html_e( 'Bathrooms', 'realestate-core' ); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ( $parking ) : ?>
                            <div class="re-property-features-grid__item">
                                <span class="dashicons dashicons-car" aria-hidden="true"></span>
                                <span class="re-property-features-grid__value"><?php echo esc_html( $parking ); ?></span>
                                <span class="re-property-features-grid__label"><?php esc_html_e( 'Parking', 'realestate-core' ); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ( $land_size ) : ?>
                            <div class="re-property-features-grid__item">
                                <span class="dashicons dashicons-admin-site" aria-hidden="true"></span>
                                <span class="re-property-features-grid__value"><?php echo esc_html( $land_size . ' ' . $land_size_unit ); ?></span>
                                <span class="re-property-features-grid__label"><?php esc_html_e( 'Land Size', 'realestate-core' ); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ( $building_size ) : ?>
                            <div class="re-property-features-grid__item">
                                <span class="dashicons dashicons-building" aria-hidden="true"></span>
                                <span class="re-property-features-grid__value"><?php echo esc_html( $building_size . ' ' . $building_unit ); ?></span>
                                <span class="re-property-features-grid__label"><?php esc_html_e( 'Building Size', 'realestate-core' ); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <div class="re-property-section">
                        <h2 class="re-property-section__title"><?php esc_html_e( 'Description', 'realestate-core' ); ?></h2>
                        <div class="re-property-section__content">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <!-- Specifications -->
                    <div class="re-property-section">
                        <h2 class="re-property-section__title"><?php esc_html_e( 'Property Specifications', 'realestate-core' ); ?></h2>
                        <table class="re-property-specs">
                            <tbody>
                                <?php if ( $property_id ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Property ID', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $property_id ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( ! empty( $types ) && ! is_wp_error( $types ) ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Property Type', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $types[0]->name ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( ! empty( $statuses ) && ! is_wp_error( $statuses ) ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Status', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $statuses[0]->name ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( ! empty( $locations ) && ! is_wp_error( $locations ) ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Location', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $locations[0]->name ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( $price ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Price', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $currency . ' ' . number_format( (float) $price ) ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( $bedrooms ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Bedrooms', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $bedrooms ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( $bathrooms ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Bathrooms', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $bathrooms ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( $parking ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Parking Spaces', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $parking ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( $land_size ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Land Size', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $land_size . ' ' . $land_size_unit ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( $building_size ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Building Size', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $building_size . ' ' . $building_unit ); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ( $address ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Address', 'realestate-core' ); ?></th>
                                        <td><?php echo esc_html( $address ); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Features -->
                    <?php if ( ! empty( $features ) ) : ?>
                        <div class="re-property-section">
                            <h2 class="re-property-section__title"><?php esc_html_e( 'Property Features', 'realestate-core' ); ?></h2>
                            <div class="re-property-features-list">
                                <?php foreach ( $features as $feature ) : ?>
                                    <?php if ( isset( $feature_labels[ $feature ] ) ) : ?>
                                        <span class="re-property-features-list__item">
                                            <span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
                                            <?php echo esc_html( $feature_labels[ $feature ] ); ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Google Map -->
                    <?php if ( $map_url ) : ?>
                        <div class="re-property-section">
                            <h2 class="re-property-section__title"><?php esc_html_e( 'Location Map', 'realestate-core' ); ?></h2>
                            <div class="re-property-map">
                                <iframe src="<?php echo esc_url( $map_url ); ?>" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php esc_attr_e( 'Property location map', 'realestate-core' ); ?>"></iframe>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Enquiry Form -->
                    <div class="re-property-section">
                        <h2 class="re-property-section__title"><?php esc_html_e( 'Enquire About This Property', 'realestate-core' ); ?></h2>
                        <?php echo do_shortcode( '[property_enquiry property_id="' . esc_attr( $post_id ) . '"]' ); ?>
                    </div>

                </div>

                <!-- Sidebar -->
                <aside class="re-property-content__sidebar">

                    <!-- Action Buttons -->
                    <div class="re-property-sidebar-box re-property-sidebar-box--actions">
                        <a href="#re-enquiry-form" class="re-button re-button--primary re-button--full">
                            <span class="dashicons dashicons-email-alt" aria-hidden="true"></span>
                            <?php esc_html_e( 'Enquire About This Property', 'realestate-core' ); ?>
                        </a>
                        <a href="<?php echo esc_url( add_query_arg( 'action', 'schedule', get_the_permalink() ) ); ?>" class="re-button re-button--outline re-button--full">
                            <span class="dashicons dashicons-calendar-alt" aria-hidden="true"></span>
                            <?php esc_html_e( 'Schedule a Viewing', 'realestate-core' ); ?>
                        </a>
                        <a href="<?php echo esc_url( $whatsapp_url ); ?>" class="re-button re-button--whatsapp re-button--full" target="_blank" rel="noopener noreferrer">
                            <span class="dashicons dashicons-smartphone" aria-hidden="true"></span>
                            <?php esc_html_e( 'Share on WhatsApp', 'realestate-core' ); ?>
                        </a>
                    </div>

                    <!-- Quick Info -->
                    <div class="re-property-sidebar-box">
                        <h3 class="re-property-sidebar-box__title"><?php esc_html_e( 'Quick Info', 'realestate-core' ); ?></h3>
                        <ul class="re-property-sidebar-list">
                            <?php if ( $property_id ) : ?>
                                <li>
                                    <strong><?php esc_html_e( 'ID:', 'realestate-core' ); ?></strong>
                                    <span><?php echo esc_html( $property_id ); ?></span>
                                </li>
                            <?php endif; ?>
                            <?php if ( $price ) : ?>
                                <li>
                                    <strong><?php esc_html_e( 'Price:', 'realestate-core' ); ?></strong>
                                    <span class="re-property-sidebar-list__price">
                                        <?php echo esc_html( $currency . ' ' . number_format( (float) $price ) ); ?>
                                    </span>
                                </li>
                            <?php endif; ?>
                            <?php if ( ! empty( $types ) && ! is_wp_error( $types ) ) : ?>
                                <li>
                                    <strong><?php esc_html_e( 'Type:', 'realestate-core' ); ?></strong>
                                    <span><?php echo esc_html( $types[0]->name ); ?></span>
                                </li>
                            <?php endif; ?>
                            <?php if ( ! empty( $statuses ) && ! is_wp_error( $statuses ) ) : ?>
                                <li>
                                    <strong><?php esc_html_e( 'Status:', 'realestate-core' ); ?></strong>
                                    <span><?php echo esc_html( $statuses[0]->name ); ?></span>
                                </li>
                            <?php endif; ?>
                            <?php if ( ! empty( $locations ) && ! is_wp_error( $locations ) ) : ?>
                                <li>
                                    <strong><?php esc_html_e( 'Location:', 'realestate-core' ); ?></strong>
                                    <span><?php echo esc_html( $locations[0]->name ); ?></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                </aside>

            </div>

            <!-- Related Properties -->
            <?php
            $related_args = array(
                'post_type'      => 'property',
                'post_status'    => 'publish',
                'posts_per_page' => 3,
                'post__not_in'   => array( $post_id ),
                'orderby'        => 'rand',
            );

            if ( ! empty( $types ) && ! is_wp_error( $types ) ) {
                $related_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
                    array(
                        'taxonomy' => 'property_type',
                        'field'    => 'term_id',
                        'terms'    => wp_list_pluck( $types, 'term_id' ),
                    ),
                );
            }

            $related_query = new WP_Query( $related_args );

            if ( $related_query->have_posts() ) :
            ?>
                <div class="re-related-properties">
                    <h2 class="re-related-properties__title"><?php esc_html_e( 'Related Properties', 'realestate-core' ); ?></h2>
                    <div class="re-properties-grid re-properties-grid--3col">
                        <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
                            <?php get_template_part( 'templates/property-card', null, array( 'post_id' => get_the_ID() ) ); ?>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php
                wp_reset_postdata();
            endif;
            ?>

        </div>
    <?php
    endwhile;
endif;

get_footer();
