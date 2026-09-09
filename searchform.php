<?php
/**
 * Search form.
 *
 * @package v5imraan
 */

$v5imraan_search_id = wp_unique_id( 'v5-search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $v5imraan_search_id ); ?>">
		<?php esc_html_e( 'Search articles', 'v5imraan' ); ?>
	</label>
	<input type="search" id="<?php echo esc_attr( $v5imraan_search_id ); ?>" class="search-form__input" placeholder="<?php esc_attr_e( 'Search articles…', 'v5imraan' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
	<button type="submit" class="search-form__btn" aria-label="<?php esc_attr_e( 'Search', 'v5imraan' ); ?>">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
	</button>
</form>