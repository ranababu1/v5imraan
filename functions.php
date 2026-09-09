<?php
/**
 * v5imraan functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package v5imraan
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Bump the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'v5imraan_asset_version' ) ) :
	/**
	 * Cache-busting version for a theme asset, based on filemtime.
	 *
	 * @param string $file Path relative to the theme directory.
	 * @return string Version string.
	 */
	function v5imraan_asset_version( $file ) {
		$path = get_stylesheet_directory() . '/' . ltrim( $file, '/' );
		return file_exists( $path ) ? (string) filemtime( $path ) : _S_VERSION;
	}
endif;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function v5imraan_setup() {
	// Make theme available for translation.
	load_theme_textdomain( 'v5imraan', get_stylesheet_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Blog card + single post hero image sizes.
	add_image_size( 'v5-blog-card', 660, 440, true );
	add_image_size( 'v5-blog-wide', 1200, 600, true );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'v5imraan' ),
		)
	);

	// Switch default core markup for search form, comment form, and comments to HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'v5imraan_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for core custom logo.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'v5imraan_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * @global int $content_width
 */
function v5imraan_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'v5imraan_content_width', 640 );
}
add_action( 'after_setup_theme', 'v5imraan_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function v5imraan_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'v5imraan' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'v5imraan' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'v5imraan_widgets_init' );

/**
 * Enqueue scripts and styles.
 *
 * blog.css is loaded everywhere because post cards also render on
 * the homepage blog strip and the 404 page.
 */
function v5imraan_scripts() {
	wp_enqueue_style( 'main-style', get_stylesheet_uri(), array(), v5imraan_asset_version( 'style.css' ) );
	wp_style_add_data( 'main-style', 'rtl', 'replace' );

	wp_enqueue_style( 'v5-blog', get_stylesheet_directory_uri() . '/css/blog.css', array( 'main-style' ), v5imraan_asset_version( 'css/blog.css' ) );

	wp_enqueue_script( 'v5imraan-navigation', get_stylesheet_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'main-js', get_stylesheet_directory_uri() . '/js/main.js', array(), v5imraan_asset_version( 'js/main.js' ), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'v5imraan_scripts' );
/**
 * AdSense loader, enqueued through the proper API (with the async
 * attribute and data-ad-client injected via a script_loader_tag
 * filter) instead of a raw <script> echo in wp_head.
 */
function v5imraan_adsense_script_tag( $tag, $handle ) {
	if ( 'v5-adsense' !== $handle ) {
		return $tag;
	}
	return str_replace( '<script', '<script data-ad-client="ca-pub-1106747567696734" async', $tag );
}

function v5imraan_enqueue_adsense() {
	if ( is_front_page() ) {
		return; // Same behavior as before: no ads on the homepage.
	}
	wp_enqueue_script( 'v5-adsense', 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js', array(), null, false );
	add_filter( 'script_loader_tag', 'v5imraan_adsense_script_tag', 10, 2 );
}
add_action( 'wp_enqueue_scripts', 'v5imraan_enqueue_adsense' );

/**
 * Custom template tags for this theme.
 */
require get_stylesheet_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_stylesheet_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_stylesheet_directory() . '/inc/customizer.php';

/**
 * Implement the Custom Header feature.
 */
require get_stylesheet_directory() . '/inc/custom-header.php';

/**
 * Blog helpers: reading time, breadcrumbs, TOC anchors, related posts.
 */
require get_stylesheet_directory() . '/inc/blog.php';

/**
 * SEO / AIO / GEO layer: JSON-LD schema + social meta tags.
 */
require get_stylesheet_directory() . '/inc/seo.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_stylesheet_directory() . '/inc/jetpack.php';
}

// Disable the block editor for all post types and widgets on this site.
add_filter( 'use_block_editor_for_post', '__return_false' );
add_filter( 'use_block_editor_for_post_type', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );

/**
 * Send a same-origin framing header to protect against clickjacking.
 */
function v5imraan_block_jacks() {
	header( 'X-FRAME-OPTIONS: SAMEORIGIN' );
}
add_action( 'send_headers', 'v5imraan_block_jacks' );

/**
 * Allow SVG uploads.
 *
 * @param array $existing_mimes Allowed mime types.
 * @return array
 */
function v5imraan_custom_upload_mimes( $existing_mimes = array() ) {
	$existing_mimes['svg'] = 'image/svg+xml';
	return $existing_mimes;
}
add_filter( 'upload_mimes', 'v5imraan_custom_upload_mimes' );