<?php
/**
 * Template — 404.php
 *
 * 404 error page template.
 *
 * @package RealEstate_Child
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="re-404" id="main-content">
	<div class="re-container re-text-center">

		<div class="re-404-number">404</div>

		<h1><?php esc_html_e( 'Page Not Found', 'realestate-child' ); ?></h1>

		<p><?php esc_html_e( 'The page you\'re looking for doesn\'t exist or has been moved. Let us help you find what you need.', 'realestate-child' ); ?></p>

		<div style="display: flex; gap: var(--re-space-md); justify-content: center; flex-wrap: wrap;">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="re-btn re-btn-primary">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
				<?php esc_html_e( 'Go Home', 'realestate-child' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/properties/' ) ); ?>" class="re-btn re-btn-outline-gold">
				<?php esc_html_e( 'Browse Properties', 'realestate-child' ); ?>
			</a>
		</div>

		<!-- Search Form -->
		<div style="max-width: 500px; margin: var(--re-space-3xl) auto 0;">
			<p style="color: var(--re-text-light); margin-bottom: var(--re-space-md);"><?php esc_html_e( 'Or try searching:', 'realestate-child' ); ?></p>
			<?php get_search_form(); ?>
		</div>

	</div>
</main>

<?php
get_footer();
