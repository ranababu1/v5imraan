<?php
/**
 * llms.txt generator for the v5imraan theme (AI/AEO discovery file).
 *
 * Serves https://imraan.in/llms.txt dynamically from WordPress so the
 * file is always current, flagship-first, and byte-for-byte without a
 * BOM: site summary, pillar hubs, then the latest posts of each
 * flagship cluster (System Design, Java, Compliance, AI).
 *
 * IMPORTANT: a physical llms.txt in the web root bypasses WordPress
 * entirely (the web server serves static files before PHP runs).
 * Delete the static file on the server so this generator takes over.
 *
 * Filters: 'v5imraan_llms_txt' (whole document),
 * 'v5imraan_llms_post_sections' (heading => array( slugs, count )),
 * 'v5imraan_llms_site_name', 'v5imraan_llms_summary'.
 *
 * @package v5imraan
 */

if ( ! function_exists( 'v5imraan_llms_category_ids' ) ) :
	/**
	 * Resolve category slugs to IDs, skipping slugs that no longer exist.
	 *
	 * @param string[] $slugs Category slugs.
	 * @return int[] Category IDs.
	 */
	function v5imraan_llms_category_ids( $slugs ) {
		$ids = array();
		foreach ( (array) $slugs as $slug ) {
			$term = get_term_by( 'slug', sanitize_key( $slug ), 'category' );
			if ( $term && ! is_wp_error( $term ) ) {
				$ids[] = (int) $term->term_id;
			}
		}
		return $ids;
	}
endif;

if ( ! function_exists( 'v5imraan_llms_post_description' ) ) :
	/**
	 * One-line description for a post in llms.txt.
	 *
	 * @param WP_Post $post Post object.
	 * @return string Plain text, or '' when nothing usable exists.
	 */
	function v5imraan_llms_post_description( $post ) {
		$text = $post->post_excerpt;
		if ( '' === $text ) {
			$text = wp_strip_all_tags( $post->post_content );
		}
		$text = trim( (string) $text );
		if ( '' === $text ) {
			return '';
		}
		return wp_html_excerpt( $text, 120, '…' );
	}
endif;

if ( ! function_exists( 'v5imraan_llms_section_lines' ) ) :
	/**
	 * Markdown lines for one "## Heading" section listing a cluster's
	 * latest published, indexed (not noindexed) posts.
	 *
	 * @param string $heading      Section heading.
	 * @param int[]  $category_ids Category IDs to include.
	 * @param int    $count        Number of posts to list.
	 * @return string[] Lines, or an empty array when the cluster is empty.
	 */
	function v5imraan_llms_section_lines( $heading, $category_ids, $count = 5 ) {
		if ( empty( $category_ids ) ) {
			return array();
		}
		$posts = get_posts(
			array(
				'category__in'        => array_map( 'intval', $category_ids ),
				'posts_per_page'      => (int) $count,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- bounded query, flagship clusters only.
					'relation' => 'OR',
					array(
						'key'     => '_yoast_wpseo_meta-robots-noindex',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_yoast_wpseo_meta-robots-noindex',
						'value'   => '1',
						'compare' => '!=',
					),
				),
			)
		);
		if ( empty( $posts ) ) {
			return array();
		}
		$lines = array( '## ' . $heading );
		foreach ( $posts as $post ) {
			$line = '- [' . wp_strip_all_tags( $post->post_title ) . '](' . get_permalink( $post ) . ')';
			$desc = v5imraan_llms_post_description( $post );
			if ( '' !== $desc ) {
				$line .= ': ' . $desc;
			}
			$lines[] = $line;
		}
		return $lines;
	}
endif;

if ( ! function_exists( 'v5imraan_build_llms_txt' ) ) :
	/**
	 * Build the full llms.txt document.
	 *
	 * @return string
	 */
	function v5imraan_build_llms_txt() {
		$name    = apply_filters( 'v5imraan_llms_site_name', 'imraan.in — Imranul Haque Mazumder' );
		$summary = apply_filters(
			'v5imraan_llms_summary',
			__( 'Practical, production-grade engineering guides from an engineering leader with 13+ years building AI-driven systems and scalable platforms: system design, Java and Spring Boot, applied AI and agents, DevOps, and privacy compliance (GDPR, CCPA, India DPDP).', 'v5imraan' )
		);

		$blog_id  = (int) get_option( 'page_for_posts' );
		$blog_url = $blog_id ? get_permalink( $blog_id ) : home_url( '/blog/' );

		$lines = array(
			'# ' . $name,
			'',
			'> ' . $summary,
			'',
			'## Hubs',
		);

		$hubs = array(
			array( 'Home', home_url( '/' ), __( 'Engineering leader building AI-driven systems, scalable platforms, and high-performance teams.', 'v5imraan' ) ),
			array( 'Blog', $blog_url, __( 'All articles.', 'v5imraan' ) ),
			array( 'System Design', home_url( '/system-design/' ), __( 'Scaling, load balancing, reliability, observability, distributed systems.', 'v5imraan' ) ),
			array( 'Java', home_url( '/backend-development/java/' ), __( 'Deep Java and Spring Boot tutorials.', 'v5imraan' ) ),
			array( 'Compliance', home_url( '/compliance/' ), __( 'Cookie consent, data privacy and user rights, sensitive data protection.', 'v5imraan' ) ),
			array( 'AI', home_url( '/ai/' ), __( 'Applied AI, agentic workflows, and automation.', 'v5imraan' ) ),
		);
		foreach ( $hubs as $hub ) {
			$lines[] = '- [' . $hub[0] . '](' . $hub[1] . '): ' . $hub[2];
		}

		$sections = apply_filters(
			'v5imraan_llms_post_sections',
			array(
				'System Design' => array(
					array( 'system-design' ),
					5,
				),
				'Java'          => array(
					array( 'java' ),
					5,
				),
				'Compliance'    => array(
					array( 'cookie-compliance', 'data-privacy-user-rights', 'sensitive-data-protection' ),
					5,
				),
				'AI and Agents' => array(
					array( 'ai' ),
					5,
				),
			)
		);
		foreach ( $sections as $heading => $cluster ) {
			$slugs   = isset( $cluster[0] ) ? $cluster[0] : array();
			$count   = isset( $cluster[1] ) ? (int) $cluster[1] : 5;
			$section = v5imraan_llms_section_lines( (string) $heading, v5imraan_llms_category_ids( $slugs ), $count );
			if ( ! empty( $section ) ) {
				$lines[] = '';
				$lines   = array_merge( $lines, $section );
			}
		}

		$content = implode( "\n", $lines ) . "\n";
		return apply_filters( 'v5imraan_llms_txt', $content );
	}
endif;

if ( ! function_exists( 'v5imraan_maybe_serve_llms_txt' ) ) :
	/**
	 * Serve /llms.txt as generated plain text (UTF-8, no BOM).
	 *
	 * Matched on the raw request path instead of a rewrite rule so the
	 * file works immediately on deploy, without a rewrite-rules flush.
	 */
	function v5imraan_maybe_serve_llms_txt() {
		if ( is_admin() ) {
			return;
		}
		$path = '';
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$path = (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH );
		}
		if ( '/llms.txt' !== untrailingslashit( $path ) ) {
			return;
		}

		status_header( 200 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'Cache-Control: public, max-age=3600' );
		echo v5imraan_build_llms_txt();
		exit;
	}
endif;
add_action( 'init', 'v5imraan_maybe_serve_llms_txt' );