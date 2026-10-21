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
 * Yoast's own JSON-LD output is blocked here too — schema is written
 * by hand in every article — while Yoast's remaining output (titles,
 * meta, robots, Open Graph, sitemaps) keeps running untouched.
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

if ( ! function_exists( 'v5imraan_remove_yoast_schema_presenter' ) ) :
	/**
	 * Drop Yoast's schema presenter from the front-end head output
	 * (Yoast 14+). With the presenter gone, the schema graph is never
	 * even generated, on any page type.
	 *
	 * @param array $presenters Fully qualified presenter class names.
	 * @return array
	 */
	function v5imraan_remove_yoast_schema_presenter( $presenters ) {
		return array_diff( $presenters, array( 'Yoast\WP\SEO\Presenters\Schema_Presenter' ) );
	}
endif;

/*
 * Permanently block Yoast's JSON-LD schema output.
 *
 * Yoast stays installed for titles, meta descriptions, robots, Open
 * Graph and sitemaps, but its auto-generated schema graph is replaced
 * by hand-written JSON-LD in every article — any output left here
 * would only duplicate or contradict that.
 *
 * Two independent kill switches keep it dead across Yoast versions:
 *
 * 1. 'wpseo_json_ld_output' returning false makes the schema presenter
 *    bail before printing its script tag — legacy filter, honoured by
 *    every modern Yoast release (still present in current source).
 * 2. 'wpseo_frontend_presenter_classes' removes the schema presenter
 *    from the head output entirely (Yoast 14+).
 *
 * Both are registered from the theme, so the block survives plugin
 * updates and cannot be undone from the Yoast settings UI.
 */
add_filter( 'wpseo_json_ld_output', '__return_false', PHP_INT_MAX );
add_filter( 'wpseo_frontend_presenter_classes', 'v5imraan_remove_yoast_schema_presenter' );

if ( ! function_exists( 'v5imraan_social_profiles' ) ) :
	/**
	 * The site author's social profiles — single source of truth.
	 *
	 * Used by the Person schema sameAs (inc/seo.php), the author
	 * archive chips (inc/blog.php) and the footer links (footer.php)
	 * so the handles can never drift apart between templates.
	 *
	 * @return array Network label => profile URL.
	 */
	function v5imraan_social_profiles() {
		return apply_filters(
			'v5imraan_social_profiles',
			array(
				'X (Twitter)' => 'https://x.com/ihm185',
				'Instagram'   => 'https://www.instagram.com/imraan.dev/',
				'Quora'       => 'https://www.quora.com/profile/Imran-M-4',
			)
		);
	}
endif;

if ( ! function_exists( 'v5imraan_person_schema' ) ) :
	/**
	 * Person schema for the site owner. Extend sameAs via the
	 * 'v5imraan_social_profiles' filter when new profiles are added.
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
		$person['sameAs'] = array_values( v5imraan_social_profiles() );
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
			// Prefer the 1200x600 v5-blog-wide crop for proper OG card sizing.
			$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'v5-blog-wide' );
			if ( ! $img ) {
				$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
			}
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
if ( ! function_exists( 'v5imraan_front_page_schema' ) ) :
	/**
	 * Hand-written @graph for the front page (house pattern).
	 */
	function v5imraan_front_page_schema() {
		if ( ! is_front_page() ) {
			return;
		}
		$home    = home_url( '/' );
		$person  = home_url( '/author/imran/#person' );
		$website = home_url( '/#website' );
		$lang    = get_bloginfo( 'language' );
		$graph   = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type'         => 'Person',
					'@id'           => $person,
					'name'          => 'Imranul Haque Mazumder',
					'alternateName' => array( 'Imran', 'Imraan', 'Imran M' ),
					'url'           => home_url( '/author/imran/' ),
					'description'   => 'Imranul Haque Mazumder is a Tech Leader and Associate Director of Technology at CleverTap who builds end-to-end AI systems and writes about AI, system design, Java, cloud and engineering leadership.',
					'jobTitle'      => 'Tech Leader',
					'image'         => array(
						'@type'  => 'ImageObject',
						'url'    => home_url( '/wp-content/uploads/2026/10/imraan-portrait.webp' ),
						'width'  => 1145,
						'height' => 1374,
					),
					'hasCredential' => array(
						'@type'              => 'EducationalOccupationalCredential',
						'name'               => 'Project Management Professional (PMP)',
						'credentialCategory' => 'certification',
						'recognizedBy'       => array(
							'@type' => 'Organization',
							'name'  => 'Project Management Institute',
							'url'   => 'https://www.pmi.org/',
						),
					),
					'knowsAbout'    => array( 'Artificial Intelligence', 'System Design', 'Java', 'Cloud Architecture', 'Engineering Leadership' ),
					'worksFor'      => array(
						'@type' => 'Organization',
						'name'  => 'CleverTap',
						'url'   => 'https://clevertap.com/',
					),
					'sameAs'        => array(
						'https://www.linkedin.com/in/imazumder/',
						'https://github.com/ranababu1',
						'https://x.com/ihm185',
						'https://www.instagram.com/imraan.dev/',
						'https://www.quora.com/profile/Imran-M-4',
						'https://medium.com/@imrn.dev/about',
						'https://about.me/imraan',
						'https://topmate.io/mazumder',
					),
				),
				array(
					'@type'           => 'WebSite',
					'@id'             => $website,
					'url'             => $home,
					'name'            => 'Imraan',
					'description'     => 'Personal website and technical blog of Imranul Haque Mazumder, covering software engineering, AI, system design, cloud, architecture, and technology.',
					'publisher'       => array( '@id' => $person ),
					'inLanguage'      => $lang,
					'potentialAction' => array(
						'@type'       => 'SearchAction',
						'target'      => array(
							'@type'       => 'EntryPoint',
							'urlTemplate' => home_url( '/?s={search_term_string}' ),
						),
						'query-input' => 'required name=search_term_string',
					),
				),
				array(
					'@type'      => array( 'WebPage', 'ProfilePage' ),
					'@id'        => $home . '#webpage',
					'url'        => $home,
					'name'       => wp_get_document_title(),
					'isPartOf'   => array( '@id' => $website ),
					'about'      => array( '@id' => $person ),
					'mainEntity' => array( '@id' => $person ),
					'inLanguage' => $lang,
				),
			),
		);
		$graph['@graph'][2]['primaryImageOfPage'] = array( '@type' => 'ImageObject', 'url' => home_url( '/wp-content/uploads/2026/10/imraan-portrait.webp' ) );
		echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
endif;
add_action( 'wp_head', 'v5imraan_front_page_schema', 3 );

if ( ! function_exists( 'v5imraan_term_meta_description' ) ) :
	/**
	 * Use the hand-written term meta description (term meta key wpseo_desc)
	 * on category, tag and taxonomy archives when one is set.
	 *
	 * Yoast keeps term SEO in its own option and indexables, which the
	 * site tooling cannot write. This lets a description saved as term meta
	 * win over the stored Yoast value, for the meta, Open Graph and X tags.
	 *
	 * @param string $description Description Yoast is about to print.
	 * @return string
	 */
	function v5imraan_term_meta_description( $description ) {
		if ( ! ( is_category() || is_tag() || is_tax() ) ) {
			return $description;
		}
		$term = get_queried_object();
		if ( ! ( $term instanceof WP_Term ) ) {
			return $description;
		}
		$custom = trim( (string) get_term_meta( $term->term_id, 'wpseo_desc', true ) );
		return '' !== $custom ? $custom : $description;
	}
endif;
add_filter( 'wpseo_metadesc', 'v5imraan_term_meta_description', 20 );
add_filter( 'wpseo_opengraph_desc', 'v5imraan_term_meta_description', 20 );
add_filter( 'wpseo_twitter_description', 'v5imraan_term_meta_description', 20 );

if ( ! function_exists( 'v5imraan_blog_index_title' ) ) :
	/**
	 * Title for the posts page (/blog/ and /blog/page/N/).
	 *
	 * Yoast's posts-page template left paginated pages as "- Page N of M",
	 * and printed no og:title or X title on /blog/. This builds one title
	 * for all of them: "Blog | Site" and "Blog - Page N of M | Site".
	 *
	 * @param string $title Title Yoast is about to print.
	 * @return string
	 */
	function v5imraan_blog_index_title( $title ) {
		if ( ! is_home() || is_front_page() ) {
			return $title;
		}
		$site  = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$paged = max( 1, (int) get_query_var( 'paged' ) );
		if ( $paged > 1 ) {
			$total = (int) $GLOBALS['wp_query']->max_num_pages;
			return sprintf( 'Blog - Page %1$d of %2$d | %3$s', $paged, max( $paged, $total ), $site );
		}
		return 'Blog | ' . $site;
	}
endif;
add_filter( 'wpseo_title', 'v5imraan_blog_index_title', 20 );
add_filter( 'wpseo_opengraph_title', 'v5imraan_blog_index_title', 20 );
add_filter( 'wpseo_twitter_title', 'v5imraan_blog_index_title', 20 );

if ( ! function_exists( 'v5imraan_blog_index_twitter_desc' ) ) :
	/**
	 * Give /blog/ an X description that matches its Open Graph one.
	 *
	 * @param string $description Description Yoast is about to print.
	 * @return string
	 */
	function v5imraan_blog_index_twitter_desc( $description ) {
		if ( ! is_home() || is_front_page() || '' !== trim( (string) $description ) ) {
			return $description;
		}
		return v5imraan_meta_description();
	}
endif;
add_filter( 'wpseo_twitter_description', 'v5imraan_blog_index_twitter_desc', 20 );

if ( ! function_exists( 'v5imraan_category_archive_schema' ) ) :
	/**
	 * CollectionPage graph for category archives, in the house @graph
	 * pattern: one script, linked to the site Person and WebSite by @id.
	 *
	 * Yoast's own schema is switched off on this site, so category pages
	 * otherwise print no structured data at all.
	 */
	function v5imraan_category_archive_schema() {
		if ( ! is_category() ) {
			return;
		}
		$term  = get_queried_object();
		$query = isset( $GLOBALS['wp_query'] ) ? $GLOBALS['wp_query'] : null;
		if ( ! ( $term instanceof WP_Term ) || ! ( $query instanceof WP_Query ) || ! $query->have_posts() ) {
			return;
		}
		$term_url = get_term_link( $term );
		if ( is_wp_error( $term_url ) ) {
			return;
		}
		$paged   = max( 1, (int) get_query_var( 'paged' ) );
		$url     = $paged > 1 ? trailingslashit( $term_url ) . 'page/' . $paged . '/' : $term_url;
		$person  = home_url( '/author/imran/#person' );
		$website = home_url( '/#website' );
		$lang    = get_bloginfo( 'language' );

		$desc = trim( (string) get_term_meta( $term->term_id, 'wpseo_desc', true ) );
		if ( '' === $desc ) {
			$desc = wp_strip_all_tags( term_description( $term ) );
			$desc = mb_strimwidth( trim( preg_replace( '/\s+/', ' ', $desc ) ), 0, 300, '...' );
		}

		$items = array();
		$pos   = 1;
		foreach ( $query->posts as $p ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'url'      => get_permalink( $p ),
				'name'     => wp_strip_all_tags( get_the_title( $p ) ),
			);
		}

		$crumbs = array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => home_url( '/' ),
			),
		);
		$ancestors = array_reverse( get_ancestors( $term->term_id, 'category', 'taxonomy' ) );
		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, 'category' );
			$link     = $ancestor ? get_term_link( $ancestor ) : '';
			if ( $ancestor && ! is_wp_error( $link ) ) {
				$crumbs[] = array(
					'@type'    => 'ListItem',
					'position' => count( $crumbs ) + 1,
					'name'     => wp_specialchars_decode( $ancestor->name, ENT_QUOTES ),
					'item'     => $link,
				);
			}
		}
		$crumbs[] = array(
			'@type'    => 'ListItem',
			'position' => count( $crumbs ) + 1,
			'name'     => wp_specialchars_decode( $term->name, ENT_QUOTES ),
			'item'     => $term_url,
		);

		$node = array(
			'@type'      => 'CollectionPage',
			'@id'        => $url . '#collection',
			'url'        => $url,
			'name'       => wp_specialchars_decode( wp_get_document_title(), ENT_QUOTES ),
			'isPartOf'   => array( '@id' => $website ),
			'author'     => array( '@id' => $person ),
			'publisher'  => array( '@id' => $person ),
			'about'      => array(
				'@type' => 'Thing',
				'name'  => wp_specialchars_decode( $term->name, ENT_QUOTES ),
			),
			'breadcrumb' => array( '@id' => $url . '#breadcrumb' ),
			'mainEntity' => array( '@id' => $url . '#itemlist' ),
			'inLanguage' => $lang,
		);
		if ( '' !== $desc ) {
			$node['description'] = $desc;
		}

		$graph = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type' => 'Person',
					'@id'   => $person,
					'name'  => 'Imranul Haque Mazumder',
					'url'   => home_url( '/author/imran/' ),
				),
				array(
					'@type'     => 'WebSite',
					'@id'       => $website,
					'url'       => home_url( '/' ),
					'name'      => 'Imraan',
					'publisher' => array( '@id' => $person ),
				),
				$node,
				array(
					'@type'           => 'BreadcrumbList',
					'@id'             => $url . '#breadcrumb',
					'itemListElement' => $crumbs,
				),
				array(
					'@type'           => 'ItemList',
					'@id'             => $url . '#itemlist',
					'itemListElement' => $items,
				),
			),
		);
		echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
endif;
add_action( 'wp_head', 'v5imraan_category_archive_schema', 3 );
