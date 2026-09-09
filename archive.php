<?php
/**
 * The template for displaying archive pages (category, tag, date, author).
 *
 * Uses the main query so tag, date and author archives work correctly
 * (the old custom WP_Query ignored everything except categories).
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package v5imraan
 */

get_header();

$archive = v5imraan_archive_heading();
?>

<main id="primary" class="site-main">

	<section class="blog-hero blog-hero--archive">
		<div class="container">
			<?php v5imraan_the_breadcrumbs(); ?>
			<p class="blog-hero__kicker"><?php echo esc_html( $archive[0] ); ?></p>
			<h1 class="blog-hero__title"><?php echo esc_html( $archive[1] ); ?></h1>
			<?php
			$archive_desc = get_the_archive_description();
			if ( $archive_desc ) :
				?>
				<div class="blog-hero__tagline"><?php echo wp_kses_post( $archive_desc ); ?></div>
			<?php endif; ?>
			<?php if ( is_author() ) : ?>
				<div class="blog-hero__author">
					<?php echo get_avatar( get_queried_object_id(), 56, '', get_the_archive_title() ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<div class="blog-listing">
		<div class="container">

			<?php if ( have_posts() ) : ?>

				<p class="blog-listing__count">
					<?php
					/* translators: %s: number of articles. */
					printf( esc_html( _n( '%s article', '%s articles', (int) $GLOBALS['wp_query']->found_posts, 'v5imraan' ) ), esc_html( number_format_i18n( (int) $GLOBALS['wp_query']->found_posts ) ) );
					?>
				</p>

				<ul class="post-grid">
					<?php
					$first = ! is_paged();
					while ( have_posts() ) :
						the_post();
						?>
						<li class="post-grid__item<?php echo $first ? ' post-grid__item--featured' : ''; ?>">
							<?php get_template_part( 'template-parts/post-card', null, array( 'featured' => $first, 'heading' => $first ? 'h2' : 'h3' ) ); ?>
						</li>
						<?php
						$first = false;
					endwhile;
					?>
				</ul>

				<?php get_template_part( 'template-parts/pagination' ); ?>

			<?php else : ?>

				<div class="blog-empty">
					<h2><?php esc_html_e( 'Nothing here yet', 'v5imraan' ); ?></h2>
					<p><?php esc_html_e( 'No articles have been published in this archive yet.', 'v5imraan' ); ?></p>
					<a class="blog-empty__cta" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to homepage', 'v5imraan' ); ?></a>
				</div>

			<?php endif; ?>

		</div>
	</div>

</main>

<?php
get_footer();