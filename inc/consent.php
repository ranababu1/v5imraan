<?php
/**
 * Consent-gated third-party loader: Ahrefs Web Analytics, Microsoft
 * Clarity and Google AdSense, driven by the WP Consent API.
 *
 *   Service   Consent category   When it loads
 *   Ahrefs    statistics         as soon as the DOM is ready
 *   Clarity   statistics         first interaction or 3 s after load
 *   AdSense   marketing          first interaction or 3 s after load
 *                                (never on the front page)
 *
 * The decision is made in the browser, not in PHP: pages are served
 * from the LiteSpeed page cache, so a server-side wp_has_consent()
 * check would cache one visitor's choice for everybody. The browser
 * uses the Consent API's own wp_has_consent(), which already applies
 * the consent type the banner sets for the visitor's region:
 *   - no consent type (no banner yet)  -> everything loads, as before;
 *   - opt-out regions                  -> loads unless the visitor said no;
 *   - opt-in regions (EEA, UK, CH...)  -> loads only after "allow".
 * When the banner sets waitfor_consent_hook, nothing loads until it
 * fires wp_consent_type_defined. A later "allow" loads the service on
 * the spot (wp_listen_for_consent_change). A later "deny" tells Clarity
 * to drop its cookies; scripts already on the page stay until the next
 * page view.
 *
 * If the WP Consent API plugin is inactive, services load as before.
 *
 * Filters: v5imraan_consent_services (edit or remove services),
 * v5imraan_delay_third_party (false = load Clarity and AdSense on DOM
 * ready instead of after interaction), v5imraan_clarity_project_id,
 * v5imraan_ahrefs_key, and v5imraan_consent_gate_third_party (false =
 * functions.php enqueues AdSense the old, ungated way; the loader then
 * should drop it via v5imraan_consent_services).
 *
 * @package v5imraan
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Register the theme with the WP Consent API so the API (and Site
 * Health) know this code follows the consent signals.
 */
add_filter( 'wp_consent_api_registered_' . get_stylesheet(), '__return_true' );
add_filter( 'wp_consent_api_registered_' . get_stylesheet() . '/functions.php', '__return_true' );

/**
 * Describe the cookies these services set, for banners that build a
 * cookie policy from the Consent API's cookie registry.
 */
function v5imraan_consent_cookie_info() {
	if ( ! function_exists( 'wp_add_cookie_info' ) ) {
		return;
	}
	wp_add_cookie_info( '_clck', 'Microsoft Clarity', 'statistics', __( '1 year', 'v5imraan' ), __( 'Keeps the same Clarity user ID across visits.', 'v5imraan' ) );
	wp_add_cookie_info( '_clsk', 'Microsoft Clarity', 'statistics', __( '1 day', 'v5imraan' ), __( 'Joins page views into one Clarity session recording.', 'v5imraan' ) );
	wp_add_cookie_info( '__gads', 'Google AdSense', 'marketing', __( '13 months', 'v5imraan' ), __( 'Ad delivery and frequency capping.', 'v5imraan' ) );
	wp_add_cookie_info( '__gpi', 'Google AdSense', 'marketing', __( '13 months', 'v5imraan' ), __( 'Ad delivery and measurement.', 'v5imraan' ) );
}
add_action( 'init', 'v5imraan_consent_cookie_info' );

/*
 * Take over the Microsoft Clarity plugin's inline tag so the theme is
 * the single, consent-gated Clarity loader. If the plugin is active but
 * its tag cannot be removed, the theme does not add a second one.
 */
add_action(
	'wp_head',
	function () {
		$id = '';
		if ( function_exists( 'clarity_add_script_to_header' ) ) {
			$plugin_id = (string) get_option( 'clarity_project_id', '' );
			if ( '' !== $plugin_id && remove_action( 'wp_head', 'clarity_add_script_to_header' ) ) {
				$id = $plugin_id;
			}
		} else {
			$id = '76f2exgijq'; // Clarity plugin gone: the theme still loads the project.
		}
		$GLOBALS['v5imraan_clarity_id'] = preg_replace( '/[^a-z0-9]/i', '', (string) apply_filters( 'v5imraan_clarity_project_id', $id ) );
	},
	0
);

/**
 * Services the loader manages.
 *
 * @return array[]
 */
function v5imraan_consent_services() {
	$services = array();

	$ahrefs = (string) apply_filters( 'v5imraan_ahrefs_key', 'yFp27iiWkCVN/wwhlycpzQ' );
	if ( '' !== $ahrefs ) {
		$services[] = array(
			'id'       => 'ahrefs',
			'category' => 'statistics',
			'when'     => 'ready',
			'src'      => 'https://analytics.ahrefs.com/analytics.js',
			'attrs'    => array( 'data-key' => $ahrefs ),
		);
	}

	$clarity = isset( $GLOBALS['v5imraan_clarity_id'] ) ? (string) $GLOBALS['v5imraan_clarity_id'] : '';
	if ( '' !== $clarity ) {
		$services[] = array(
			'id'       => 'clarity',
			'category' => 'statistics',
			'when'     => 'idle',
			'src'      => 'https://www.clarity.ms/tag/' . rawurlencode( $clarity ) . '?ref=wordpress',
			'attrs'    => array(),
		);
	}

	if ( ! is_front_page() ) {
		$services[] = array(
			'id'       => 'adsense',
			'category' => 'marketing',
			'when'     => 'idle',
			'src'      => 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1106747567696734',
			'attrs'    => array(
				'data-ad-client' => 'ca-pub-1106747567696734',
				'crossorigin'    => 'anonymous',
			),
		);
	}

	return (array) apply_filters( 'v5imraan_consent_services', $services );
}

/*
 * Print the loader. It is inline (no extra request) and only registers
 * listeners while the page parses; the Consent API script is deferred,
 * so the first consent check runs on DOMContentLoaded, after it.
 */
add_action(
	'wp_footer',
	function () {
		$services = v5imraan_consent_services();
		if ( empty( $services ) ) {
			return;
		}
		$config = array(
			'services' => array_values( $services ),
			'delay'    => (bool) apply_filters( 'v5imraan_delay_third_party', true ),
		);
		?>
<script id="v5-consent-loader">
(function () {
	var cfg = <?php echo wp_json_encode( $config ); ?>;
	var loaded = {};
	var domReady = false;
	var idle = ! cfg.delay;
	var typeDefined = false;
	var evts = [ 'pointerdown', 'keydown', 'touchstart', 'scroll', 'mousemove' ];

	function hasConsent( category ) {
		if ( 'function' !== typeof window.wp_has_consent ) {
			return true; // WP Consent API inactive: keep the old behaviour.
		}
		try { return !! window.wp_has_consent( category ); } catch ( e ) { return false; }
	}
	function consentType() {
		return 'undefined' !== typeof window.wp_consent_type ? window.wp_consent_type : ( window.wp_fallback_consent_type || '' );
	}
	function typeKnown() {
		return typeDefined || ! window.waitfor_consent_hook || 'undefined' !== typeof window.wp_consent_type;
	}
	function clarityConsent() {
		if ( ! consentType() || ! loaded.clarity ) { return; }
		window.clarity( 'consentv2', {
			ad_Storage: hasConsent( 'marketing' ) ? 'granted' : 'denied',
			analytics_Storage: hasConsent( 'statistics' ) ? 'granted' : 'denied'
		} );
	}
	function inject( s ) {
		var el = document.createElement( 'script' );
		el.async = true;
		el.src = s.src;
		for ( var k in s.attrs ) { if ( s.attrs.hasOwnProperty( k ) ) { el.setAttribute( k, s.attrs[ k ] ); } }
		document.head.appendChild( el );
	}
	function run() {
		if ( ! domReady || ! typeKnown() ) { return; }
		cfg.services.forEach( function ( s ) {
			if ( loaded[ s.id ] || ( 'idle' === s.when && ! idle ) || ! hasConsent( s.category ) ) { return; }
			loaded[ s.id ] = true;
			if ( 'clarity' === s.id ) {
				window.clarity = window.clarity || function () { ( window.clarity.q = window.clarity.q || [] ).push( arguments ); };
			}
			inject( s );
		} );
		clarityConsent();
	}
	function goIdle() {
		if ( idle ) { return; }
		idle = true;
		evts.forEach( function ( e ) { window.removeEventListener( e, goIdle, { passive: true } ); } );
		run();
	}

	if ( cfg.delay ) {
		evts.forEach( function ( e ) { window.addEventListener( e, goIdle, { passive: true } ); } );
		window.addEventListener( 'load', function () { setTimeout( goIdle, 3000 ); } );
	}
	document.addEventListener( 'wp_consent_type_defined', function () { typeDefined = true; run(); } );
	document.addEventListener( 'wp_listen_for_consent_change', run );
	document.addEventListener( 'DOMContentLoaded', function () { domReady = true; run(); } );
	if ( 'complete' === document.readyState ) { domReady = true; run(); }
})();
</script>
		<?php
	},
	99
);
