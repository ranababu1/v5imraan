<?php
/**
 * Writing Timeline (/timeline/). Auto-applies by page slug.
 *
 * Renders one chunk (?tlp=N, default 1) or one whole year (?y=YYYY);
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
$v5_tl_page  = isset( $_GET['tlp'] ) ? max( 1, absint( $_GET['tlp'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ?page= is canonical-redirected by core.
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

		<div id="timeline" class="tl-years" data-total="<?php echo (int) $v5_tl['total']; ?>" data-endpoint="<?php echo esc_url( add_query_arg( 'v', rawurlencode( (string) get_option( 'v5_timeline_ver', '1' ) ), rest_url( 'v5imraan/v1/timeline' ) ) ); ?>" data-next="<?php echo (int) $v5_tl_slice['next']; ?>" data-mode="<?php echo $v5_tl_year ? 'year' : 'all'; ?>">
			<?php echo v5imraan_timeline_render_days( $v5_tl_slice['days'], $v5_tl['year_totals'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped at render time. ?>
		</div>

		<div class="tl-more">
			<?php if ( $v5_tl_slice['next'] ) : ?>
				<a class="tl-more__btn tl-more__link" href="<?php echo esc_url( add_query_arg( 'tlp', (int) $v5_tl_slice['next'], $v5_tl_url ) ); ?>"><?php esc_html_e( 'Load more', 'v5imraan' ); ?></a>
				<button type="button" class="tl-more__btn tl-more__button" hidden><?php esc_html_e( 'Load more', 'v5imraan' ); ?></button>
			<?php elseif ( $v5_tl_year ) : ?>
				<a class="tl-more__btn" href="<?php echo esc_url( $v5_tl_url ); ?>"><?php esc_html_e( 'Show all years', 'v5imraan' ); ?></a>
			<?php endif; ?>
			<p class="tl-more__status" role="status" aria-live="polite"></p>
		</div>
	</div>
</main>

<?php
/*
 * Structured data in the house pattern used by articles: one @graph with
 * the shared Person (author/imran/#person) and WebSite (#website) nodes,
 * the page node, its BreadcrumbList and an ItemList of the first chunk.
 */
$v5_tl_person  = home_url( '/author/imran/#person' );
$v5_tl_website = home_url( '/#website' );
$v5_tl_lang    = get_bloginfo( 'language' );
$v5_tl_first   = v5imraan_timeline_slice( $v5_tl, 1, 0 );
$v5_tl_items   = array();
foreach ( $v5_tl_first['days'] as $v5_tl_d ) {
	foreach ( $v5_tl_d['items'] as $v5_tl_i ) {
		$v5_tl_items[] = array(
			'@type'    => 'ListItem',
			'position' => count( $v5_tl_items ) + 1,
			'url'      => $v5_tl_i['url'],
			'name'     => wp_strip_all_tags( html_entity_decode( $v5_tl_i['title'], ENT_QUOTES, 'UTF-8' ) ),
		);
	}
}
$v5_tl_lastpost = get_lastpostdate( 'blog' );
$v5_tl_graph    = array(
	'@context' => 'https://schema.org',
	'@graph'   => array(
		array(
			'@type'         => 'Person',
			'@id'           => $v5_tl_person,
			'name'          => 'Imranul Haque Mazumder',
			'alternateName' => array( 'Imran', 'Imraan', 'Imran M' ),
			'url'           => home_url( '/author/imran/' ),
			'description'   => 'Imranul Haque Mazumder is an Associate Director of Technology at CleverTap and a PMP-certified engineering leader with 13+ years of experience in software engineering, AI, system architecture, cloud platforms, and engineering leadership.',
			'jobTitle'      => 'Associate Director, Tech',
			'image'         => array(
				'@type' => 'ImageObject',
				'url'   => home_url( '/wp-content/uploads/2026/10/imraans_portrait_photo.webp' ),
			),
			'sameAs'        => array(
				'https://www.linkedin.com/in/imazumder/',
				'https://github.com/ranababu1',
				'https://medium.com/@imrn.dev/about',
				'https://about.me/imraan',
				'https://topmate.io/mazumder',
			),
			'worksFor'      => array(
				'@type' => 'Organization',
				'name'  => 'CleverTap',
				'url'   => 'https://clevertap.com/',
			),
		),
		array(
			'@type'       => 'WebSite',
			'@id'         => $v5_tl_website,
			'url'         => home_url( '/' ),
			'name'        => 'Imraan',
			'description' => 'Personal website and technical blog of Imranul Haque Mazumder, covering software engineering, AI, system design, cloud, architecture, and technology.',
			'publisher'   => array( '@id' => $v5_tl_person ),
			'inLanguage'  => $v5_tl_lang,
		),
		array_filter(
			array(
				'@type'        => 'CollectionPage',
				'@id'          => $v5_tl_url . '#webpage',
				'url'          => $v5_tl_url,
				'name'         => 'Writing Timeline',
				'description'  => 'Every article on imraan.in in date order, newest first, grouped by year.',
				'isPartOf'     => array( '@id' => $v5_tl_website ),
				'about'        => array( '@id' => $v5_tl_person ),
				'author'       => array( '@id' => $v5_tl_person ),
				'publisher'    => array( '@id' => $v5_tl_person ),
				'mainEntity'   => array( '@id' => $v5_tl_url . '#itemlist' ),
				'breadcrumb'   => array( '@id' => $v5_tl_url . '#breadcrumb' ),
				'inLanguage'   => $v5_tl_lang,
				'dateModified' => $v5_tl_lastpost ? mysql2date( DATE_W3C, $v5_tl_lastpost, false ) : null,
			)
		),
		array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $v5_tl_url . '#breadcrumb',
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => 'Home',
					'item'     => home_url( '/' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => 'Writing Timeline',
					'item'     => $v5_tl_url,
				),
			),
		),
		array(
			'@type'           => 'ItemList',
			'@id'             => $v5_tl_url . '#itemlist',
			'name'            => 'Latest articles',
			'numberOfItems'   => count( $v5_tl_items ),
			'itemListOrder'   => 'https://schema.org/ItemListOrderDescending',
			'itemListElement' => $v5_tl_items,
		),
	),
);
?>
<script type="application/ld+json"><?php echo wp_json_encode( $v5_tl_graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

<?php
get_footer();
