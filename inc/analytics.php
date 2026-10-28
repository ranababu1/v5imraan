<?php
/**
 * GA4 custom events (js/analytics.js) and the self-hosted web-vitals
 * build it lazy-loads.
 *
 * GA4 itself (G-P4MWT4TL2X) is loaded by Site Kit; this file only adds
 * the theme's custom events on top. Switch everything off with:
 *     add_filter( 'v5imraan_analytics_events', '__return_false' );
 *
 * @package v5imraan
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the event script (deferred, footer) and its page config.
 */
function v5imraan_enqueue_analytics() {
	if ( ! apply_filters( 'v5imraan_analytics_events', true ) ) {
		return;
	}

	wp_enqueue_script(
		'v5imraan-analytics',
		get_stylesheet_directory_uri() . '/js/analytics.js',
		array(),
		v5imraan_asset_version( 'js/analytics.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	$is_single = is_singular( 'post' );
	$config    = array(
		'isSingle'     => $is_single,
		'is404'        => is_404(),
		'postId'       => $is_single ? get_queried_object_id() : 0,
		'postTitle'    => $is_single ? html_entity_decode( wp_strip_all_tags( get_the_title( get_queried_object_id() ) ), ENT_QUOTES, 'UTF-8' ) : '',
		'webVitalsSrc' => apply_filters( 'v5imraan_web_vitals', true )
			? add_query_arg( 'ver', v5imraan_asset_version( 'js/vendor/web-vitals.iife.js' ), get_stylesheet_directory_uri() . '/js/vendor/web-vitals.iife.js' )
			: '',
	);

	wp_add_inline_script(
		'v5imraan-analytics',
		'window.v5Analytics = ' . wp_json_encode( $config ) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'v5imraan_enqueue_analytics' );
