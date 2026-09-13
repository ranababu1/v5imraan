<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 * @package v5imraan
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<div class="sp-progress" aria-hidden="true"><span class="sp-progress-bar" id="sp-progress-bar"></span></div>

	<article <?php post_class( 'sp-article' ); ?>>

		<header class="blog-hero sp-hero">
			<div class="container">
				<?php v5imraan_the_breadcrumbs(); ?>
				<div class="sp-hero__inner">
					<div class="sp-hero__content">
						<div class="sp-hero__meta">
							<?php
							$categories = get_the_category();
							if ( $categories ) :
								?>
								<p class="sp-hero__cats">
									<?php foreach ( $categories as $category ) : ?>
										<a class="sp-hero__cat" href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
									<?php endforeach; ?>
								</p>
							<?php endif; ?>
							<p class="sp-hero__byline">
								<?php echo get_avatar( get_the_author_meta( 'ID' ), 40, get_stylesheet_directory_uri() . '/img/imrn.png', get_the_author() ); ?>
								<span>
									<?php
									/* translators: %s: author name. */
									printf( esc_html__( 'By %s', 'v5imraan' ), esc_html( get_the_author() ) );
									?>
									&middot;
									<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
									&middot; <?php echo esc_html( v5imraan_reading_time() ); ?> <?php esc_html_e( 'min read', 'v5imraan' ); ?>
								</span>
							</p>
						</div>
						<h1 class="sp-hero__title"><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?>
							<p class="sp-hero__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="sp-hero__media">
							<figure class="sp-featured">
								<?php
								the_post_thumbnail(
									'large',
									array(
										'class'         => 'sp-featured__img',
										'fetchpriority' => 'high',
										'decoding'      => 'async',
									)
								);
								?>
							</figure>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</header>

		<div class="container">
			<div class="sp-layout">
				<div class="sp-main">
					<div class="sp-content">
						<?php
						the_content();

						wp_link_pages(
							array(
								'before' => '<nav class="sp-page-links" aria-label="' . esc_attr__( 'Post pages', 'v5imraan' ) . '">',
								'after'  => '</nav>',
							)
						);
						?>
					</div>
					<?php
					$tags = get_the_tags();
					if ( $tags ) :
						?>
						<p class="sp-tags">
							<?php foreach ( $tags as $tag ) : ?>
								<a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>"><?php echo esc_html( $tag->name ); ?></a>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>

					<div class="sp-share">
						<span class="sp-share__label"><?php esc_html_e( 'Share this article', 'v5imraan' ); ?></span>
						<?php get_template_part( 'template-parts/share-buttons' ); ?>
					</div>

					<aside class="sp-author">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 64, get_stylesheet_directory_uri() . '/img/imrn.png', get_the_author() ); ?>
						<div>
							<h2 class="sp-author__title"><?php the_author(); ?></h2>
							<p class="sp-author__bio">
								<?php
								$author_bio = get_the_author_meta( 'description' );

								echo esc_html(
									$author_bio
										? $author_bio
										: __( 'Engineering leader and AI architect with 13+ years of experience building scalable platforms and AI-powered systems. I write about Applied AI, Agentic AI, software architecture, cloud, DevOps, platform engineering, and the practical side of building reliable technology at scale.', 'v5imraan' )
								);
								?>
							</p>
							<a class="sp-author__link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
								<?php /* translators: %s: Author display name. */ printf( esc_html__( 'All articles by %s', 'v5imraan' ), esc_html( get_the_author() ) ); ?> <span aria-hidden="true">&rarr;</span>
							</a>
						</div>
					</aside>

					<nav class="sp-postnav" aria-label="<?php esc_attr_e( 'More articles', 'v5imraan' ); ?>">
						<?php
						previous_post_link(
							'<div class="sp-postnav__card">%link</div>',
							'<span class="sp-postnav__label">' . esc_html__( 'Newer article', 'v5imraan' ) . '</span><span class="sp-postnav__title">%title</span>'
						);
						next_post_link(
							'<div class="sp-postnav__card sp-postnav__card--next">%link</div>',
							'<span class="sp-postnav__label">' . esc_html__( 'Older article', 'v5imraan' ) . '</span><span class="sp-postnav__title">%title</span>'
						);
						?>
					</nav>

					<?php
					$related = v5imraan_get_related_posts( get_the_ID(), 3 );
					if ( $related->have_posts() ) :
						?>
						<section class="sp-related" aria-label="<?php esc_attr_e( 'Related articles', 'v5imraan' ); ?>">
							<h2><?php esc_html_e( 'Related articles', 'v5imraan' ); ?></h2>
							<ul class="post-grid">
								<?php
								while ( $related->have_posts() ) :
									$related->the_post();
									?>
									<li class="post-grid__item"><?php get_template_part( 'template-parts/post-card', null, array( 'heading' => 'h3' ) ); ?></li>
								<?php endwhile; ?>
							</ul>
							<?php wp_reset_postdata(); ?>
						</section>
					<?php endif; ?>

					<?php
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>
				</div><!-- .sp-main -->

				<aside class="sp-sidebar" aria-label="<?php esc_attr__( 'Article tools', 'v5imraan' ); ?>">
					<div class="sp-toc-wrap">
						<h2><?php esc_html_e( 'On this page', 'v5imraan' ); ?></h2>
						<ul class="sp-toc" id="sp-toc"></ul>
					</div>
					<div class="sp-share-wrap">
						<h2><?php esc_html_e( 'Share', 'v5imraan' ); ?></h2>
						<?php get_template_part( 'template-parts/share-buttons', null, array( 'class' => 'sp-share__buttons sp-share__buttons--sidebar' ) ); ?>
					</div>
				</aside>
			</div><!-- .sp-layout -->
		</div><!-- .container -->

	</article>

	<?php
endwhile; // End of the loop.
?>

<?php
get_footer();