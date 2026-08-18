<?php
/**
 * Template Name: Property Search Results
 *
 * @package realestate-core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$has_search = ! empty( $_GET['type'] ) || ! empty( $_GET['status'] ) || ! empty( $_GET['location'] )
    || ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] )
    || ! empty( $_GET['bedrooms'] ) || ! empty( $_GET['bathrooms'] ) || ! empty( $_GET['min_size'] );

$search_query = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
?>
<div class="re-search-page">

    <div class="re-search-page__header">
        <h1 class="re-search-page__title"><?php esc_html_e( 'Property Search', 'realestate-core' ); ?></h1>
    </div>

    <?php echo do_shortcode( '[property_search layout="horizontal" show_title="false"]' ); ?>

    <div class="re-search-page__results">
        <?php echo do_shortcode( '[property_results]' ); ?>
    </div>

</div>
<?php

get_footer();
