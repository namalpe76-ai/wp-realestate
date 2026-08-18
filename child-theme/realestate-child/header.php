<?php
/**
 * Template — header.php
 *
 * Sticky site header with logo, navigation, CTA, and mobile toggle.
 *
 * @package RealEstate_Child
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Site Header -->
<header class="re-site-header" id="re-site-header" role="banner">
	<div class="re-container">

		<!-- Logo -->
		<div class="re-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<span class="re-logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<!-- Primary Navigation -->
		<nav class="re-primary-nav" id="re-primary-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'realestate-child' ); ?>">
			<?php
			if ( has_nav_menu( 'primary-menu' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary-menu',
					'container'      => false,
					'menu_class'     => 're-nav-list',
					'fallback_cb'    => false,
					'depth'          => 2,
					'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				) );
			} else {
				// Fallback menu items.
				?>
				<ul class="re-nav-list">
					<li class="menu-item current-menu-item"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'realestate-child' ); ?></a></li>
					<li class="menu-item"><a href="<?php echo esc_url( home_url( '/properties/' ) ); ?>"><?php esc_html_e( 'Properties', 'realestate-child' ); ?></a></li>
					<li class="menu-item"><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Services', 'realestate-child' ); ?></a></li>
					<li class="menu-item"><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'realestate-child' ); ?></a></li>
					<li class="menu-item"><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'realestate-child' ); ?></a></li>
				</ul>
				<?php
			}
			?>
		</nav>

		<!-- Header CTA -->
		<div class="re-header-cta">
			<a href="<?php echo esc_url( home_url( '/properties/' ) ); ?>" class="re-btn re-btn-primary re-btn-sm">
				<?php esc_html_e( 'Find a Property', 'realestate-child' ); ?>
			</a>
		</div>

		<!-- Mobile Menu Toggle -->
		<button class="re-menu-toggle" id="re-menu-toggle" aria-controls="re-primary-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle Navigation', 'realestate-child' ); ?>">
			<span class="re-hamburger"></span>
		</button>

	</div>
</header>

<!-- Mobile Navigation Overlay -->
<div class="re-nav-overlay" id="re-nav-overlay"></div>

<!-- Site Content Wrapper -->
<div id="re-site-content">
