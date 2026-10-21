<?php
/**
 * Pretty numbered pagination for the main query.
 *
 * On term archives the links are built from the term's canonical URL, so an
 * alias path such as /java/ (which WordPress also resolves) never produces
 * dead links like /java/page/2/.
 *
 * @package v5imraan
 */

$v5_pagination_args = array(
	'mid_size'  => 1,
	'prev_text' => '<span aria-hidden="true">&larr;</span> ' . __( 'Newer posts', 'v5imraan' ),
	'next_text' => __( 'Older posts', 'v5imraan' ) . ' <span aria-hidden="true">&rarr;</span>',
);

if ( is_category() || is_tag() || is_tax() ) {
	$v5_term      = get_queried_object();
	$v5_term_link = ( $v5_term instanceof WP_Term ) ? get_term_link( $v5_term ) : '';
	if ( $v5_term_link && ! is_wp_error( $v5_term_link ) ) {
		$v5_pagination_args['base']   = trailingslashit( $v5_term_link ) . '%_%';
		$v5_pagination_args['format'] = 'page/%#%/';
	}
}

the_posts_pagination( $v5_pagination_args );
