<?php
/**
 * Template Part — content-property-card.php
 *
 * Reusable property card component. Uses query vars set by the
 * calling template or falls back to The Loop context.
 *
 * @package RealEstate_Child
 * @since   1.0.0
 *
 * Expected query vars (set via set_query_var before get_template_part):
 *   re_property_id         — Post ID
 *   re_property_title      — Title string
 *   re_property_excerpt    — Excerpt string
 *   re_property_permalink  — URL string
 *   re_property_image      — Image URL string
 *   re_property_badge      — "Sale" | "Rent"
 *   re_property_price      — Price string
 *   re_property_location   — Location string
 *   re_property_bedrooms   — Number
 *   re_property_bathrooms  — Number
 *   re_property_parking    — Number
 *   re_property_landsize   — Land size string
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Resolve values from query vars or The Loop.
$card_id       = get_query_var( 're_property_id', get_the_ID() );
$card_title    = get_query_var( 're_property_title', get_the_title() );
$card_excerpt  = get_query_var( 're_property_excerpt', get_the_excerpt() );
$card_url      = get_query_var( 're_property_permalink', get_the_permalink() );
$card_image    = get_query_var( 're_property_image', get_the_post_thumbnail_url( $card_id, 'medium_large' ) );
$card_badge    = get_query_var( 're_property_badge', '' );
$card_price    = get_query_var( 're_property_price', '' );
$card_location = get_query_var( 're_property_location', '' );
$card_beds     = get_query_var( 're_property_bedrooms', '' );
$card_baths    = get_query_var( 're_property_bathrooms', '' );
$card_parking  = get_query_var( 're_property_parking', '' );
$card_land     = get_query_var( 're_property_landsize', '' );

// Fallback image if none set.
if ( empty( $card_image ) ) {
	$card_image = esc_url( REAL_ESTATE_CHILD_URI . '/assets/images/property-placeholder.jpg' );
}

// Badge class mapping.
$badge_class = 're-badge-sale';
if ( 'Rent' === $card_badge || 'rent' === $card_badge ) {
	$badge_class = 're-badge-rent';
}

/**
 * Allow overriding card output via filter.
 *
 * @param string|null $card_html  Pre-rendered HTML or null.
 * @param int         $card_id    Post ID.
 */
$pre_render = apply_filters( 'realestate_property_card_before_render', null, $card_id );
if ( null !== $pre_render ) {
	echo $pre_render; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>

<div class="re-property-card" data-property-id="<?php echo esc_attr( $card_id ); ?>">

	<!-- Property Image -->
	<div class="re-property-card-image">
		<a href="<?php echo esc_url( $card_url ); ?>">
			<img
				src="<?php echo esc_url( $card_image ); ?>"
				alt="<?php echo esc_attr( $card_title ); ?>"
				loading="lazy"
				width="400"
				height="240"
			>
		</a>

		<!-- Badges -->
		<div class="re-property-badge">
			<?php if ( ! empty( $card_badge ) ) : ?>
				<span class="re-badge <?php echo esc_attr( $badge_class ); ?>">
					<?php echo esc_html( $card_badge ); ?>
				</span>
			<?php endif; ?>

			<?php if ( $card_id ) : ?>
				<span class="re-badge re-badge-id">
					<?php
					printf(
						/* translators: %s: property ID */
						esc_html__( 'ID: %s', 'realestate-child' ),
						esc_html( '#' . $card_id )
					);
					?>
				</span>
			<?php endif; ?>
		</div>
	</div>

	<!-- Card Body -->
	<div class="re-property-card-body">

		<!-- Title -->
		<h3 class="re-property-card-title">
			<a href="<?php echo esc_url( $card_url ); ?>">
				<?php echo esc_html( $card_title ); ?>
			</a>
		</h3>

		<!-- Location -->
		<?php if ( ! empty( $card_location ) ) : ?>
			<div class="re-property-location">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
					<circle cx="12" cy="10" r="3"/>
				</svg>
				<span><?php echo esc_html( $card_location ); ?></span>
			</div>
		<?php endif; ?>

		<!-- Price -->
		<?php if ( ! empty( $card_price ) ) : ?>
			<div class="re-property-price">
				<?php echo esc_html( $card_price ); ?>
			</div>
		<?php endif; ?>

		<!-- Features (Bedrooms, Bathrooms, Parking, Land) -->
		<div class="re-property-features">
			<?php if ( '' !== $card_beds && null !== $card_beds ) : ?>
				<span class="re-property-feature">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 7v11a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7"/>
						<path d="M21 11H3V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4z"/>
						<path d="M3 11v6"/>
					</svg>
					<span><?php printf( esc_html__( '%s Beds', 'realestate-child' ), esc_html( $card_beds ) ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( '' !== $card_baths && null !== $card_baths ) : ?>
				<span class="re-property-feature">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4 12h16a1 1 0 0 1 1 1v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a1 1 0 0 1 1-1z"/>
						<path d="M6 12V5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v7"/>
						<line x1="4" y1="18" x2="4" y2="20"/>
						<line x1="20" y1="18" x2="20" y2="20"/>
					</svg>
					<span><?php printf( esc_html__( '%s Baths', 'realestate-child' ), esc_html( $card_baths ) ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( '' !== $card_parking && null !== $card_parking ) : ?>
				<span class="re-property-feature">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<rect x="3" y="3" width="18" height="18" rx="2"/>
						<path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
					</svg>
					<span><?php printf( esc_html__( '%s Parking', 'realestate-child' ), esc_html( $card_parking ) ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $card_land ) ) : ?>
				<span class="re-property-feature">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 21h18"/>
						<path d="M5 21V7l7-4 7 4v14"/>
					</svg>
					<span><?php echo esc_html( $card_land ); ?></span>
				</span>
			<?php endif; ?>
		</div>

	</div>

	<!-- Card Footer — Enquire Button -->
	<div class="re-property-card-footer">
		<a
			href="<?php echo esc_url( add_query_arg( 'property_id', $card_id, home_url( '/enquire/' ) ) ); ?>"
			class="re-btn re-btn-outline-gold re-btn-sm"
			style="width: 100%;"
		>
			<?php esc_html_e( 'Enquire', 'realestate-child' ); ?>
		</a>
	</div>

</div>
