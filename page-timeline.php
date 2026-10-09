<?php
/**
 * Writing Timeline (/timeline/). Auto-applies by page slug.
 *
 * @package v5imraan
 */

get_header();
$v5_tl_html = v5imraan_timeline_html();
?>

<main id="primary" class="site-main tl-page">
	<div class="tl-wrap">
		<header class="tl-hero">
			<p class="tl-hero__eyebrow"><?php esc_html_e( 'Writing timeline', 'v5imraan' ); ?></p>
			<h1 class="tl-hero__title"><?php esc_html_e( 'Everything I&rsquo;ve written, year by year', 'v5imraan' ); ?></h1>
			<p class="tl-hero__intro"><?php esc_html_e( 'Every article in date order, newest first. Pick a year to jump to it, or scroll through the whole archive.', 'v5imraan' ); ?></p>
		</header>

		<?php echo $v5_tl_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped at render time. ?>
	</div>
</main>

<script type="application/ld+json">
<?php
echo wp_json_encode(
	array(
		'@context'    => 'https://schema.org',
		'@type'       => 'CollectionPage',
		'@id'         => get_permalink() . '#webpage',
		'url'         => get_permalink(),
		'name'        => 'Writing Timeline',
		'description' => 'Every article on imraan.in in date order, grouped by year.',
		'isPartOf'    => array( '@id' => home_url( '/#website' ) ),
	),
	JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);
?>
</script>
<script>
(function () {
	var chips = document.querySelectorAll('.tl-chip');
	var years = document.querySelectorAll('.tl-year');
	if (!chips.length || !years.length) { return; }
	function show(y) {
		var found = false;
		years.forEach(function (s) {
			var on = y === 'all' || s.getAttribute('data-year') === y;
			s.hidden = !on;
			if (on) { found = true; }
		});
		if (!found) { return show('all'); }
		chips.forEach(function (c) {
			var on = c.getAttribute('data-year') === y;
			c.classList.toggle('is-active', on);
			if (on) { c.setAttribute('aria-current', 'true'); } else { c.removeAttribute('aria-current'); }
		});
	}
	chips.forEach(function (c) {
		c.addEventListener('click', function (e) {
			e.preventDefault();
			var y = c.getAttribute('data-year');
			show(y);
			history.replaceState(null, '', y === 'all' ? location.pathname : '#y' + y);
			var top = document.getElementById('timeline').getBoundingClientRect().top + window.scrollY - 140;
			if (window.scrollY > top) { window.scrollTo({ top: top }); }
		});
	});
	var m = /^#y(\d{4})$/.exec(location.hash);
	if (m) { show(m[1]); }
})();
</script>

<?php
get_footer();
