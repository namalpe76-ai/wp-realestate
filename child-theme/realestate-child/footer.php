<?php
/**
 * Template — footer.php
 *
 * Professional footer with four-column layout, social links,
 * newsletter signup, copyright, and back-to-top button.
 *
 * @package RealEstate_Child
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_year = gmdate( 'Y' );
$site_name    = get_bloginfo( 'name' );
$site_url     = home_url( '/' );
?>

<!-- Site Footer -->
<footer class="re-site-footer" role="contentinfo">

	<!-- Footer Main Columns -->
	<div class="re-container">
		<div class="re-footer-main">

			<!-- Column 1 — Company Info -->
			<div class="re-footer-col re-footer-about">
				<?php if ( has_custom_logo() ) : ?>
					<div class="re-logo re-logo-footer" style="margin-bottom: var(--re-space-lg);">
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<a href="<?php echo esc_url( $site_url ); ?>">
							<span class="re-logo-text re-text-white"><?php echo esc_html( $site_name ); ?></span>
						</a>
					<?php endif; ?>
				</div>
				<p><?php esc_html_e( 'Your trusted partner in finding the perfect property. We provide premium real estate services with a commitment to excellence and client satisfaction.', 'realestate-child' ); ?></p>

				<div class="re-footer-social">
					<a href="#" aria-label="Facebook" title="Facebook">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
					</a>
					<a href="#" aria-label="Twitter" title="Twitter">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
					</a>
					<a href="#" aria-label="Instagram" title="Instagram">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
					</a>
					<a href="#" aria-label="LinkedIn" title="LinkedIn">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
					</a>
				</div>
			</div>

			<!-- Column 2 — Quick Links -->
			<div class="re-footer-col">
				<h4><?php esc_html_e( 'Quick Links', 'realestate-child' ); ?></h4>
				<?php
				if ( has_nav_menu( 'footer-menu' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'footer-menu',
						'container'      => false,
						'menu_class'     => 're-footer-links',
						'fallback_cb'    => false,
						'depth'          => 1,
					) );
				} else {
					?>
					<ul class="re-footer-links">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'realestate-child' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/properties/' ) ); ?>"><?php esc_html_e( 'Properties', 'realestate-child' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'realestate-child' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'realestate-child' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'realestate-child' ); ?></a></li>
					</ul>
					<?php
				}
				?>
			</div>

			<!-- Column 3 — Services -->
			<div class="re-footer-col">
				<h4><?php esc_html_e( 'Services', 'realestate-child' ); ?></h4>
				<ul class="re-footer-links">
					<li><a href="#"><?php esc_html_e( 'Property Buying', 'realestate-child' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Property Selling', 'realestate-child' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Property Leasing', 'realestate-child' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Property Management', 'realestate-child' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Investment Advisory', 'realestate-child' ); ?></a></li>
				</ul>
			</div>

			<!-- Column 4 — Contact & Newsletter -->
			<div class="re-footer-col">
				<h4><?php esc_html_e( 'Contact Us', 'realestate-child' ); ?></h4>

				<div class="re-footer-contact-item">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
					<span><?php esc_html_e( '123 Business Avenue, Suite 100', 'realestate-child' ); ?><br><?php esc_html_e( 'City, State 12345', 'realestate-child' ); ?></span>
				</div>

				<div class="re-footer-contact-item">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
					<span><a href="tel:+11234567890" style="color: inherit;">+1 (123) 456-7890</a></span>
				</div>

				<div class="re-footer-contact-item">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
					<span><a href="mailto:info@11aarealestate.com" style="color: inherit;">info@11aarealestate.com</a></span>
				</div>

				<!-- Newsletter Signup -->
				<h4 style="margin-top: var(--re-space-xl);"><?php esc_html_e( 'Newsletter', 'realestate-child' ); ?></h4>
				<form class="re-newsletter-form" action="#" method="post">
					<?php wp_nonce_field( 're_newsletter', 're_nonce' ); ?>
					<input type="email" name="re_newsletter_email" placeholder="<?php esc_attr_e( 'Your email address', 'realestate-child' ); ?>" required>
					<button type="submit"><?php esc_html_e( 'Subscribe', 'realestate-child' ); ?></button>
				</form>
			</div>

		</div>

		<!-- Footer Bottom -->
		<div class="re-footer-bottom">
			<p class="re-footer-copyright">
				&copy; <?php echo esc_html( $current_year ); ?> <?php echo esc_html( $site_name ); ?>. <?php esc_html_e( 'All Rights Reserved.', 'realestate-child' ); ?>
			</p>
			<div class="re-footer-legal">
				<a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy Policy', 'realestate-child' ); ?></a>
				<a href="#"><?php esc_html_e( 'Terms of Service', 'realestate-child' ); ?></a>
			</div>
		</div>
	</div>

</footer>

<!-- Back to Top Button -->
<button class="re-back-to-top" id="re-back-to-top" aria-label="<?php esc_attr_e( 'Back to Top', 'realestate-child' ); ?>">
	<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
