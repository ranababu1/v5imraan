<?php
/**
 * Classic editor (TinyMCE) enhancements.
 *
 * The theme disables the block editor (see functions.php), so posts and
 * pages are edited in the classic editor. This file adds a "Formats"
 * dropdown right next to the Paragraph/Headings dropdown with:
 *
 * - Code       -> wraps the selection in <code> (inline code)
 * - Code block -> converts the current paragraph to <pre> (block code)
 *
 * Both are styled by css/blog.css, so code can be formatted without
 * toggling the editor to the Text (HTML) tab.
 *
 * @package v5imraan
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Put the TinyMCE "Formats" (styleselect) dropdown in toolbar row 1,
 * right after the Paragraph dropdown.
 *
 * @param array $buttons Row-1 toolbar buttons.
 * @return array
 */
function v5imraan_mce_buttons_formats( $buttons ) {
	if ( in_array( 'styleselect', $buttons, true ) ) {
		return $buttons;
	}

	$pos = array_search( 'formatselect', $buttons, true );
	if ( false !== $pos ) {
		array_splice( $buttons, (int) $pos + 1, 0, array( 'styleselect' ) );
	} else {
		array_unshift( $buttons, 'styleselect' );
	}

	return $buttons;
}
add_filter( 'mce_buttons', 'v5imraan_mce_buttons_formats' );

/**
 * Register the Code formats shown in the "Formats" dropdown.
 *
 * "Code" is an inline format, so it cannot live inside the Paragraph
 * (block format) dropdown itself; the Formats dropdown beside it is the
 * standard TinyMCE home for inline formats. The Paragraph dropdown's
 * built-in "Preformatted" item also maps to <pre>.
 *
 * @param array $init TinyMCE init settings.
 * @return array
 */
function v5imraan_tiny_mce_code_formats( $init ) {
	$init['style_formats'] = wp_json_encode(
		array(
			array(
				'title'  => 'Code',
				'inline' => 'code',
			),
			array(
				'title' => 'Code block',
				'block' => 'pre',
			),
		)
	);

	return $init;
}
add_filter( 'tiny_mce_before_init', 'v5imraan_tiny_mce_code_formats' );