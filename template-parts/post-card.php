<?php
/**
 * Reusable blog post card.
 *
 * Used on the blog listing, archives, search results, related posts,
 * the homepage blog strip and the 404 page.
 *
 * @package v5imraan
 *
 * @param array $args {
 *     Optional display arguments.
 *
 *     @type bool   $featured Featured hero card (larger, eager image).
 *     @type string $heading  Heading tag for the title. Default 'h2'.
 * }
 */

$featured = ! empty( $args['featured'] );
$heading  = empty( $args['heading'] ) ? 'h2' : $args['heading'];
if ( ! in_array( $heading, array( 'h2', 'h3', 'h4' ), true ) ) {
	$heading = 'h2';
}
$size       = $featured ? 'v5-blog-wide' : 'v5-blog-card';
$classes    = 'post-card';
if ( $featured ) {
	$classes .= ' post-card--featured';
}
$categories = get_the_category();
$category   = ! empty( $categories ) ? $categories[0] : '';
$reading    = v5imraan_reading_time();
?>
<article <?php post_class( $classes ); ?>>
	<a class="post-card__media<?php echo has_post_thumbnail() ? '' : ' post-card__media--ph'; ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( $size, array( 'class' => 'post-card__img', 'loading' => $featured ? 'eager' : 'lazy' ) ); ?>
		<?php else : ?>
			<span class="post-card__ph-mark" aria-hidden="true"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
		<?php endif; ?>
		<?php if ( $category ) : ?>
			<span class="post-card__cat"><?php echo esc_html( $category->name ); ?></span>
		<?php endif; ?>
	</a>
	<div class="post-card__body">
		<p class="post-card__meta">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<span aria-hidden="true">&bull;</span>
			<span><?php echo esc_html( $reading ); ?>&nbsp;<?php esc_html_e( 'min read', 'v5imraan' ); ?></span>
		</p>
		<<?php echo esc_html( $heading ); ?> class="post-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</<?php echo esc_html( $heading ); ?>>
		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $featured ? 26 : 18, '…' ) ); ?></p>
	</div>
</article>