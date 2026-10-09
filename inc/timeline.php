<?php
/**
 * Writing Timeline (/timeline/): data, chunked rendering, REST endpoint.
 *
 * One WP_Query pulls every published, indexable post; the grouped data
 * is cached in a transient (flushed on save_post / deleted_post /
 * transition_post_status). The page renders the first chunk (~40 posts,
 * whole date groups only); further chunks or whole years come from
 * GET /wp-json/v5imraan/v1/timeline?page=N or ?y=YYYY.
 *
 * @package v5imraan
 */

if ( ! defined( 'V5_TIMELINE_CHUNK' ) ) {
	define( 'V5_TIMELINE_CHUNK', 40 );
}

if ( ! function_exists( 'v5imraan_timeline_cache_key' ) ) :
	/**
	 * Transient key; includes this file's mtime so deploys bust it.
	 *
	 * @return string
	 */
	function v5imraan_timeline_cache_key() {
		return 'v5_timeline_data_' . substr( md5( (string) filemtime( __FILE__ ) ), 0, 10 );
	}
endif;

if ( ! function_exists( 'v5imraan_timeline_flush' ) ) :
	/**
	 * Drop the cached timeline data.
	 *
	 * @param int $post_id Post ID.
	 */
	function v5imraan_timeline_flush( $post_id = 0 ) {
		if ( $post_id && 'post' !== get_post_type( $post_id ) ) {
			return;
		}
		delete_transient( v5imraan_timeline_cache_key() );
	}
endif;
add_action( 'save_post', 'v5imraan_timeline_flush' );
add_action( 'deleted_post', 'v5imraan_timeline_flush' );
add_action(
	'transition_post_status',
	function ( $new, $old, $post ) {
		if ( $new !== $old && $post instanceof WP_Post && 'post' === $post->post_type ) {
			v5imraan_timeline_flush();
		}
	},
	10,
	3
);

if ( ! function_exists( 'v5imraan_timeline_data' ) ) :
	/**
	 * Cached timeline data.
	 *
	 * @return array{days: array, year_totals: array, total: int, pages: array}
	 */
	function v5imraan_timeline_data() {
		$key  = v5imraan_timeline_cache_key();
		$data = get_transient( $key );
		if ( is_array( $data ) && isset( $data['days'], $data['pages'] ) ) {
			return $data;
		}

		$q = new WP_Query(
			array(
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'orderby'                => 'date',
				'order'                  => 'DESC',
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => true,
				'update_post_meta_cache' => false,
				'meta_query'             => array(
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

		$days        = array();
		$year_totals = array();
		$total       = 0;
		foreach ( $q->posts as $p ) {
			$ts    = strtotime( $p->post_date );
			$year  = (int) gmdate( 'Y', $ts );
			$day   = gmdate( 'Y-m-d', $ts );
			$terms = array();
			foreach ( (array) get_the_terms( $p, 'category' ) as $t ) {
				if ( $t instanceof WP_Term && 'uncategorized' !== $t->slug ) {
					$terms[] = $t->name;
				}
			}
			$tags = get_the_terms( $p, 'post_tag' );
			if ( is_array( $tags ) ) {
				foreach ( $tags as $t ) {
					$terms[] = $t->name;
				}
			}
			if ( ! isset( $days[ $day ] ) ) {
				$days[ $day ] = array(
					'day'   => $day,
					'year'  => $year,
					'items' => array(),
				);
			}
			$days[ $day ]['items'][] = array(
				'title' => get_the_title( $p ),
				'url'   => get_permalink( $p ),
				'meta'  => array_slice( array_values( array_unique( $terms ) ), 0, 4 ),
			);
			$year_totals[ $year ] = ( $year_totals[ $year ] ?? 0 ) + 1;
			$total++;
		}
		$days = array_values( $days );
		krsort( $year_totals );

		// Chunk boundaries: whole date groups, ~V5_TIMELINE_CHUNK posts each.
		$pages = array();
		$start = 0;
		$count = 0;
		foreach ( $days as $i => $d ) {
			$count += count( $d['items'] );
			if ( $count >= V5_TIMELINE_CHUNK ) {
				$pages[] = array( $start, $i );
				$start   = $i + 1;
				$count   = 0;
			}
		}
		if ( $start < count( $days ) ) {
			$pages[] = array( $start, count( $days ) - 1 );
		}

		$data = array(
			'days'        => $days,
			'year_totals' => $year_totals,
			'total'       => $total,
			'pages'       => $pages,
		);
		set_transient( $key, $data, DAY_IN_SECONDS );
		return $data;
	}
endif;

if ( ! function_exists( 'v5imraan_timeline_slice' ) ) :
	/**
	 * Days for a page (1-based) or a whole year.
	 *
	 * @param array $data Timeline data.
	 * @param int   $page Page number (ignored when $year is set).
	 * @param int   $year Year, or 0.
	 * @return array{days: array, next: int}
	 */
	function v5imraan_timeline_slice( $data, $page = 1, $year = 0 ) {
		if ( $year ) {
			return array(
				'days' => array_values(
					array_filter(
						$data['days'],
						function ( $d ) use ( $year ) {
							return (int) $d['year'] === (int) $year;
						}
					)
				),
				'next' => 0,
			);
		}
		$n = count( $data['pages'] );
		if ( $page < 1 || $page > $n ) {
			return array(
				'days' => array(),
				'next' => 0,
			);
		}
		list( $a, $b ) = $data['pages'][ $page - 1 ];
		return array(
			'days' => array_slice( $data['days'], $a, $b - $a + 1 ),
			'next' => $page < $n ? $page + 1 : 0,
		);
	}
endif;

if ( ! function_exists( 'v5imraan_timeline_render_days' ) ) :
	/**
	 * Year sections for a list of days (headers carry full-year totals).
	 *
	 * @param array $days        Day groups, newest first.
	 * @param array $year_totals Year => total posts.
	 * @return string
	 */
	function v5imraan_timeline_render_days( $days, $year_totals ) {
		$by_year = array();
		foreach ( $days as $d ) {
			$by_year[ (int) $d['year'] ][] = $d;
		}
		ob_start();
		foreach ( $by_year as $y => $ydays ) :
			$count = (int) ( $year_totals[ $y ] ?? 0 );
			?>
			<section class="tl-year" id="y<?php echo (int) $y; ?>" data-year="<?php echo (int) $y; ?>" data-count="<?php echo (int) $count; ?>" aria-labelledby="y<?php echo (int) $y; ?>-h">
				<header class="tl-year__head">
					<h2 class="tl-year__title" id="y<?php echo (int) $y; ?>-h"><?php echo (int) $y; ?></h2>
					<p class="tl-year__count">
						<?php
						/* translators: %s: number of articles. */
						echo esc_html( sprintf( _n( '%s article', '%s articles', $count, 'v5imraan' ), number_format_i18n( $count ) ) );
						?>
					</p>
				</header>
				<ol class="tl-days">
					<?php foreach ( $ydays as $d ) : ?>
						<li class="tl-day" data-day="<?php echo esc_attr( $d['day'] ); ?>">
							<time class="tl-day__date" datetime="<?php echo esc_attr( $d['day'] ); ?>"><?php echo esc_html( date_i18n( 'l, M j', strtotime( $d['day'] ) ) ); ?></time>
							<ul class="tl-day__posts">
								<?php foreach ( $d['items'] as $it ) : ?>
									<li class="tl-post">
										<a class="tl-post__link" href="<?php echo esc_url( $it['url'] ); ?>"><?php echo esc_html( $it['title'] ); ?><span class="tl-post__arrow" aria-hidden="true">&rarr;</span></a>
										<?php if ( $it['meta'] ) : ?>
											<p class="tl-post__meta"><?php echo implode( '<span class="tl-post__sep" aria-hidden="true">&middot;</span>', array_map( 'esc_html', $it['meta'] ) ); ?></p>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>
			<?php
		endforeach;
		return (string) ob_get_clean();
	}
endif;

/**
 * REST: GET /wp-json/v5imraan/v1/timeline?page=N | ?y=YYYY
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'v5imraan/v1',
			'/timeline',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'args'                => array(
					'page' => array(
						'type'    => 'integer',
						'default' => 1,
						'minimum' => 1,
					),
					'y'    => array(
						'type'    => 'integer',
						'default' => 0,
					),
				),
				'callback'            => function ( WP_REST_Request $req ) {
					$data  = v5imraan_timeline_data();
					$year  = (int) $req->get_param( 'y' );
					$page  = (int) $req->get_param( 'page' );
					$slice = v5imraan_timeline_slice( $data, $page, $year );
					$res   = new WP_REST_Response(
						array(
							'html'  => v5imraan_timeline_render_days( $slice['days'], $data['year_totals'] ),
							'next'  => $slice['next'],
							'posts' => array_sum(
								array_map(
									function ( $d ) {
										return count( $d['items'] );
									},
									$slice['days']
								)
							),
						)
					);
					$res->header( 'Cache-Control', 'public, max-age=600' );
					$res->header( 'X-LiteSpeed-Cache-Control', 'public,max-age=600' );
					return $res;
				},
			)
		);
	}
);

/**
 * Timeline assets only on the timeline page.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_page( 'timeline' ) ) {
			wp_enqueue_style( 'v5-timeline', get_stylesheet_directory_uri() . '/css/timeline.css', array( 'main-style' ), v5imraan_asset_version( 'css/timeline.css' ) );
			wp_enqueue_script( 'v5-timeline', get_stylesheet_directory_uri() . '/js/timeline.js', array(), v5imraan_asset_version( 'js/timeline.js' ), true );
		}
	},
	20
);
