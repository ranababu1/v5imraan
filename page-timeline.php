<?php
/**
 * Writing Timeline (/timeline/). Auto-applies by page slug.
 *
 * Renders one chunk (?page=N, default 1) or one whole year (?y=YYYY);
 * js/timeline.js loads further chunks / years from the REST route.
 *
 * @package v5imraan
 */

get_header();

$v5_tl      = v5imraan_timeline_data();
$v5_tl_url  = get_permalink();
$v5_tl_year = isset( $_GET['y'] ) ? absint( $_GET['y'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( $v5_tl_year && ! isset( $v5_tl['year_totals'][ $v5_tl_year ] ) ) {
	$v5_tl_year = 0;
}
$v5_tl_page  = max( 1, (int) get_query_var( 'page' ) );
$v5_tl_slice = v5imraan_timeline_slice( $v5_tl, $v5_tl_page, $v5_tl_year );
?>

<main id="primary" class="site-main tl-page">
	<div class="tl-wrap">
		<header class="tl-hero">
			<p class="tl-hero__eyebrow"><?php esc_html_e( 'Writing timeline', 'v5imraan' ); ?></p>
			<h1 class="tl-hero__title"><?php esc_html_e( 'Everything I&rsquo;ve written, year by year', 'v5imraan' ); ?></h1>
			<p class="tl-hero__intro"><?php esc_html_e( 'Every article in date order, newest first. Pick a year to jump to it, or scroll through the whole archive.', 'v5imraan' ); ?></p>
		</header>

		<nav class="tl-filter" aria-label="<?php esc_attr_e( 'Filter by year', 'v5imraan' ); ?>">
			<ul class="tl-filter__list">
				<li><a class="tl-chip<?php echo $v5_tl_year ? '' : ' is-active'; ?>" href="<?php echo esc_url( $v5_tl_url ); ?>" data-year="all"<?php echo $v5_tl_year ? '' : ' aria-current="true"'; ?>><?php esc_html_e( 'All', 'v5imraan' ); ?></a></li>
				<?php foreach ( array_keys( $v5_tl['year_totals'] ) as $y ) : ?>
					<li><a class="tl-chip<?php echo $v5_tl_year === (int) $y ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'y', (int) $y, $v5_tl_url ) . '#y' . (int) $y ); ?>" data-year="<?php echo (int) $y; ?>"<?php echo $v5_tl_year === (int) $y ? ' aria-current="true"' : ''; ?>><?php echo (int) $y; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div id="timeline" class="tl-years" data-total="<?php echo (int) $v5_tl['total']; ?>" data-endpoint="<?php echo esc_url( rest_url( 'v5imraan/v1/timeline' ) ); ?>" data-next="<?php echo (int) $v5_tl_slice['next']; ?>" data-mode="<?php echo $v5_tl_year ? 'year' : 'all'; ?>">
			<?php echo v5imraan_timeline_render_days( $v5_tl_slice['days'], $v5_tl['year_totals'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped at render time. ?>
		</div>

		<div class="tl-more">
			<?php if ( $v5_tl_slice['next'] ) : ?>
				<a class="tl-more__btn tl-more__link" href="<?php echo esc_url( add_query_arg( 'page', (int) $v5_tl_slice['next'], $v5_tl_url ) ); ?>"><?php esc_html_e( 'Load more', 'v5imraan' ); ?></a>
				<button type="button" class="tl-more__btn tl-more__button" hidden><?php esc_html_e( 'Load more', 'v5imraan' ); ?></button>
			<?php elseif ( $v5_tl_year ) : ?>
				<a class="tl-more__btn" href="<?php echo esc_url( $v5_tl_url ); ?>"><?php esc_html_e( 'Show all years', 'v5imraan' ); ?></a>
			<?php endif; ?>
			<p class="tl-more__status" role="status" aria-live="polite"></p>
		</div>
	</div>
</main>

<script type="application/ld+json">
<?php
echo wp_json_encode(
	array(
		'@context'    => 'https://schema.org',
		'@type'       => 'CollectionPage',
		'@id'         => $v5_tl_url . '#webpage',
		'url'         => $v5_tl_url,
		'name'        => 'Writing Timeline',
		'description' => 'Every article on imraan.in in date order, grouped by year.',
		'isPartOf'    => array( '@id' => home_url( '/#website' ) ),
	),
	JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);
?>
</script>

<?php
get_footer();
