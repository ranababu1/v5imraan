<?php
/**
 * The main template file — blog listing.
 *
 * Uses the main query (instead of a custom WP_Query) so pagination,
 * canonical URLs and SEO plugins all behave correctly.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package v5imraan
 */

get_header();
?>

<main id="primary" class="site-main">

	<section class="blog-hero">
		<div class="container">
			<?php v5imraan_the_breadcrumbs(); ?>
			<p class="blog-hero__kicker"><?php esc_html_e( 'Blog', 'v5imraan' ); ?></p>
			<h1 class="blog-hero__title"><?php esc_html_e( 'Tech Insights &amp; Engineering Notes', 'v5imraan' ); ?></h1>
			<p class="blog-hero__tagline"><?php esc_html_e( 'AI systems, platform engineering and architecture — lessons from 13+ years of building at scale.', 'v5imraan' ); ?></p>
			<div class="blog-hero__search">
				<?php get_search_form(); ?>
			</div>
		</div>
	</section>

	<div class="blog-listing">
		<div class="container">

			<nav class="blog-chips" aria-label="<?php esc_attr_e( 'Filter by category', 'v5imraan' ); ?>">
				<?php
				$blog_page_id = (int) get_option( 'page_for_posts' );
				$chips_base   = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/' );
				$chip_current = is_category() ? (int) get_queried_object_id() : 0;
				?>
				<a class="blog-chip<?php echo is_home() ? ' is-active' : ''; ?>" href="<?php echo esc_url( $chips_base ); ?>"><?php esc_html_e( 'All Posts', 'v5imraan' ); ?></a>
				<?php
				$chips = get_categories(
					array(
						'parent'     => 0,
						'orderby'    => 'count',
						'order'      => 'DESC',
						'number'     => 8,
						'hide_empty' => true,
					)
				);
				foreach ( $chips as $chip ) :
					?>
					<a class="blog-chip<?php echo $chip->term_id === $chip_current ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $chip ) ); ?>"><?php echo esc_html( $chip->name ); ?></a>
				<?php endforeach; ?>
			</nav>

			<?php if ( have_posts() ) : ?>

				<ul class="post-grid">
					<?php
					$first = ! is_paged();
					while ( have_posts() ) :
						the_post();
						?>
						<li class="post-grid__item<?php echo $first ? ' post-grid__item--featured' : ''; ?>">
							<?php
							get_template_part(
								'template-parts/post-card',
								null,
								array(
									'featured' => $first,
									'heading'  => $first ? 'h2' : 'h3',
								)
							);
							?>
						</li>
						<?php
						$first = false;
					endwhile;
					?>
				</ul>

				<?php
				get_template_part( 'template-parts/pagination' );

			else :
				?>
				<div class="blog-empty">
					<h2><?php esc_html_e( 'No posts yet', 'v5imraan' ); ?></h2>
					<p><?php esc_html_e( 'New articles are on the way. Check back soon.', 'v5imraan' ); ?></p>
				</div>
			<?php endif; ?>

		</div>
	</div>

</main>

<?php
get_footer();