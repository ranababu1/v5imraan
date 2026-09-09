<?php
/**
 * SEO / AIO / GEO layer for the v5imraan theme.
 *
 * Outputs JSON-LD structured data (Person, WebSite, BlogPosting,
 * BreadcrumbList, ItemList) plus Open Graph / Twitter Card meta tags.
 * Everything is disabled automatically when a dedicated SEO plugin
 * (Yoast, Rank Math, AIOSEO, SEOPress) or Jetpack's Publicize module
 * handles the same job, so nothing is ever duplicated.
 *
 * @package v5imraan
 */

if ( ! function_exists( 'v5imraan_seo_plugin_active' ) ) :
	/**
	 * Detect popular SEO plugins so the theme never outputs
	 * duplicate schema or meta tags.
	 *
	 * @return bool True when an SEO plugin is active.
	 */
	function v5imraan_seo_plugin_active() {
		return defined( 'WPSEO_VERSION' )
			|| defined( 'RANK_MATH_VERSION' )
			|| defined( 'AIOSEO_VERSION' )
			|| defined( 'SEOPRESS_VERSION' );
	}
endif;

if ( ! function_exists( 'v5imraan_schema_enabled' ) ) :
	/**
	 * Whether the theme should print JSON-LD schema.
	 *
	 * @return bool
	 */
	function v5imraan_schema_enabled() {
		return apply_filters( 'v5imraan_schema_enabled', ! v5imraan_seo_plugin_active() );
	}
endif;

if ( ! function_exists( 'v5imraan_social_meta_enabled' ) ) :
	/**
	 * Whether the theme should print Open Graph / Twitter meta tags.
	 *
	 * @return bool
	 */
	function v5imraan_social_meta_enabled() {
		$jetpack_social = false;
		if ( defined( 'JETPACK__VERSION' ) && class_exists( 'Jetpack' ) && method_exists( 'Jetpack', 'is_module_active' ) ) {
			$jetpack_social = Jetpack::is_module_active( 'publicize' );
		}
		return apply_filters( 'v5imraan_social_meta_enabled', ! v5imraan_seo_plugin_active() && ! $jetpack_social );
	}
endif;

if ( ! function_exists( 'v5imraan_person_schema' ) ) :
	/**
	 * Person schema for the site owner. Extend sameAs here when
	 * new social profiles are added.
	 *
	 * @return array
	 */
	function v5imraan_person_schema() {
		$person = array(
			'@type' => 'Person',
			'@id'   => home_url( '/#person' ),
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
			'image' => get_stylesheet_directory_uri() . '/img/imrn.png',
		);
		$description = get_bloginfo( 'description' );
		if ( $description ) {
			$person['description'] = $description;
		}
		$person['sameAs'] = array(
			'https://www.instagram.com/imraan.dev/',
			'https://x.com/ihm185',
			'https://www.quora.com/profile/Imran-M-4',
		);
		return apply_filters( 'v5imraan_person_schema', $person );
	}
endif;

if ( ! function_exists( 'v5imraan_website_schema' ) ) :
	/**
	 * WebSite schema (site name + SearchAction for AI search engines).
	 *
	 * @return array
	 */
	function v5imraan_website_schema() {
		$site = array(
			'@type'           => 'WebSite',
			'@id'             => home_url( '/#website' ),
			'url'             => home_url( '/' ),
			'name'            => get_bloginfo( 'name' ),
			'publisher'       => array( '@id' => home_url( '/#person' ) ),
			'inLanguage'      => get_bloginfo( 'language' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);
		return apply_filters( 'v5imraan_website_schema', $site );
	}
endif;

if ( ! function_exists( 'v5imraan_current_url' ) ) :
	/**
	 * Canonical request URL for meta tags.
	 *
	 * @return string
	 */
	function v5imraan_current_url() {
		if ( is_singular() ) {
			return get_permalink();
		}
		if ( is_post_type_archive() ) {
			return get_post_type_archive_link( get_query_var( 'post_type' ) );
		}
		if ( is_home() ) {
			$page_id = (int) get_option( 'page_for_posts' );
			return $page_id ? get_permalink( $page_id ) : home_url( '/' );
		}
		$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : wp_parse_url( home_url(), PHP_URL_HOST );
		$path = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		return set_url_scheme( '//' . $host . $path );
	}
endif;

if ( ! function_exists( 'v5imraan_meta_description' ) ) :
	/**
	 * Best-effort meta description: post excerpt, archive description
	 * or the site tagline. Capped at 160 characters.
	 *
	 * @return string
	 */
	function v5imraan_meta_description() {
		$description = '';
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post ) {
				if ( has_excerpt( $post ) ) {
					$description = get_the_excerpt( $post );
				} else {
					$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '…' );
				}
			}
		} elseif ( is_home() ) {
			$page_id = (int) get_option( 'page_for_posts' );
			if ( $page_id && has_excerpt( $page_id ) ) {
				$description = get_the_excerpt( $page_id );
			} else {
				$description = get_bloginfo( 'description' );
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$description = term_description();
			if ( ! $description ) {
				$term        = get_queried_object();
				/* translators: %s: archive name. */
				$description = sprintf( __( 'Articles about %s.', 'v5imraan' ), $term && isset( $term->name ) ? $term->name : '' );
			}
		} elseif ( is_author() ) {
			$description = get_the_author_meta( 'description' );
			if ( ! $description ) {
				$user        = get_queried_object();
				/* translators: %s: author name. */
				$description = sprintf( __( 'Articles written by %s.', 'v5imraan' ), $user && isset( $user->display_name ) ? $user->display_name : '' );
			}
		} elseif ( is_search() ) {
			/* translators: %s: search query. */
			$description = sprintf( __( 'Search results for “%s”.', 'v5imraan' ), get_search_query() );
		} else {
			$description = get_bloginfo( 'description' );
		}
		$description = wp_strip_all_tags( (string) $description );
		return mb_strimwidth( $description, 0, 160, '…' );
	}
endif;
if ( ! function_exists( 'v5imraan_first_category_name' ) ) :
	/**
	 * First category name for schema articleSection.
	 *
	 * @param WP_Post|int $post Post object or ID.
	 * @return string
	 */
	function v5imraan_first_category_name( $post ) {
		$categories = get_the_category( $post );
		return empty( $categories ) ? '' : $categories[0]->name;
	}
endif;

if ( ! function_exists( 'v5imraan_article_schema' ) ) :
	/**
	 * BlogPosting schema for the current single post.
	 *
	 * @return array|null Null on non-post views.
	 */
	function v5imraan_article_schema() {
		$post = get_queried_object();
		if ( ! is_singular( 'post' ) || ! $post ) {
			return null;
		}
		$author_id  = (int) $post->post_author;
		$word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );
		$article    = array(
			'@type'            => 'BlogPosting',
			'@id'              => get_permalink( $post ) . '#article',
			'headline'         => get_the_title( $post ),
			'name'             => get_the_title( $post ),
			'description'     => wp_trim_words( wp_strip_all_tags( has_excerpt( $post ) ? get_the_excerpt( $post ) : $post->post_content ), 28, '…' ),
			'author'           => array(
				'@type' => 'Person',
				'@id'   => home_url( '/#person' ),
				'name'  => get_the_author_meta( 'display_name', $author_id ),
				'url'   => get_author_posts_url( $author_id ),
			),
			'publisher'        => array( '@id' => home_url( '/#person' ) ),
			'datePublished'    => get_the_date( DATE_W3C, $post ),
			'dateModified'     => get_the_modified_date( DATE_W3C, $post ),
			'mainEntityOfPage' => array( '@id' => get_permalink( $post ) ),
			'url'              => get_permalink( $post ),
			'wordCount'        => $word_count,
			'timeRequired'     => 'PT' . max( 1, (int) ceil( $word_count / 225 ) ) . 'M',
			'isPartOf'         => array( '@id' => home_url( '/#website' ) ),
			'inLanguage'       => get_bloginfo( 'language' ),
			'articleSection'   => v5imraan_first_category_name( $post ),
		);
		if ( has_post_thumbnail( $post ) ) {
			$img = wp_get_attachment_image_src( (int) get_post_thumbnail_id( $post ), 'full' );
			if ( $img ) {
				$article['image'] = array(
					'@type'  => 'ImageObject',
					'url'    => $img[0],
					'width'  => $img[1],
					'height' => $img[2],
				);
			}
		}
		return apply_filters( 'v5imraan_article_schema', $article );
	}
endif;

if ( ! function_exists( 'v5imraan_breadcrumb_schema' ) ) :
	/**
	 * BreadcrumbList schema for the current view.
	 *
	 * @return array|null Null when no trail exists.
	 */
	function v5imraan_breadcrumb_schema() {
		$items = v5imraan_get_breadcrumbs();
		if ( count( $items ) < 2 ) {
			return null;
		}
		$list_items = array();
		$position   = 1;
		foreach ( $items as $item ) {
			$list_item = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'name'     => $item['label'],
			);
			if ( ! empty( $item['url'] ) ) {
				$list_item['item'] = $item['url'];
			}
			$list_items[] = $list_item;
			$position++;
		}
		return array(
			'@type'           => 'BreadcrumbList',
			'@id'             => v5imraan_current_url() . '#breadcrumb',
			'itemListElement' => $list_items,
		);
	}
endif;
if ( ! function_exists( 'v5imraan_collection_schema' ) ) :
	/**
	 * ItemList schema for blog listing / archive / search pages.
	 *
	 * @return array|null Null on singular views or empty listings.
	 */
	function v5imraan_collection_schema() {
		if ( is_singular() ) {
			return null;
		}
		$query = $GLOBALS['wp_query'];
		if ( ! ( $query instanceof WP_Query ) || ! $query->have_posts() ) {
			return null;
		}
		$posts = array();
		$index = 1;
		foreach ( $query->posts as $p ) {
			$posts[] = array(
				'@type'    => 'ListItem',
				'position' => $index,
				'url'      => get_permalink( $p ),
				'name'     => get_the_title( $p ),
			);
			$index++;
		}
		$name = is_home() ? get_bloginfo( 'name' ) . ' — ' . __( 'Blog', 'v5imraan' ) : wp_get_document_title();
		return apply_filters(
			'v5imraan_collection_schema',
			array(
				'@type'           => 'ItemList',
				'@id'             => v5imraan_current_url() . '#itemlist',
				'name'            => $name,
				'itemListElement' => $posts,
			)
		);
	}
endif;

if ( ! function_exists( 'v5imraan_print_schema' ) ) :
	/**
	 * Print all JSON-LD graph blocks for the current view.
	 */
	function v5imraan_print_schema() {
		if ( ! v5imraan_schema_enabled() ) {
			return;
		}
		$graph = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				v5imraan_person_schema(),
				v5imraan_website_schema(),
			),
		);
		$article = v5imraan_article_schema();
		if ( $article ) {
			$graph['@graph'][] = $article;
		}
		$breadcrumbs = v5imraan_breadcrumb_schema();
		if ( $breadcrumbs ) {
			$graph['@graph'][] = $breadcrumbs;
		}
		$collection = v5imraan_collection_schema();
		if ( $collection ) {
			$graph['@graph'][] = $collection;
		}
		$graph = apply_filters( 'v5imraan_schema_graph', $graph );
		echo "\n" . '<!-- v5imraan structured data -->' . "\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
endif;
add_action( 'wp_head', 'v5imraan_print_schema', 2 );

if ( ! function_exists( 'v5imraan_social_meta_tags' ) ) :
	/**
	 * Print Open Graph + Twitter Card meta tags.
	 */
	function v5imraan_social_meta_tags() {
		if ( ! v5imraan_social_meta_enabled() ) {
			return;
		}
		$title  = wp_get_document_title();
		$url    = v5imraan_current_url();
		$desc   = v5imraan_meta_description();
		$image  = '';
		$width  = 1200;
		$height = 630;

		if ( is_singular() && has_post_thumbnail() ) {
			$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
			if ( $img ) {
				$image  = $img[0];
				$width  = $img[1];
				$height = $img[2];
			}
		}
		if ( ! $image && file_exists( get_stylesheet_directory() . '/img/imrn.png' ) ) {
			$image  = get_stylesheet_directory_uri() . '/img/imrn.png';
			$width  = 256;
			$height = 256;
		}

		echo "\n" . '<!-- v5imraan social meta -->' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
		echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		if ( $image ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
			echo '<meta property="og:image:width" content="' . (int) $width . '">' . "\n";
			echo '<meta property="og:image:height" content="' . (int) $height . '">' . "\n";
		}
		echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
		if ( is_singular( 'post' ) ) {
			$post = get_queried_object();
			if ( $post ) {
				echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( DATE_W3C, $post ) ) . '">' . "\n";
				echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( DATE_W3C, $post ) ) . '">' . "\n";
				echo '<meta property="article:author" content="' . esc_url( get_author_posts_url( (int) $post->post_author ) ) . '">' . "\n";
			}
		}
		echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
		if ( $image ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
		}
	}
endif;
add_action( 'wp_head', 'v5imraan_social_meta_tags', 4 );