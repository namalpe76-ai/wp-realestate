<?php
/**
 * Template — front-page.php
 *
 * Landing page template for the real estate homepage.
 *
 * @package RealEstate_Child
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- Hero Section -->
<section class="re-hero" id="re-hero" style="background-image: url('<?php echo esc_url( REAL_ESTATE_CHILD_URI . '/assets/images/hero-bg.jpg' ); ?>');">
	<div class="re-hero-content">
		<span class="re-hero-subtitle"><?php esc_html_e( 'Premium Real Estate', 'realestate-child' ); ?></span>
		<h1 class="re-hero-title"><?php esc_html_e( 'Find Your Perfect Property', 'realestate-child' ); ?></h1>
		<p class="re-hero-desc"><?php esc_html_e( 'Discover exceptional properties that match your lifestyle. From luxury apartments to family homes, we help you find the place you\'ll love coming home to.', 'realestate-child' ); ?></p>
		<div class="re-hero-actions">
			<a href="<?php echo esc_url( home_url( '/properties/' ) ); ?>" class="re-btn re-btn-primary re-btn-lg">
				<?php esc_html_e( 'Browse Properties', 'realestate-child' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="re-btn re-btn-secondary re-btn-lg">
				<?php esc_html_e( 'Contact Us', 'realestate-child' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- Property Search Section -->
<section class="re-section" id="re-property-search" style="margin-top: -60px; position: relative; z-index: 3;">
	<div class="re-container">
		<div class="re-search-box">
			<div class="re-search-tabs">
				<button class="re-search-tab active" data-tab="buy"><?php esc_html_e( 'Buy', 'realestate-child' ); ?></button>
				<button class="re-search-tab" data-tab="rent"><?php esc_html_e( 'Rent', 'realestate-child' ); ?></button>
				<button class="re-search-tab" data-tab="new"><?php esc_html_e( 'New Projects', 'realestate-child' ); ?></button>
			</div>
			<form class="re-search-form" action="<?php echo esc_url( home_url( '/properties/' ) ); ?>" method="get">
				<div class="re-search-field">
					<label for="re-location"><?php esc_html_e( 'Location', 'realestate-child' ); ?></label>
					<input type="text" id="re-location" name="location" placeholder="<?php esc_attr_e( 'Enter city, area...', 'realestate-child' ); ?>">
				</div>
				<div class="re-search-field">
					<label for="re-property-type"><?php esc_html_e( 'Property Type', 'realestate-child' ); ?></label>
					<select id="re-property-type" name="property_type">
						<option value=""><?php esc_html_e( 'All Types', 'realestate-child' ); ?></option>
						<option value="apartment"><?php esc_html_e( 'Apartment', 'realestate-child' ); ?></option>
						<option value="house"><?php esc_html_e( 'House', 'realestate-child' ); ?></option>
						<option value="villa"><?php esc_html_e( 'Villa', 'realestate-child' ); ?></option>
						<option value="condo"><?php esc_html_e( 'Condo', 'realestate-child' ); ?></option>
						<option value="land"><?php esc_html_e( 'Land', 'realestate-child' ); ?></option>
						<option value="commercial"><?php esc_html_e( 'Commercial', 'realestate-child' ); ?></option>
					</select>
				</div>
				<div class="re-search-field">
					<label for="re-price-range"><?php esc_html_e( 'Price Range', 'realestate-child' ); ?></label>
					<select id="re-price-range" name="price_range">
						<option value=""><?php esc_html_e( 'Select Range', 'realestate-child' ); ?></option>
						<option value="0-500000"><?php esc_html_e( 'Up to $500,000', 'realestate-child' ); ?></option>
						<option value="500000-1000000"><?php esc_html_e( '$500K - $1M', 'realestate-child' ); ?></option>
						<option value="1000000-2000000"><?php esc_html_e( '$1M - $2M', 'realestate-child' ); ?></option>
						<option value="2000000-5000000"><?php esc_html_e( '$2M - $5M', 'realestate-child' ); ?></option>
						<option value="5000000-"><?php esc_html_e( '$5M+', 'realestate-child' ); ?></option>
					</select>
				</div>
				<div class="re-search-field">
					<label for="re-bedrooms"><?php esc_html_e( 'Bedrooms', 'realestate-child' ); ?></label>
					<select id="re-bedrooms" name="bedrooms">
						<option value=""><?php esc_html_e( 'Any', 'realestate-child' ); ?></option>
						<option value="1">1+</option>
						<option value="2">2+</option>
						<option value="3">3+</option>
						<option value="4">4+</option>
						<option value="5">5+</option>
					</select>
				</div>
				<button type="submit" class="re-btn re-btn-primary">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
					<?php esc_html_e( 'Search', 'realestate-child' ); ?>
				</button>
			</form>
		</div>
	</div>
</section>

<!-- Date / Weather Widgets Section -->
<section class="re-section re-section-alt" id="re-datetime-weather-section">
	<div class="re-container">
		<div class="re-datetime-weather">
			<div class="re-widget-box">
				<h3>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
					<?php esc_html_e( 'Date & Time', 'realestate-child' ); ?>
				</h3>
				<div id="realestate-datetime">
					<p><?php esc_html_e( 'Loading date & time...', 'realestate-child' ); ?></p>
				</div>
			</div>
			<div class="re-widget-box">
				<h3>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 18a5 5 0 0 0-10 0"/><line x1="12" y1="9" x2="12" y2="2"/><line x1="4.22" y1="10.22" x2="5.64" y2="11.64"/><line x1="1" y1="18" x2="3" y2="18"/><line x1="21" y1="18" x2="23" y2="18"/><line x1="18.36" y1="11.64" x2="19.78" y2="10.22"/><line x1="23" y1="22" x2="1" y2="22"/><polyline points="16 5 12 9 8 5"/></svg>
					<?php esc_html_e( 'Weather', 'realestate-child' ); ?>
				</h3>
				<div id="realestate-weather">
					<p><?php esc_html_e( 'Loading weather data...', 'realestate-child' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Statistics / Counter Section -->
<section class="re-stats-section" id="re-stats-section">
	<div class="re-container">
		<div id="realestate-stats">
			<div class="re-stats-grid">
				<div class="re-stat-item">
					<div class="re-stat-number" data-count="500">0</div>
					<div class="re-stat-label"><?php esc_html_e( 'Properties Listed', 'realestate-child' ); ?></div>
				</div>
				<div class="re-stat-item">
					<div class="re-stat-number" data-count="350">0</div>
					<div class="re-stat-label"><?php esc_html_e( 'Happy Clients', 'realestate-child' ); ?></div>
				</div>
				<div class="re-stat-item">
					<div class="re-stat-number" data-count="12">0</div>
					<div class="re-stat-label"><?php esc_html_e( 'Years Experience', 'realestate-child' ); ?></div>
				</div>
				<div class="re-stat-item">
					<div class="re-stat-number" data-count="45">0</div>
					<div class="re-stat-label"><?php esc_html_e( 'Team Members', 'realestate-child' ); ?></div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Featured Properties Section -->
<section class="re-section" id="re-featured-properties">
	<div class="re-container">
		<div class="re-section-header">
			<span class="re-section-label"><?php esc_html_e( 'Featured Properties', 'realestate-child' ); ?></span>
			<h2 class="re-section-title"><?php esc_html_e( 'Explore Our Top Picks', 'realestate-child' ); ?></h2>
			<p class="re-section-desc"><?php esc_html_e( 'Handpicked properties that offer the best value, location, and lifestyle for discerning buyers.', 'realestate-child' ); ?></p>
		</div>

		<div class="re-properties-grid">
			<?php
			// Load property card template part for each featured property.
			// This is a static fallback — works without a properties CPT.
			$featured_args = array(
				'post_type'      => array( 'post', 'property' ),
				'posts_per_page' => 6,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post_status'    => 'publish',
			);

			$featured_query = new WP_Query( $featured_args );

			if ( $featured_query->have_posts() ) :
				while ( $featured_query->have_posts() ) :
					$featured_query->the_post();
					set_query_var( 're_property_id', get_the_ID() );
					set_query_var( 're_property_title', get_the_title() );
					set_query_var( 're_property_excerpt', get_the_excerpt() );
					set_query_var( 're_property_permalink', get_the_permalink() );
					set_query_var( 're_property_image', get_the_post_thumbnail_url( get_the_ID(), 'medium_large' ) );
					set_query_var( 're_property_badge', 'Sale' );
					set_query_var( 're_property_price', '' );
					set_query_var( 're_property_location', '' );
					set_query_var( 're_property_bedrooms', '' );
					set_query_var( 're_property_bathrooms', '' );
					set_query_var( 're_property_parking', '' );
					set_query_var( 're_property_landsize', '' );
					get_template_part( 'template-parts/content', 'property-card' );
				endwhile;
				wp_reset_postdata();
			else :
				// No properties — display placeholder cards.
				for ( $i = 1; $i <= 3; $i++ ) :
					$placeholders = array(
						array(
							'title'    => esc_html__( 'Modern Luxury Villa', 'realestate-child' ),
							'location' => esc_html__( 'Beverly Hills, CA', 'realestate-child' ),
							'price'    => esc_html__( '$1,250,000', 'realestate-child' ),
							'beds'     => 4,
							'baths'    => 3,
							'parking'  => 2,
							'land'     => '500 m²',
							'badge'    => 'Sale',
						),
						array(
							'title'    => esc_html__( 'Downtown Penthouse', 'realestate-child' ),
							'location' => esc_html__( 'Manhattan, NY', 'realestate-child' ),
							'price'    => esc_html__( '$2,800,000', 'realestate-child' ),
							'beds'     => 3,
							'baths'    => 2,
							'parking'  => 1,
							'land'     => '320 m²',
							'badge'    => 'Sale',
						),
						array(
							'title'    => esc_html__( 'Seaside Family Home', 'realestate-child' ),
							'location' => esc_html__( 'Malibu, CA', 'realestate-child' ),
							'price'    => esc_html__( '$850,000', 'realestate-child' ),
							'beds'     => 5,
							'baths'    => 4,
							'parking'  => 3,
							'land'     => '750 m²',
							'badge'    => 'Sale',
						),
					);
					$ph = $placeholders[ $i - 1 ];
					?>
					<div class="re-property-card">
						<div class="re-property-card-image">
							<img src="<?php echo esc_url( REAL_ESTATE_CHILD_URI . '/assets/images/property-placeholder.jpg' ); ?>" alt="<?php echo esc_attr( $ph['title'] ); ?>" loading="lazy">
							<div class="re-property-badge">
								<span class="re-badge re-badge-sale"><?php echo esc_html( $ph['badge'] ); ?></span>
							</div>
						</div>
						<div class="re-property-card-body">
							<h3 class="re-property-card-title">
								<a href="#"><?php echo esc_html( $ph['title'] ); ?></a>
							</h3>
							<div class="re-property-location">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
								<span><?php echo esc_html( $ph['location'] ); ?></span>
							</div>
							<div class="re-property-price"><?php echo esc_html( $ph['price'] ); ?></div>
							<div class="re-property-features">
								<span class="re-property-feature">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v11a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7"/><path d="M21 11H3V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4z"/><path d="M3 11v6"/></svg>
									<?php printf( esc_html__( '%s Beds', 'realestate-child' ), $ph['beds'] ); ?>
								</span>
								<span class="re-property-feature">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16a1 1 0 0 1 1 1v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a1 1 0 0 1 1-1z"/><path d="M6 12V5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v7"/><line x1="4" y1="18" x2="4" y2="20"/><line x1="20" y1="18" x2="20" y2="20"/></svg>
									<?php printf( esc_html__( '%s Baths', 'realestate-child' ), $ph['baths'] ); ?>
								</span>
								<span class="re-property-feature">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/></svg>
									<?php printf( esc_html__( '%s Parking', 'realestate-child' ), $ph['parking'] ); ?>
								</span>
								<span class="re-property-feature">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
									<?php echo esc_html( $ph['land'] ); ?>
								</span>
							</div>
						</div>
						<div class="re-property-card-footer">
							<a href="#" class="re-btn re-btn-outline-gold re-btn-sm" style="width: 100%;">
								<?php esc_html_e( 'Enquire', 'realestate-child' ); ?>
							</a>
						</div>
					</div>
					<?php
				endfor;
			endif;
			?>
		</div>

		<div class="re-text-center re-mt-3">
			<a href="<?php echo esc_url( home_url( '/properties/' ) ); ?>" class="re-btn re-btn-primary">
				<?php esc_html_e( 'View All Properties', 'realestate-child' ); ?>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
			</a>
		</div>
	</div>
</section>

<!-- Services Overview Section -->
<section class="re-section re-section-alt" id="re-services">
	<div class="re-container">
		<div class="re-section-header">
			<span class="re-section-label"><?php esc_html_e( 'Our Services', 'realestate-child' ); ?></span>
			<h2 class="re-section-title"><?php esc_html_e( 'What We Offer', 'realestate-child' ); ?></h2>
			<p class="re-section-desc"><?php esc_html_e( 'Comprehensive real estate solutions tailored to meet your unique needs and goals.', 'realestate-child' ); ?></p>
		</div>

		<div class="re-services-grid">
			<div class="re-service-card">
				<div class="re-service-icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
				</div>
				<h3><?php esc_html_e( 'Property Buying', 'realestate-child' ); ?></h3>
				<p><?php esc_html_e( 'Expert guidance through every step of the property buying process, from search to closing.', 'realestate-child' ); ?></p>
			</div>
			<div class="re-service-card">
				<div class="re-service-icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
				</div>
				<h3><?php esc_html_e( 'Property Selling', 'realestate-child' ); ?></h3>
				<p><?php esc_html_e( 'Strategic marketing and pricing to sell your property quickly and at the best possible price.', 'realestate-child' ); ?></p>
			</div>
			<div class="re-service-card">
				<div class="re-service-icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
				</div>
				<h3><?php esc_html_e( 'Property Leasing', 'realestate-child' ); ?></h3>
				<p><?php esc_html_e( 'Comprehensive rental solutions for both landlords and tenants with market-leading expertise.', 'realestate-child' ); ?></p>
			</div>
			<div class="re-service-card">
				<div class="re-service-icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
				</div>
				<h3><?php esc_html_e( 'Investment Advisory', 'realestate-child' ); ?></h3>
				<p><?php esc_html_e( 'Data-driven investment guidance to help you build and manage a profitable real estate portfolio.', 'realestate-child' ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- Testimonial Section -->
<section class="re-section" id="re-testimonials">
	<div class="re-container">
		<div class="re-section-header">
			<span class="re-section-label"><?php esc_html_e( 'Testimonials', 'realestate-child' ); ?></span>
			<h2 class="re-section-title"><?php esc_html_e( 'What Our Clients Say', 'realestate-child' ); ?></h2>
		</div>

		<div class="re-testimonials-grid">
			<div class="re-testimonial-card">
				<p class="re-testimonial-text"><?php esc_html_e( '"Outstanding service from start to finish. They found us the perfect family home within our budget. Highly recommended!"', 'realestate-child' ); ?></p>
				<div class="re-testimonial-author">
					<div class="re-testimonial-avatar">JR</div>
					<div>
						<p class="re-testimonial-name"><?php esc_html_e( 'James Rodriguez', 'realestate-child' ); ?></p>
						<p class="re-testimonial-role"><?php esc_html_e( 'Home Buyer', 'realestate-child' ); ?></p>
					</div>
				</div>
			</div>
			<div class="re-testimonial-card">
				<p class="re-testimonial-text"><?php esc_html_e( '"Professional, responsive, and truly cares about their clients. Our property was sold above asking price in just two weeks!"', 'realestate-child' ); ?></p>
				<div class="re-testimonial-author">
					<div class="re-testimonial-avatar">SC</div>
					<div>
						<p class="re-testimonial-name"><?php esc_html_e( 'Sarah Chen', 'realestate-child' ); ?></p>
						<p class="re-testimonial-role"><?php esc_html_e( 'Property Seller', 'realestate-child' ); ?></p>
					</div>
				</div>
			</div>
			<div class="re-testimonial-card">
				<p class="re-testimonial-text"><?php esc_html_e( '"Their investment advisory service helped me make informed decisions. My portfolio has grown significantly in just one year." ', 'realestate-child' ); ?></p>
				<div class="re-testimonial-author">
					<div class="re-testimonial-avatar">MK</div>
					<div>
						<p class="re-testimonial-name"><?php esc_html_e( 'Michael Kim', 'realestate-child' ); ?></p>
						<p class="re-testimonial-role"><?php esc_html_e( 'Investor', 'realestate-child' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- CTA Banner Section -->
<section class="re-cta-banner" id="re-cta-banner">
	<div class="re-container">
		<h2><?php esc_html_e( 'Ready to Find Your Dream Property?', 'realestate-child' ); ?></h2>
		<p><?php esc_html_e( 'Let our expert team help you navigate the real estate market and find the perfect property for your needs.', 'realestate-child' ); ?></p>
		<div class="re-cta-banner-actions">
			<a href="<?php echo esc_url( home_url( '/properties/' ) ); ?>" class="re-btn re-btn-primary re-btn-lg">
				<?php esc_html_e( 'Browse Properties', 'realestate-child' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="re-btn re-btn-secondary re-btn-lg">
				<?php esc_html_e( 'Schedule a Consultation', 'realestate-child' ); ?>
			</a>
		</div>
	</div>
</section>

<?php
get_footer();
