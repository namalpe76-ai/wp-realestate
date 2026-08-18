<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Property_Search {

    public function __construct() {
        add_shortcode( 'property_search', array( $this, 'render_search_form' ) );
        add_shortcode( 'property_results', array( $this, 'render_search_results' ) );
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * Render the property search form shortcode.
     */
    public function render_search_form( $atts ) {
        $atts = shortcode_atts( array(
            'show_title' => false,
            'layout'     => 'horizontal',
        ), $atts, 'property_search' );

        ob_start();
        $this->search_form_template( $atts );
        return ob_get_clean();
    }

    /**
     * Search form template.
     */
    private function search_form_template( $atts ) {
        $type_options     = $this->get_taxonomy_options( 'property_type' );
        $status_options   = $this->get_taxonomy_options( 'property_status' );
        $location_options = $this->get_taxonomy_options( 'property_location' );

        $selected_type     = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';
        $selected_status   = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
        $selected_location = isset( $_GET['location'] ) ? sanitize_text_field( wp_unslash( $_GET['location'] ) ) : '';
        $min_price         = isset( $_GET['min_price'] ) ? absint( $_GET['min_price'] ) : '';
        $max_price         = isset( $_GET['max_price'] ) ? absint( $_GET['max_price'] ) : '';
        $bedrooms          = isset( $_GET['bedrooms'] ) ? absint( $_GET['bedrooms'] ) : '';
        $bathrooms         = isset( $_GET['bathrooms'] ) ? absint( $_GET['bathrooms'] ) : '';
        $min_size          = isset( $_GET['min_size'] ) ? absint( $_GET['min_size'] ) : '';

        $form_layout = 'horizontal' === $atts['layout'] ? 're-search-form--horizontal' : 're-search-form--vertical';
        ?>
        <div class="re-search-form <?php echo esc_attr( $form_layout ); ?>">
            <?php if ( $atts['show_title'] ) : ?>
                <h2 class="re-search-form__title"><?php esc_html_e( 'Search Properties', 'realestate-core' ); ?></h2>
            <?php endif; ?>

            <form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>" class="re-search-form__form">
                <div class="re-search-form__fields">
                    <div class="re-search-form__field">
                        <label for="re-search-type"><?php esc_html_e( 'Property Type', 'realestate-core' ); ?></label>
                        <select id="re-search-type" name="type">
                            <option value=""><?php esc_html_e( 'All Types', 'realestate-core' ); ?></option>
                            <?php foreach ( $type_options as $value => $label ) : ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected_type, $value ); ?>>
                                    <?php echo esc_html( $label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="re-search-form__field">
                        <label for="re-search-status"><?php esc_html_e( 'Sale / Rent', 'realestate-core' ); ?></label>
                        <select id="re-search-status" name="status">
                            <option value=""><?php esc_html_e( 'All', 'realestate-core' ); ?></option>
                            <?php foreach ( $status_options as $value => $label ) : ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected_status, $value ); ?>>
                                    <?php echo esc_html( $label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="re-search-form__field">
                        <label for="re-search-location"><?php esc_html_e( 'Location', 'realestate-core' ); ?></label>
                        <select id="re-search-location" name="location">
                            <option value=""><?php esc_html_e( 'All Locations', 'realestate-core' ); ?></option>
                            <?php foreach ( $location_options as $value => $label ) : ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected_location, $value ); ?>>
                                    <?php echo esc_html( $label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="re-search-form__field re-search-form__field--price">
                        <label><?php esc_html_e( 'Price Range', 'realestate-core' ); ?></label>
                        <div class="re-search-form__price-range">
                            <input type="number" name="min_price" value="<?php echo esc_attr( $min_price ); ?>" placeholder="<?php esc_attr_e( 'Min', 'realestate-core' ); ?>" min="0">
                            <span class="re-search-form__separator">-</span>
                            <input type="number" name="max_price" value="<?php echo esc_attr( $max_price ); ?>" placeholder="<?php esc_attr_e( 'Max', 'realestate-core' ); ?>" min="0">
                        </div>
                    </div>

                    <div class="re-search-form__field">
                        <label for="re-search-bedrooms"><?php esc_html_e( 'Bedrooms', 'realestate-core' ); ?></label>
                        <select id="re-search-bedrooms" name="bedrooms">
                            <option value=""><?php esc_html_e( 'Any', 'realestate-core' ); ?></option>
                            <?php for ( $i = 1; $i <= 10; $i++ ) : ?>
                                <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $bedrooms, $i ); ?>><?php echo esc_html( $i ); ?>+</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="re-search-form__field">
                        <label for="re-search-bathrooms"><?php esc_html_e( 'Bathrooms', 'realestate-core' ); ?></label>
                        <select id="re-search-bathrooms" name="bathrooms">
                            <option value=""><?php esc_html_e( 'Any', 'realestate-core' ); ?></option>
                            <?php for ( $i = 1; $i <= 10; $i++ ) : ?>
                                <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $bathrooms, $i ); ?>><?php echo esc_html( $i ); ?>+</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="re-search-form__field">
                        <label for="re-search-min-size"><?php esc_html_e( 'Min Size (sqft)', 'realestate-core' ); ?></label>
                        <input type="number" id="re-search-min-size" name="min_size" value="<?php echo esc_attr( $min_size ); ?>" min="0" placeholder="<?php esc_attr_e( 'Min sqft', 'realestate-core' ); ?>">
                    </div>
                </div>

                <div class="re-search-form__actions">
                    <button type="submit" class="re-search-form__button"><?php esc_html_e( 'Search Properties', 'realestate-core' ); ?></button>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>" class="re-search-form__reset"><?php esc_html_e( 'Reset', 'realestate-core' ); ?></a>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Get taxonomy terms for dropdowns.
     */
    private function get_taxonomy_options( $taxonomy ) {
        $terms  = get_terms( array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ) );
        $options = array();
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $options[ $term->slug ] = $term->name;
            }
        }
        return $options;
    }

    /**
     * Render search results shortcode.
     */
    public function render_search_results( $atts ) {
        ob_start();
        $this->perform_search();
        return ob_get_clean();
    }

    /**
     * Perform the property search query.
     */
    public function perform_search() {
        $args = array(
            'post_type'      => 'property',
            'post_status'    => 'publish',
            'posts_per_page' => 12,
            'paged'          => get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1,
        );

        $meta_query  = array();
        $tax_query   = array();

        // Type filter.
        if ( ! empty( $_GET['type'] ) ) {
            $type = sanitize_text_field( wp_unslash( $_GET['type'] ) );
            $tax_query[] = array(
                'taxonomy' => 'property_type',
                'field'    => 'slug',
                'terms'    => $type,
            );
        }

        // Status filter.
        if ( ! empty( $_GET['status'] ) ) {
            $status = sanitize_text_field( wp_unslash( $_GET['status'] ) );
            $tax_query[] = array(
                'taxonomy' => 'property_status',
                'field'    => 'slug',
                'terms'    => $status,
            );
        }

        // Location filter.
        if ( ! empty( $_GET['location'] ) ) {
            $location = sanitize_text_field( wp_unslash( $_GET['location'] ) );
            $tax_query[] = array(
                'taxonomy' => 'property_location',
                'field'    => 'slug',
                'terms'    => $location,
            );
        }

        // Price filters.
        if ( ! empty( $_GET['min_price'] ) ) {
            $meta_query[] = array(
                'key'     => '_property_price',
                'value'   => absint( $_GET['min_price'] ),
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        }
        if ( ! empty( $_GET['max_price'] ) ) {
            $meta_query[] = array(
                'key'     => '_property_price',
                'value'   => absint( $_GET['max_price'] ),
                'type'    => 'NUMERIC',
                'compare' => '<=',
            );
        }

        // Bedrooms filter.
        if ( ! empty( $_GET['bedrooms'] ) ) {
            $meta_query[] = array(
                'key'     => '_property_bedrooms',
                'value'   => absint( $_GET['bedrooms'] ),
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        }

        // Bathrooms filter.
        if ( ! empty( $_GET['bathrooms'] ) ) {
            $meta_query[] = array(
                'key'     => '_property_bathrooms',
                'value'   => absint( $_GET['bathrooms'] ),
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        }

        // Min size filter.
        if ( ! empty( $_GET['min_size'] ) ) {
            $meta_query[] = array(
                'key'     => '_property_building_size',
                'value'   => absint( $_GET['min_size'] ),
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        }

        // Sort.
        $sort = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : 'newest';
        switch ( $sort ) {
            case 'price_asc':
                $args['meta_key'] = '_property_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'price_desc':
                $args['meta_key'] = '_property_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'oldest':
                $args['orderby'] = 'date';
                $args['order']   = 'ASC';
                break;
            case 'newest':
            default:
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
        }

        if ( ! empty( $meta_query ) ) {
            $args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery
        }
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
            if ( count( $tax_query ) > 1 ) {
                $args['tax_query']['relation'] = 'AND';
            }
        }

        $query = new WP_Query( $args );

        $this->render_results( $query, $sort );
    }

    /**
     * Render search results.
     */
    private function render_results( $query, $current_sort ) {
        ?>
        <div class="re-search-results">
            <div class="re-search-results__header">
                <div class="re-search-results__count">
                    <?php
                    printf(
                        /* translators: %d: number of properties found */
                        esc_html( _n( '%d property found', '%d properties found', $query->found_posts, 'realestate-core' ) ),
                        absint( $query->found_posts )
                    );
                    ?>
                </div>

                <div class="re-search-results__sort">
                    <label for="re-sort"><?php esc_html_e( 'Sort by:', 'realestate-core' ); ?></label>
                    <select id="re-sort" class="re-sort-select" data-current="<?php echo esc_attr( $current_sort ); ?>">
                        <option value="newest" <?php selected( $current_sort, 'newest' ); ?>><?php esc_html_e( 'Newest First', 'realestate-core' ); ?></option>
                        <option value="oldest" <?php selected( $current_sort, 'oldest' ); ?>><?php esc_html_e( 'Oldest First', 'realestate-core' ); ?></option>
                        <option value="price_asc" <?php selected( $current_sort, 'price_asc' ); ?>><?php esc_html_e( 'Price: Low to High', 'realestate-core' ); ?></option>
                        <option value="price_desc" <?php selected( $current_sort, 'price_desc' ); ?>><?php esc_html_e( 'Price: High to Low', 'realestate-core' ); ?></option>
                    </select>
                </div>
            </div>

            <?php if ( $query->have_posts() ) : ?>
                <div class="re-properties-grid re-properties-grid--list">
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <?php get_template_part( 'templates/property-card', null, array( 'post_id' => get_the_ID() ) ); ?>
                    <?php endwhile; ?>
                </div>

                <div class="re-search-results__pagination">
                    <?php
                    echo wp_kses_post( paginate_links( array(
                        'total'     => $query->max_num_pages,
                        'prev_text' => '&laquo; ' . esc_html__( 'Previous', 'realestate-core' ),
                        'next_text' => esc_html__( 'Next', 'realestate-core' ) . ' &raquo;',
                    ) ) );
                    ?>
                </div>
            <?php else : ?>
                <div class="re-search-results__empty">
                    <h3><?php esc_html_e( 'No properties found', 'realestate-core' ); ?></h3>
                    <p><?php esc_html_e( 'Sorry, no properties matched your search criteria. Please try adjusting your filters.', 'realestate-core' ); ?></p>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>" class="re-button"><?php esc_html_e( 'View All Properties', 'realestate-core' ); ?></a>
                </div>
            <?php endif; ?>
        </div>
        <?php
        wp_reset_postdata();
    }

    /**
     * Register REST API routes for AJAX search.
     */
    public function register_rest_routes() {
        register_rest_route( 'realestate-core/v1', '/search', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'rest_search' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'type'       => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'status'     => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'location'   => array( 'sanitize_callback' => 'sanitize_text_field' ),
                'min_price'  => array( 'sanitize_callback' => 'absint' ),
                'max_price'  => array( 'sanitize_callback' => 'absint' ),
                'bedrooms'   => array( 'sanitize_callback' => 'absint' ),
                'bathrooms'  => array( 'sanitize_callback' => 'absint' ),
                'min_size'   => array( 'sanitize_callback' => 'absint' ),
                'page'       => array( 'sanitize_callback' => 'absint' ),
                'sort'       => array( 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );
    }

    /**
     * REST API search handler.
     */
    public function rest_search( $request ) {
        $args = array(
            'post_type'      => 'property',
            'post_status'    => 'publish',
            'posts_per_page' => 12,
            'paged'          => $request->get_param( 'page' ) ? $request->get_param( 'page' ) : 1,
        );

        $meta_query = array();
        $tax_query  = array();

        if ( $request->get_param( 'type' ) ) {
            $tax_query[] = array(
                'taxonomy' => 'property_type',
                'field'    => 'slug',
                'terms'    => $request->get_param( 'type' ),
            );
        }
        if ( $request->get_param( 'status' ) ) {
            $tax_query[] = array(
                'taxonomy' => 'property_status',
                'field'    => 'slug',
                'terms'    => $request->get_param( 'status' ),
            );
        }
        if ( $request->get_param( 'location' ) ) {
            $tax_query[] = array(
                'taxonomy' => 'property_location',
                'field'    => 'slug',
                'terms'    => $request->get_param( 'location' ),
            );
        }
        if ( $request->get_param( 'min_price' ) ) {
            $meta_query[] = array(
                'key'     => '_property_price',
                'value'   => $request->get_param( 'min_price' ),
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        }
        if ( $request->get_param( 'max_price' ) ) {
            $meta_query[] = array(
                'key'     => '_property_price',
                'value'   => $request->get_param( 'max_price' ),
                'type'    => 'NUMERIC',
                'compare' => '<=',
            );
        }
        if ( $request->get_param( 'bedrooms' ) ) {
            $meta_query[] = array(
                'key'     => '_property_bedrooms',
                'value'   => $request->get_param( 'bedrooms' ),
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        }
        if ( $request->get_param( 'bathrooms' ) ) {
            $meta_query[] = array(
                'key'     => '_property_bathrooms',
                'value'   => $request->get_param( 'bathrooms' ),
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        }
        if ( $request->get_param( 'min_size' ) ) {
            $meta_query[] = array(
                'key'     => '_property_building_size',
                'value'   => $request->get_param( 'min_size' ),
                'type'    => 'NUMERIC',
                'compare' => '>=',
            );
        }

        $sort = $request->get_param( 'sort' ) ? $request->get_param( 'sort' ) : 'newest';
        switch ( $sort ) {
            case 'price_asc':
                $args['meta_key'] = '_property_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'price_desc':
                $args['meta_key'] = '_property_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'oldest':
                $args['orderby'] = 'date';
                $args['order']   = 'ASC';
                break;
            default:
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
        }

        if ( ! empty( $meta_query ) ) {
            $args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery
        }
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
            if ( count( $tax_query ) > 1 ) {
                $args['tax_query']['relation'] = 'AND';
            }
        }

        $query      = new WP_Query( $args );
        $properties = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();

                $terms_type    = wp_get_post_terms( $post_id, 'property_type', array( 'fields' => 'names' ) );
                $terms_status  = wp_get_post_terms( $post_id, 'property_status', array( 'fields' => 'names' ) );
                $terms_loc     = wp_get_post_terms( $post_id, 'property_location', array( 'fields' => 'names' ) );

                $properties[] = array(
                    'id'          => $post_id,
                    'title'       => get_the_title(),
                    'url'         => get_the_permalink(),
                    'excerpt'     => get_the_excerpt(),
                    'thumbnail'   => get_the_post_thumbnail_url( $post_id, 'medium' ),
                    'price'       => get_post_meta( $post_id, '_property_price', true ),
                    'currency'    => get_post_meta( $post_id, '_property_currency', true ),
                    'display_id'  => get_post_meta( $post_id, '_property_display_id', true ),
                    'bedrooms'    => get_post_meta( $post_id, '_property_bedrooms', true ),
                    'bathrooms'   => get_post_meta( $post_id, '_property_bathrooms', true ),
                    'parking'     => get_post_meta( $post_id, '_property_parking', true ),
                    'land_size'   => get_post_meta( $post_id, '_property_land_size', true ),
                    'building_size' => get_post_meta( $post_id, '_property_building_size', true ),
                    'address'     => get_post_meta( $post_id, '_property_address', true ),
                    'type'        => ! is_wp_error( $terms_type ) ? $terms_type : array(),
                    'status'      => ! is_wp_error( $terms_status ) ? $terms_status : array(),
                    'location'    => ! is_wp_error( $terms_loc ) ? $terms_loc : array(),
                    'date'        => get_the_date( 'c' ),
                );
            }
            wp_reset_postdata();
        }

        return rest_ensure_response( array(
            'properties'  => $properties,
            'total'       => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'page'        => absint( $args['paged'] ),
        ) );
    }
}
