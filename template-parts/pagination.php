<?php
/**
 * Pretty numbered pagination for the main query.
 *
 * the_posts_pagination() respects pretty permalinks and works with
 * the main query, unlike the old custom-WP_Query pagination.
 *
 * @package v5imraan
 */

the_posts_pagination(
	array(
		'mid_size'  => 1,
		'prev_text' => '<span aria-hidden="true">&larr;</span> ' . __( 'Newer posts', 'v5imraan' ),
		'next_text' => __( 'Older posts', 'v5imraan' ) . ' <span aria-hidden="true">&rarr;</span>',
	)
);