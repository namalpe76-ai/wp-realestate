<?php
/**
 * Template — single.php
 *
 * Single blog post template.
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

			<article class="re-single-post" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<!-- Post Meta -->
				<div class="re-post-meta re-mb-2">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
						<?php echo esc_html( get_the_date() ); ?>
					</time>
					&middot;
					<span><?php echo esc_html( get_the_author() ); ?></span>
					&middot;
					<span>
						<?php
						$categories = get_the_category();
						if ( ! empty( $categories ) ) {
							$cat_names = wp_list_pluck( $categories, 'name' );
							echo esc_html( implode( ', ', $cat_names ) );
						}
						?>
					</span>
				</div>

				<!-- Title -->
				<h1><?php the_title(); ?></h1>

				<!-- Featured Image -->
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="re-post-featured-image">
						<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
					</div>
				<?php endif; ?>

				<!-- Post Content -->
				<div class="re-post-content">
					<?php
					the_content();

					wp_link_pages( array(
						'before' => '<div class="page-links re-mt-2">' . esc_html__( 'Pages:', 'realestate-child' ),
						'after'  => '</div>',
					) );
					?>
				</div>

				<!-- Tags -->
				<?php
				$tags = get_the_tags();
				if ( ! empty( $tags ) ) :
					?>
					<div class="re-post-tags re-mt-2">
						<?php foreach ( $tags as $tag ) : ?>
							<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="re-badge re-badge-id" style="margin-right: 6px; margin-bottom: 6px;">
								<?php echo esc_html( $tag->name ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<!-- Post Navigation -->
				<div class="re-post-navigation re-mt-3" style="display: flex; justify-content: space-between; border-top: 1px solid var(--re-border); padding-top: var(--re-space-xl);">
					<div>
						<?php
						$prev = get_previous_post();
						if ( $prev ) :
							?>
							<a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" style="display: block;">
								<span style="font-size: var(--re-fs-xs); text-transform: uppercase; letter-spacing: 1px; color: var(--re-text-light);"><?php esc_html_e( 'Previous Post', 'realestate-child' ); ?></span><br>
								<strong style="color: var(--re-primary);"><?php echo esc_html( get_the_title( $prev ) ); ?></strong>
							</a>
						<?php endif; ?>
					</div>
					<div style="text-align: right;">
						<?php
						$next = get_next_post();
						if ( $next ) :
							?>
							<a href="<?php echo esc_url( get_permalink( $next ) ); ?>" style="display: block;">
								<span style="font-size: var(--re-fs-xs); text-transform: uppercase; letter-spacing: 1px; color: var(--re-text-light);"><?php esc_html_e( 'Next Post', 'realestate-child' ); ?></span><br>
								<strong style="color: var(--re-primary);"><?php echo esc_html( get_the_title( $next ) ); ?></strong>
							</a>
						<?php endif; ?>
					</div>
				</div>

				<!-- Comments -->
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
