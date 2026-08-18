<?php
/**
 * Template — archive.php
 *
 * Archive template for categories, tags, dates, and custom post types.
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

		<!-- Archive Header -->
		<div class="re-section-header re-pt-header">
			<?php the_archive_title( '<h1 class="re-section-title">', '</h1>' ); ?>

			<?php
			the_archive_description( '<p class="re-section-desc">', '</p>' );
			?>
		</div>

		<?php if ( have_posts() ) : ?>

			<div class="re-posts-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="re-post-card" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="re-post-card-image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?>
								</a>
							</div>
						<?php endif; ?>

						<div class="re-post-card-body">
							<div class="re-post-meta">
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<?php echo esc_html( get_the_date() ); ?>
								</time>
								&middot;
								<span><?php echo esc_html( get_the_author() ); ?></span>
							</div>

							<h2 class="re-post-card-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<p class="re-post-card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>

							<a href="<?php the_permalink(); ?>" class="re-btn re-btn-outline-gold re-btn-sm">
								<?php esc_html_e( 'Read More', 'realestate-child' ); ?>
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
							</a>
						</div>

					</article>
					<?php
				endwhile;
				?>
			</div>

			<!-- Pagination -->
			<div class="re-pagination re-mt-3 re-text-center">
				<?php
				the_posts_pagination( array(
					'mid_size'  => 2,
					'prev_text' => '&laquo; ' . esc_html__( 'Previous', 'realestate-child' ),
					'next_text' => esc_html__( 'Next', 'realestate-child' ) . ' &raquo;',
				) );
				?>
			</div>

		<?php else : ?>

			<div class="re-text-center re-mt-3" style="min-height: 40vh; display: flex; align-items: center; justify-content: center;">
				<div>
					<h2><?php esc_html_e( 'Nothing Found', 'realestate-child' ); ?></h2>
					<p><?php esc_html_e( 'No content matched your criteria. Try broadening your search.', 'realestate-child' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="re-btn re-btn-primary re-mt-1">
						<?php esc_html_e( 'Back to Home', 'realestate-child' ); ?>
					</a>
				</div>
			</div>

		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
