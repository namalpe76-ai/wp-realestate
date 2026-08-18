<?php
/**
 * Template — page.php
 *
 * Standard page template. Compatible with Elementor page builder.
 *
 * @package RealEstate_Child
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="re-page-content" id="main-content">
	<div class="re-container">

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php
				/**
				 * If Elementor is active, it takes over the_content() rendering.
				 * No custom wrapper needed — just output the content.
				 */
				?>

				<?php if ( ! \Elementor\Plugin::instance()->preview->is_preview_mode() ) : ?>
					<div class="re-page-header re-pt-header re-mb-2">
						<h1><?php the_title(); ?></h1>
					</div>
				<?php endif; ?>

				<div class="re-page-post-content">
					<?php
					the_content();

					wp_link_pages( array(
						'before' => '<div class="page-links re-mt-2">' . esc_html__( 'Pages:', 'realestate-child' ),
						'after'  => '</div>',
					) );
					?>
				</div>

				<?php
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
				?>

			</article>

		<?php
		endwhile;
		?>

	</div>
</main>

<?php
get_footer();
