<?php
/**
 * Real Estate Child Theme — functions.php
 *
 * Theme setup, enqueue scripts/styles, register menus, sidebars.
 * No business logic — only theme infrastructure.
 *
 * @package RealEstate_Child
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REAL_ESTATE_CHILD_DIR', get_stylesheet_directory() );
define( 'REAL_ESTATE_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * Return a version string for cache busting.
 *
 * @param string $file Optional file path appended as query param.
 * @return string
 */
function real_evaluate_enqueue_version( $file = '' ) {
	$version = '1.0.0';
	$theme   = wp_get_theme();
	if ( $theme && $theme->get( 'Version' ) ) {
		$version = $theme->get( 'Version' );
	}
	return apply_filters( 'realestate_enqueue_version', $version, $file );
}

/**
 * Enqueue parent and child styles plus custom assets.
 */
function realestate_enqueue_assets() {

	/* Parent Astra styles */
	wp_enqueue_style(
		'astra-parent',
		get_template_directory_uri() . '/style.css',
		array(),
		real_evaluate_enqueue_version()
	);

	/* Child styles */
	wp_enqueue_style(
		'realestate-child',
		REAL_ESTATE_CHILD_URI . '/style.css',
		array( 'astra-parent' ),
		real_evaluate_enqueue_version()
	);

	/* Custom child CSS (separate file for clarity) */
	$custom_css_file = REAL_ESTATE_CHILD_URI . '/assets/css/custom.css';
	if ( file_exists( REAL_ESTATE_CHILD_DIR . '/assets/css/custom.css' ) ) {
		wp_enqueue_style(
			'realestate-custom',
			$custom_css_file,
			array( 'realestate-child' ),
			real_evaluate_enqueue_version()
		);
	}

	/* Datetime script */
	$datetime_js = REAL_ESTATE_CHILD_URI . '/assets/js/datetime.js';
	if ( file_exists( REAL_ESTATE_CHILD_DIR . '/assets/js/datetime.js' ) ) {
		wp_enqueue_script(
			'realestate-datetime',
			$datetime_js,
			array(),
			real_evaluate_enqueue_version(),
			true
		);
	}

	/* Weather widget script */
	$weather_js = REAL_ESTATE_CHILD_URI . '/assets/js/weather.js';
	if ( file_exists( REAL_ESTATE_CHILD_DIR . '/assets/js/weather.js' ) ) {
		wp_enqueue_script(
			'realestate-weather',
			$weather_js,
			array(),
			real_evaluate_enqueue_version(),
			true
		);
	}

	/* Analytics script */
	$analytics_js = REAL_ESTATE_CHILD_URI . '/assets/js/analytics.js';
	if ( file_exists( REAL_ESTATE_CHILD_DIR . '/assets/js/analytics.js' ) ) {
		wp_enqueue_script(
			'realestate-analytics',
			$analytics_js,
			array(),
			real_evaluate_enqueue_version(),
			true
		);
	}

	/* Header script (sticky + mobile menu) */
	$header_js = REAL_ESTATE_CHILD_URI . '/assets/js/header.js';
	if ( file_exists( REAL_ESTATE_CHILD_DIR . '/assets/js/header.js' ) ) {
		wp_enqueue_script(
			'realestate-header',
			$header_js,
			array(),
			real_evaluate_enqueue_version(),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'realestate_enqueue_assets' );

/**
 * Theme support declarations.
 */
function realestate_theme_support() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'widgets' );
	add_theme_support( 'custom-background', array(
		'default-color' => 'FFFFFF',
	) );
	add_theme_support( 'automatic-feed-links' );

	// Content width
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 1280;
	}
}
add_action( 'after_setup_theme', 'realestate_theme_support' );

/**
 * Register navigation menus.
 */
function realestate_register_menus() {
	register_nav_menus( array(
		'primary-menu' => esc_html__( 'Primary Menu', 'realestate-child' ),
		'footer-menu'  => esc_html__( 'Footer Menu', 'realestate-child' ),
	) );
}
add_action( 'after_setup_theme', 'realestate_register_menus' );

/**
 * Register widget areas (footer columns).
 */
function realestate_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 1', 'realestate-child' ),
		'id'            => 'footer-column-1',
		'description'   => esc_html__( 'Footer column 1 — Company info area.', 'realestate-child' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 2', 'realestate-child' ),
		'id'            => 'footer-column-2',
		'description'   => esc_html__( 'Footer column 2 — Quick links.', 'realestate-child' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 3', 'realestate-child' ),
		'id'            => 'footer-column-3',
		'description'   => esc_html__( 'Footer column 3 — Services.', 'realestate-child' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 4', 'realestate-child' ),
		'id'            => 'footer-column-4',
		'description'   => esc_html__( 'Footer column 4 — Contact / Newsletter.', 'realestate-child' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'realestate_widgets_init' );

/**
 * Add body classes for the theme.
 */
function realestate_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 're-front-page';
	}
	return $classes;
}
add_filter( 'body_class', 'realestate_body_classes' );

/**
 * Custom excerpt length.
 */
function realestate_excerpt_length( $length ) {
	return 25;
}
add_filter( 'excerpt_length', 'realestate_excerpt_length' );

/**
 * Custom excerpt more text.
 */
function realestate_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'realestate_excerpt_more' );
