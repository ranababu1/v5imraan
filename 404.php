<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://developer.wordpress.org/themes/template-hierarchy/
 * @package v5imraan
 */

get_header();
?>

<main id="primary" class="site-main site-main--404">

	<section class="blog-hero blog-hero--404">
		<div class="container">
			<p class="blog-hero__kicker"><?php esc_html_e( 'Error 404', 'v5imraan' ); ?></p>
			<h1 class="blog-hero__title"><?php esc_html_e( 'This page can’t be found.', 'v5imraan' ); ?></h1>
			<p class="blog-hero__tagline"><?php esc_html_e( 'The link may be broken, or the page may have moved. Try a search, or browse the latest articles below.', 'v5imraan' ); ?></p>
			<div class="blog-hero__search">
				<?php get_search_form(); ?>
			</div>
		</div>
	</section>

	<div class="blog-listing">
		<div class="container">
			<div class="cards-heading">
				<h2><?php esc_html_e( 'Latest articles', 'v5imraan' ); ?></h2>
			</div>
			<?php
			$latest = new WP_Query(
				array(
					'posts_per_page'      => 3,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			if ( $latest->have_posts() ) :
				?>
				<ul class="post-grid">
					<?php
					while ( $latest->have_posts() ) :
						$latest->the_post();
						?>
						<li class="post-grid__item"><?php get_template_part( 'template-parts/post-card', null, array( 'heading' => 'h3' ) ); ?></li>
					<?php endwhile; ?>
				</ul>
				<?php
				wp_reset_postdata();
			else :
				?>
				<div class="blog-empty">
					<p><?php esc_html_e( 'Nothing found — head back to the homepage.', 'v5imraan' ); ?></p>
					<a class="blog-empty__cta" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to homepage', 'v5imraan' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>

</main>

<?php
get_footer();