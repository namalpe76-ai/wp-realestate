<?php
/**
 * Template Name: Property Archive
 * Template Post Type: property
 *
 * @package realestate-core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;

// Determine sort from URL.
$sort = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : 'newest';

// Build query args.
$args = array(
    'post_type'      => 'property',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => $paged,
);

$meta_query = array();
$tax_query  = array();

if ( ! empty( $_GET['type'] ) ) {
    $tax_query[] = array(
        'taxonomy' => 'property_type',
        'field'    => 'slug',
        'terms'    => sanitize_text_field( wp_unslash( $_GET['type'] ) ),
    );
}
if ( ! empty( $_GET['status'] ) ) {
    $tax_query[] = array(
        'taxonomy' => 'property_status',
        'field'    => 'slug',
        'terms'    => sanitize_text_field( wp_unslash( $_GET['status'] ) ),
    );
}
if ( ! empty( $_GET['location'] ) ) {
    $tax_query[] = array(
        'taxonomy' => 'property_location',
        'field'    => 'slug',
        'terms'    => sanitize_text_field( wp_unslash( $_GET['location'] ) ),
    );
}
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
if ( ! empty( $_GET['bedrooms'] ) ) {
    $meta_query[] = array(
        'key'     => '_property_bedrooms',
        'value'   => absint( $_GET['bedrooms'] ),
        'type'    => 'NUMERIC',
        'compare' => '>=',
    );
}
if ( ! empty( $_GET['bathrooms'] ) ) {
    $meta_query[] = array(
        'key'     => '_property_bathrooms',
        'value'   => absint( $_GET['bathrooms'] ),
        'type'    => 'NUMERIC',
        'compare' => '>=',
    );
}
if ( ! empty( $_GET['min_size'] ) ) {
    $meta_query[] = array(
        'key'     => '_property_building_size',
        'value'   => absint( $_GET['min_size'] ),
        'type'    => 'NUMERIC',
        'compare' => '>=',
    );
}

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

// Check for active filters.
$has_filters = ! empty( $_GET['type'] ) || ! empty( $_GET['status'] ) || ! empty( $_GET['location'] )
    || ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] )
    || ! empty( $_GET['bedrooms'] ) || ! empty( $_GET['bathrooms'] ) || ! empty( $_GET['min_size'] );

$reset_url = remove_query_arg( array( 'type', 'status', 'location', 'min_price', 'max_price', 'bedrooms', 'bathrooms', 'min_size', 'sort' ) );
?>
<div class="re-properties-archive">

    <div class="re-properties-archive__header">
        <h1 class="re-properties-archive__title"><?php esc_html_e( 'Properties', 'realestate-core' ); ?></h1>
    </div>

    <!-- Search Form -->
    <?php echo do_shortcode( '[property_search layout="horizontal"]' ); ?>

    <!-- Active Filters -->
    <?php if ( $has_filters ) : ?>
        <div class="re-active-filters">
            <span class="re-active-filters__label"><?php esc_html_e( 'Active Filters:', 'realestate-core' ); ?></span>
            <?php if ( ! empty( $_GET['type'] ) ) :
                $term = get_term_by( 'slug', sanitize_text_field( wp_unslash( $_GET['type'] ) ), 'property_type' );
                if ( $term ) : ?>
                    <span class="re-active-filters__tag">
                        <?php echo esc_html( $term->name ); ?>
                        <a href="<?php echo esc_url( remove_query_arg( 'type' ) ); ?>" class="re-active-filters__remove" aria-label="<?php esc_attr_e( 'Remove filter', 'realestate-core' ); ?>">&times;</a>
                    </span>
                <?php endif;
            endif; ?>
            <?php if ( ! empty( $_GET['status'] ) ) :
                $term = get_term_by( 'slug', sanitize_text_field( wp_unslash( $_GET['status'] ) ), 'property_status' );
                if ( $term ) : ?>
                    <span class="re-active-filters__tag">
                        <?php echo esc_html( $term->name ); ?>
                        <a href="<?php echo esc_url( remove_query_arg( 'status' ) ); ?>" class="re-active-filters__remove" aria-label="<?php esc_attr_e( 'Remove filter', 'realestate-core' ); ?>">&times;</a>
                    </span>
                <?php endif;
            endif; ?>
            <?php if ( ! empty( $_GET['location'] ) ) :
                $term = get_term_by( 'slug', sanitize_text_field( wp_unslash( $_GET['location'] ) ), 'property_location' );
                if ( $term ) : ?>
                    <span class="re-active-filters__tag">
                        <?php echo esc_html( $term->name ); ?>
                        <a href="<?php echo esc_url( remove_query_arg( 'location' ) ); ?>" class="re-active-filters__remove" aria-label="<?php esc_attr_e( 'Remove filter', 'realestate-core' ); ?>">&times;</a>
                    </span>
                <?php endif;
            endif; ?>
            <?php if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) :
                $min_p = ! empty( $_GET['min_price'] ) ? absint( $_GET['min_price'] ) : '0';
                $max_p = ! empty( $_GET['max_price'] ) ? absint( $_GET['max_price'] ) : '∞';
                ?>
                <span class="re-active-filters__tag">
                    <?php printf( esc_html__( 'Price: %s - %s', 'realestate-core' ), esc_html( $min_p ), esc_html( $max_p ) ); ?>
                    <a href="<?php echo esc_url( remove_query_arg( array( 'min_price', 'max_price' ) ) ); ?>" class="re-active-filters__remove" aria-label="<?php esc_attr_e( 'Remove filter', 'realestate-core' ); ?>">&times;</a>
                </span>
            <?php endif; ?>
            <a href="<?php echo esc_url( $reset_url ); ?>" class="re-active-filters__clear"><?php esc_html_e( 'Clear All', 'realestate-core' ); ?></a>
        </div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="re-properties-toolbar">
        <div class="re-properties-toolbar__count">
            <?php
            printf(
                esc_html( _n( '%d property found', '%d properties found', $query->found_posts, 'realestate-core' ) ),
                absint( $query->found_posts )
            );
            ?>
        </div>

        <div class="re-properties-toolbar__controls">
            <div class="re-properties-toolbar__sort">
                <label for="archive-sort"><?php esc_html_e( 'Sort by:', 'realestate-core' ); ?></label>
                <select id="archive-sort" class="re-sort-select">
                    <option value="newest" <?php selected( $sort, 'newest' ); ?>><?php esc_html_e( 'Newest First', 'realestate-core' ); ?></option>
                    <option value="oldest" <?php selected( $sort, 'oldest' ); ?>><?php esc_html_e( 'Oldest First', 'realestate-core' ); ?></option>
                    <option value="price_asc" <?php selected( $sort, 'price_asc' ); ?>><?php esc_html_e( 'Price: Low to High', 'realestate-core' ); ?></option>
                    <option value="price_desc" <?php selected( $sort, 'price_desc' ); ?>><?php esc_html_e( 'Price: High to Low', 'realestate-core' ); ?></option>
                </select>
            </div>

            <div class="re-properties-toolbar__view">
                <button type="button" class="re-view-toggle re-view-toggle--active" data-view="grid" aria-label="<?php esc_attr_e( 'Grid view', 'realestate-core' ); ?>">
                    <span class="dashicons dashicons-grid-view" aria-hidden="true"></span>
                </button>
                <button type="button" class="re-view-toggle" data-view="list" aria-label="<?php esc_attr_e( 'List view', 'realestate-core' ); ?>">
                    <span class="dashicons dashicons-list-view" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Properties Grid -->
    <?php if ( $query->have_posts() ) : ?>
        <div class="re-properties-grid" id="re-properties-grid">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php get_template_part( 'templates/property-card', null, array( 'post_id' => get_the_ID() ) ); ?>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="re-properties-pagination">
            <?php
            echo wp_kses_post( paginate_links( array(
                'total'     => $query->max_num_pages,
                'current'   => $paged,
                'prev_text' => '&laquo; ' . esc_html__( 'Previous', 'realestate-core' ),
                'next_text' => esc_html__( 'Next', 'realestate-core' ) . ' &raquo;',
            ) ) );
            ?>
        </div>
    <?php else : ?>
        <div class="re-properties-empty">
            <div class="re-properties-empty__icon" aria-hidden="true">
                <span class="dashicons dashicons-building" style="font-size:64px;width:64px;height:64px;color:#ccc;"></span>
            </div>
            <h2 class="re-properties-empty__title"><?php esc_html_e( 'No properties found', 'realestate-core' ); ?></h2>
            <p class="re-properties-empty__text">
                <?php esc_html_e( 'Sorry, no properties matched your search criteria. Please try adjusting your filters.', 'realestate-core' ); ?>
            </p>
            <a href="<?php echo esc_url( $reset_url ); ?>" class="re-button re-button--primary"><?php esc_html_e( 'Clear Filters', 'realestate-core' ); ?></a>
        </div>
    <?php endif; ?>
</div>
<?php
wp_reset_postdata();

get_footer();
