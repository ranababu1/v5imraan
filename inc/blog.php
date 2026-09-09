<?php
/**
 * Blog helper layer: reading time, breadcrumbs, archive headings,
 * share links, server-side heading anchors, executive summary box
 * and related posts.
 *
 * Everything renders server-side so search engines and AI engines
 * see the full markup without executing JavaScript.
 *
 * @package v5imraan
 */

if ( ! function_exists( 'v5imraan_reading_time' ) ) :
	/**
	 * Estimated reading time in minutes (~225 words per minute).
	 *
	 * @param int $post_id Optional post ID. Defaults to the current post.
	 * @return int Reading time in minutes, minimum 1.
	 */
	function v5imraan_reading_time( $post_id = 0 ) {
		$post = $post_id ? get_post( $post_id ) : get_post();
		if ( ! $post ) {
			return 1;
		}
		$words = str_word_count( wp_strip_all_tags( $post->post_content ) );
		return max( 1, (int) ceil( $words / 225 ) );
	}
endif;

if ( ! function_exists( 'v5imraan_strip_archive_prefix' ) ) :
	/**
	 * Strip the "Category:" / "Tag:" / "Author:" prefix from archive titles.
	 *
	 * @param string $title Raw archive title.
	 * @return string Title without the prefix.
	 */
	function v5imraan_strip_archive_prefix( $title ) {
		$trimmed = trim( preg_replace( '/^[A-Za-z ]+:\s*/', '', (string) $title ) );
		return '' === $trimmed ? (string) $title : $trimmed;
	}
endif;

if ( ! function_exists( 'v5imraan_get_breadcrumbs' ) ) :
	/**
	 * Build the breadcrumb trail for the current view.
	 *
	 * @return array[] Items as label/url pairs. The current view has an empty url.
	 */
	function v5imraan_get_breadcrumbs() {
		$items      = array(
			array(
				'label' => __( 'Home', 'v5imraan' ),
				'url'   => home_url( '/' ),
			),
		);
		$blog_label = __( 'Blog', 'v5imraan' );
		$blog_url   = '';

		$blog_page_id = (int) get_option( 'page_for_posts' );
		if ( $blog_page_id && get_post( $blog_page_id ) ) {
			$blog_title = get_the_title( $blog_page_id );
			if ( $blog_title ) {
				$blog_label = $blog_title;
			}
			$blog_url = get_permalink( $blog_page_id );
		}

		if ( is_singular( 'post' ) ) {
			if ( $blog_url ) {
				$items[] = array(
					'label' => $blog_label,
					'url'   => $blog_url,
				);
			}
			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				$primary   = $categories[0];
				$term_ids  = get_ancestors( $primary->term_id, 'category', 'taxonomy' );
				$term_ids[] = $primary->term_id;
				foreach ( $term_ids as $term_id ) {
					$term = get_term( $term_id );
					if ( $term && ! is_wp_error( $term ) ) {
						$items[] = array(
							'label' => $term->name,
							'url'   => get_term_link( $term ),
						);
					}
				}
			}
			$items[] = array(
				'label' => get_the_title(),
				'url'   => '',
			);
		} elseif ( is_home() ) {
			$items[] = array(
				'label' => $blog_label,
				'url'   => '',
			);
		} elseif ( is_category() || is_tag() || is_tax() ) {
			if ( $blog_url ) {
				$items[] = array(
					'label' => $blog_label,
					'url'   => $blog_url,
				);
			}
			$term = get_queried_object();
			if ( $term && ! empty( $term->term_id ) ) {
				$ancestors = array_reverse( get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) );
				foreach ( $ancestors as $ancestor_id ) {
					$ancestor = get_term( $ancestor_id );
					if ( $ancestor && ! is_wp_error( $ancestor ) ) {
						$items[] = array(
							'label' => $ancestor->name,
							'url'   => get_term_link( $ancestor ),
						);
					}
				}
				$items[] = array(
					'label' => $term->name,
					'url'   => '',
				);
			}
		} elseif ( is_search() ) {
			$items[] = array(
				/* translators: %s: search query. */
				'label' => sprintf( __( 'Search: %s', 'v5imraan' ), get_search_query() ),
				'url'   => '',
			);
		} elseif ( is_archive() ) {
			$items[] = array(
				'label' => v5imraan_strip_archive_prefix( get_the_archive_title() ),
				'url'   => '',
			);
		}

		return apply_filters( 'v5imraan_breadcrumbs', $items );
	}
endif;

if ( ! function_exists( 'v5imraan_the_breadcrumbs' ) ) :
	/**
	 * Print accessible breadcrumb markup; the current page is never a link.
	 */
	function v5imraan_the_breadcrumbs() {
		$items = v5imraan_get_breadcrumbs();
		if ( count( $items ) < 2 ) {
			return;
		}
		$last = count( $items ) - 1;
		echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'v5imraan' ) . '"><ol>';
		foreach ( $items as $index => $item ) {
			$is_current = $index === $last || '' === $item['url'];
			echo '<li class="breadcrumbs__item' . ( $index === $last ? ' breadcrumbs__item--current' : '' ) . '">';
			if ( $is_current ) {
				echo '<span aria-current="page">' . esc_html( $item['label'] ) . '</span>';
			} else {
				echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
			}
			echo '</li>';
		}
		echo '</ol></nav>';
	}
endif;
if ( ! function_exists( 'v5imraan_archive_heading' ) ) :
	/**
	 * Human-friendly archive label + title for hero headings.
	 *
	 * @return array [label, title].
	 */
	function v5imraan_archive_heading() {
		if ( is_tag() ) {
			return array( __( 'Tag', 'v5imraan' ), v5imraan_strip_archive_prefix( get_the_archive_title() ) );
		}
		if ( is_category() || is_tax() ) {
			return array( __( 'Category', 'v5imraan' ), v5imraan_strip_archive_prefix( get_the_archive_title() ) );
		}
		if ( is_author() ) {
			$user = get_queried_object();
			return array( __( 'Author', 'v5imraan' ), $user && isset( $user->display_name ) ? $user->display_name : __( 'Archives', 'v5imraan' ) );
		}
		if ( is_post_type_archive() ) {
			return array( __( 'Archive', 'v5imraan' ), post_type_archive_title( '', false ) );
		}
		$label = __( 'Archive', 'v5imraan' );
		if ( is_day() ) {
			$label = __( 'Daily archive', 'v5imraan' );
		} elseif ( is_month() ) {
			$label = __( 'Monthly archive', 'v5imraan' );
		} elseif ( is_year() ) {
			$label = __( 'Yearly archive', 'v5imraan' );
		}
		return array( $label, v5imraan_strip_archive_prefix( get_the_archive_title() ) );
	}
endif;

if ( ! function_exists( 'v5imraan_share_links' ) ) :
	/**
	 * Share URLs for the current post.
	 *
	 * @return array Channel => share URL.
	 */
	function v5imraan_share_links() {
		$url   = get_permalink();
		$title = get_the_title() . ' — ' . get_bloginfo( 'name' );
		return array(
			'x'        => 'https://twitter.com/intent/tweet?url=' . rawurlencode( $url ) . '&text=' . rawurlencode( $title ),
			'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url ),
			'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $url ),
			'whatsapp' => 'https://wa.me/?text=' . rawurlencode( $title . ' ' . $url ),
			'telegram' => 'https://t.me/share/url?url=' . rawurlencode( $url ) . '&text=' . rawurlencode( $title ),
		);
	}
endif;

if ( ! function_exists( 'v5imraan_heading_anchors' ) ) :
	/**
	 * Add stable, crawlable id attributes to h2/h3 headings on single posts.
	 * Anchors are rendered server-side so search engines and AI crawlers
	 * see them without executing JavaScript.
	 *
	 * @param string $content Post content.
	 * @return string Filtered content.
	 */
	function v5imraan_heading_anchors( $content ) {
		if ( ! is_singular( 'post' ) || is_feed() || empty( $content ) ) {
			return $content;
		}
		if ( ! preg_match_all( '/<h([23])([^>]*)>(.+?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER ) ) {
			return $content;
		}
		$used = array();
		foreach ( $matches as $match ) {
			if ( false !== stripos( $match[2], 'id=' ) ) {
				continue; // Respect manually authored anchors.
			}
			$slug = sanitize_title( wp_strip_all_tags( $match[3] ) );
			if ( '' === $slug ) {
				$slug = 'section';
			}
			$base = $slug;
			$i    = 2;
			while ( in_array( $slug, $used, true ) ) {
				$slug = $base . '-' . $i;
				$i++;
			}
			$used[]   = $slug;
			$anchor   = '<h' . $match[1] . $match[2] . ' id="' . esc_attr( $slug ) . '">' . $match[3] . '</h' . $match[1] . '>';
			$replaced = preg_replace( '/' . preg_quote( $match[0], '/' ) . '/s', $anchor, $content, 1 );
			if ( null !== $replaced ) {
				$content = $replaced;
			}
		}
		return $content;
	}
endif;
add_filter( 'the_content', 'v5imraan_heading_anchors', 20 );

if ( ! function_exists( 'v5imraan_summary_box' ) ) :
	/**
	 * Wrap a "TL;DR:" / "Executive Summary:" opening paragraph in a
	 * machine-friendly aside so AI engines can lift the quick answer.
	 *
	 * @param string $content Post content.
	 * @return string Filtered content.
	 */
	function v5imraan_summary_box( $content ) {
		if ( ! is_singular( 'post' ) || is_feed() || empty( $content ) ) {
			return $content;
		}
		$pattern = '/^\s*<p([^>]*)>\s*((?:Executive Summary|TL;DR|TLDR)\s*:)\s*(.*?)<\/p>/is';
		if ( ! preg_match( $pattern, $content, $m ) ) {
			return $content;
		}
		$aside    = '<aside class="post-summary" role="note" aria-label="' . esc_attr__( 'Quick answer', 'v5imraan' ) . '"><p' . $m[1] . '><strong class="post-summary__label">' . esc_html( trim( $m[2] ) ) . '</strong> ' . $m[3] . '</p></aside>';
		$replaced = preg_replace( $pattern, $aside, $content, 1 );
		if ( null !== $replaced ) {
			$content = $replaced;
		}
		return $content;
	}
endif;
add_filter( 'the_content', 'v5imraan_summary_box', 30 );

if ( ! function_exists( 'v5imraan_get_related_posts' ) ) :
	/**
	 * Related posts: same category first, latest posts as a fallback.
	 *
	 * @param int $post_id Current post ID.
	 * @param int $count   How many posts to return.
	 * @return WP_Query
	 */
	function v5imraan_get_related_posts( $post_id, $count = 3 ) {
		$args = array(
			'post__not_in'        => array( (int) $post_id ),
			'posts_per_page'      => (int) $count,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);
		$categories = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
		if ( ! empty( $categories ) ) {
			$args['category__in'] = $categories;
		}
		$related = new WP_Query( $args );
		if ( empty( $related->posts ) && ! empty( $categories ) ) {
			unset( $args['category__in'] );
			$related = new WP_Query( $args );
		}
		return $related;
	}
endif;