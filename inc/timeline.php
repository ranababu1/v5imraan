<?php
/**
 * Writing Timeline (/timeline/): data + cached markup.
 *
 * One WP_Query pulls every published, indexable post; the rendered
 * timeline is cached in a transient and flushed whenever a post is
 * saved, deleted or changes status.
 *
 * @package v5imraan
 */

if ( ! function_exists( 'v5imraan_timeline_cache_key' ) ) :
	/**
	 * Transient key; includes this file's mtime so deploys bust it.
	 *
	 * @return string
	 */
	function v5imraan_timeline_cache_key() {
		return 'v5_timeline_' . substr( md5( (string) filemtime( __FILE__ ) . '|' . (string) filemtime( get_stylesheet_directory() . '/page-timeline.php' ) ), 0, 10 );
	}
endif;

if ( ! function_exists( 'v5imraan_timeline_flush' ) ) :
	/**
	 * Drop the cached timeline.
	 *
	 * @param int $post_id Post ID (unused beyond type check).
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
	 * Posts grouped as [ year => [ 'Y-m-d' => [ items ] ] ], newest first.
	 *
	 * @return array{years: array, total: int}
	 */
	function v5imraan_timeline_data() {
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

		$years = array();
		$total = 0;
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
			$years[ $year ][ $day ][] = array(
				'title' => get_the_title( $p ),
				'url'   => get_permalink( $p ),
				'meta'  => array_slice( array_values( array_unique( $terms ) ), 0, 4 ),
			);
			$total++;
		}
		krsort( $years );
		return array(
			'years' => $years,
			'total' => $total,
		);
	}
endif;

if ( ! function_exists( 'v5imraan_timeline_html' ) ) :
	/**
	 * Rendered filter row + year sections (cached).
	 *
	 * @return string
	 */
	function v5imraan_timeline_html() {
		$key  = v5imraan_timeline_cache_key();
		$html = get_transient( $key );
		if ( is_string( $html ) && '' !== $html ) {
			return $html;
		}

		$data  = v5imraan_timeline_data();
		$years = $data['years'];

		ob_start();
		?>
		<nav class="tl-filter" aria-label="<?php esc_attr_e( 'Filter by year', 'v5imraan' ); ?>">
			<ul class="tl-filter__list">
				<li><a class="tl-chip is-active" href="#timeline" data-year="all" aria-current="true"><?php esc_html_e( 'All', 'v5imraan' ); ?></a></li>
				<?php foreach ( array_keys( $years ) as $y ) : ?>
					<li><a class="tl-chip" href="#y<?php echo (int) $y; ?>" data-year="<?php echo (int) $y; ?>"><?php echo (int) $y; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div id="timeline" class="tl-years" data-total="<?php echo (int) $data['total']; ?>">
			<?php
			foreach ( $years as $y => $days ) :
				$count = 0;
				foreach ( $days as $items ) {
					$count += count( $items );
				}
				?>
				<section class="tl-year" id="y<?php echo (int) $y; ?>" data-year="<?php echo (int) $y; ?>" aria-labelledby="y<?php echo (int) $y; ?>-h">
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
						<?php foreach ( $days as $day => $items ) : ?>
							<li class="tl-day">
								<time class="tl-day__date" datetime="<?php echo esc_attr( $day ); ?>"><?php echo esc_html( date_i18n( 'M d', strtotime( $day ) ) ); ?></time>
								<ul class="tl-day__posts">
									<?php foreach ( $items as $it ) : ?>
										<li class="tl-post">
											<a class="tl-post__link" href="<?php echo esc_url( $it['url'] ); ?>"><?php echo esc_html( $it['title'] ); ?><span class="tl-post__arrow" aria-hidden="true">&rarr;</span></a>
											<?php if ( $it['meta'] ) : ?>
												<p class="tl-post__meta">
													<?php
													echo implode(
														'<span class="tl-post__sep" aria-hidden="true">&middot;</span>',
														array_map( 'esc_html', $it['meta'] )
													);
													?>
												</p>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</li>
						<?php endforeach; ?>
					</ol>
				</section>
			<?php endforeach; ?>
		</div>
		<?php
		$html = (string) ob_get_clean();
		set_transient( $key, $html, DAY_IN_SECONDS );
		return $html;
	}
endif;

/**
 * Timeline stylesheet only on the timeline page.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_page( 'timeline' ) ) {
			wp_enqueue_style( 'v5-timeline', get_stylesheet_directory_uri() . '/css/timeline.css', array( 'main-style' ), v5imraan_asset_version( 'css/timeline.css' ) );
		}
	},
	20
);
