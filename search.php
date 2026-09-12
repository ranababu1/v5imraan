<?php
/**
 * The template for displaying search results.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 * @package v5imraan
 */

get_header();

$blog_page_id = (int) get_option( 'page_for_posts' );
$browse_url   = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/' );
?>

<main id="primary" class="site-main">

	<section class="blog-hero blog-hero--search">
		<div class="container">
			<?php v5imraan_the_breadcrumbs(); ?>
			<p class="blog-hero__kicker"><?php esc_html_e( 'Search', 'v5imraan' ); ?></p>
			<h1 class="blog-hero__title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Results for “%s”', 'v5imraan' ), esc_html( get_search_query() ) );
				?>
			</h1>
			<div class="blog-hero__search">
				<?php get_search_form(); ?>
			</div>
		</div>
	</section>

	<div class="blog-listing">
		<div class="container">

			<?php if ( have_posts() ) : ?>

				<p class="blog-listing__count">
					<?php
					/* translators: %s: number of results. */
					printf( esc_html( _n( '%s result found', '%s results found', (int) $GLOBALS['wp_query']->found_posts, 'v5imraan' ) ), esc_html( number_format_i18n( (int) $GLOBALS['wp_query']->found_posts ) ) );
					?>
				</p>

				<ul class="post-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<li class="post-grid__item">
							<?php get_template_part( 'template-parts/post-card', null, array( 'heading' => 'h3' ) ); ?>
						</li>
					<?php endwhile; ?>
				</ul>

				<?php get_template_part( 'template-parts/pagination' ); ?>

			<?php else : ?>

				<div class="blog-empty">
					<h2><?php esc_html_e( 'No results found', 'v5imraan' ); ?></h2>
					<p><?php esc_html_e( 'Try a different keyword, or browse all articles instead.', 'v5imraan' ); ?></p>
					<a class="blog-empty__cta" href="<?php echo esc_url( $browse_url ); ?>"><?php esc_html_e( 'Browse all posts', 'v5imraan' ); ?></a>
				</div>

			<?php endif; ?>

		</div>
	</div>

</main>

<?php
get_footer();