<?php
/**
 * Front-end performance: Jetpack sharing off and deferred scripts.
 * AdSense / Clarity / Ahrefs loading lives in inc/consent.php.
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
 * 3. AdSense, Microsoft Clarity and Ahrefs are loaded by the consent-gated
 * loader in inc/consent.php (still after first interaction or 3 seconds
 * after window load for AdSense and Clarity).
 */
