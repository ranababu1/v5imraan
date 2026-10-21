<?php
/**
 * Front-end performance: Jetpack sharing off, deferred scripts, and
 * AdSense / Clarity loaded only after the reader interacts or the page
 * has finished loading.
 *
 * Every piece can be switched off with a filter:
 *   v5imraan_disable_jetpack_sharing, v5imraan_defer_scripts,
 *   v5imraan_delay_third_party.
 *
 * @package v5imraan
 */

/*
 * 1. Jetpack sharing buttons. The theme prints its own plain share links
 * (template-parts/share-buttons.php), so Jetpack's block, its ?share=
 * links and the Facebook, X, Pinterest and Tumblr SDKs it pulls in are
 * dropped.
 */
add_filter(
	'sharing_show',
	function ( $show ) {
		return apply_filters( 'v5imraan_disable_jetpack_sharing', true ) ? false : $show;
	},
	99
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! apply_filters( 'v5imraan_disable_jetpack_sharing', true ) ) {
			return;
		}
		foreach ( array( 'sharing-js', 'tumblr-js' ) as $handle ) {
			wp_dequeue_script( $handle );
		}
		foreach ( array( 'sharedaddy', 'social-logos' ) as $handle ) {
			wp_dequeue_style( $handle );
		}
	},
	100
);

// Jetpack enqueues the sharing stylesheet late, so drop it again at print time.
add_action(
	'wp_print_styles',
	function () {
		if ( apply_filters( 'v5imraan_disable_jetpack_sharing', true ) ) {
			wp_dequeue_style( 'sharedaddy' );
			wp_dequeue_style( 'social-logos' );
		}
	},
	100
);

/*
 * 2. Defer scripts that nothing needs before the page is parsed.
 * Deferred scripts still run in order and before DOMContentLoaded, so
 * the Algolia autocomplete set-up (which waits for window load) and
 * main.js keep working. No inline script on the site calls jQuery.
 */
if ( ! function_exists( 'v5imraan_defer_handles' ) ) :
	/**
	 * Script handles that get the defer attribute.
	 *
	 * @return array
	 */
	function v5imraan_defer_handles() {
		return apply_filters(
			'v5imraan_defer_handles',
			array(
				'jquery-core',
				'jquery-migrate',
				'underscore',
				'wp-util',
				'algolia-search',
				'algolia-autocomplete',
				'algolia-autocomplete-noconflict',
				'v5imraan-navigation',
				'main-js',
				'googlesitekit-consent-mode',
				'wp-consent-api',
			)
		);
	}
endif;

add_filter(
	'script_loader_tag',
	function ( $tag, $handle ) {
		if ( is_admin() || ! apply_filters( 'v5imraan_defer_scripts', true ) ) {
			return $tag;
		}
		if ( ! in_array( $handle, v5imraan_defer_handles(), true ) ) {
			return $tag;
		}
		if ( false !== strpos( $tag, ' defer' ) || false !== strpos( $tag, ' async' ) ) {
			return $tag;
		}
		return preg_replace( '#<script(?=[^>]*\ssrc=)#', '<script defer', $tag, 1 );
	},
	20,
	2
);

/*
 * 3. AdSense and Microsoft Clarity after first interaction, or 3 seconds
 * after window load, whichever comes first.
 */
add_action(
	'wp_head',
	function () {
		if ( ! apply_filters( 'v5imraan_delay_third_party', true ) ) {
			return;
		}
		// Take over the Clarity plugin's inline tag so it can be delayed.
		// If the plugin or its option is not found, its own tag is left alone.
		$id = function_exists( 'clarity_add_script_to_header' ) ? (string) get_option( 'clarity_project_id', '' ) : '';
		if ( '' !== $id && remove_action( 'wp_head', 'clarity_add_script_to_header' ) ) {
			$GLOBALS['v5imraan_clarity_id'] = $id;
		}
	},
	0
);

add_action(
	'wp_footer',
	function () {
		if ( ! apply_filters( 'v5imraan_delay_third_party', true ) ) {
			return;
		}
		$ads     = ! is_front_page();
		$clarity = isset( $GLOBALS['v5imraan_clarity_id'] ) ? preg_replace( '/[^a-z0-9]/i', '', $GLOBALS['v5imraan_clarity_id'] ) : '';
		if ( ! $ads && '' === $clarity ) {
			return;
		}
		?>
<script id="v5-delayed-third-party">
(function () {
	var done = false;
	var evts = [ 'pointerdown', 'keydown', 'touchstart', 'scroll', 'mousemove' ];
	function add( src, attrs ) {
		var s = document.createElement( 'script' );
		s.async = true;
		s.src = src;
		for ( var k in attrs ) { s.setAttribute( k, attrs[ k ] ); }
		document.head.appendChild( s );
	}
	function go() {
		if ( done ) { return; }
		done = true;
		evts.forEach( function ( e ) { window.removeEventListener( e, go, { passive: true } ); } );
		<?php if ( $ads ) : ?>
		add( 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1106747567696734', { 'data-ad-client': 'ca-pub-1106747567696734', 'crossorigin': 'anonymous' } );
		<?php endif; ?>
		<?php if ( '' !== $clarity ) : ?>
		window.clarity = window.clarity || function () { ( window.clarity.q = window.clarity.q || [] ).push( arguments ); };
		add( 'https://www.clarity.ms/tag/<?php echo esc_js( $clarity ); ?>?ref=wordpress', {} );
		<?php endif; ?>
	}
	evts.forEach( function ( e ) { window.addEventListener( e, go, { passive: true } ); } );
	window.addEventListener( 'load', function () { setTimeout( go, 3000 ); } );
})();
</script>
		<?php
	},
	99
);
