<?php

/**
 * v5imraan functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package v5imraan
 */

if (! defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function v5imraan_setup()
{
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on v5imraan, use a find and replace
		* to change 'v5imraan' to the name of your theme in all the template files.
		*/
	load_theme_textdomain('v5imraan', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support('title-tag');

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Primary', 'v5imraan'),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
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
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
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
add_action('after_setup_theme', 'v5imraan_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function v5imraan_content_width()
{
	$GLOBALS['content_width'] = apply_filters('v5imraan_content_width', 640);
}
add_action('after_setup_theme', 'v5imraan_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function v5imraan_widgets_init()
{
	register_sidebar(
		array(
			'name'          => esc_html__('Sidebar', 'v5imraan'),
			'id'            => 'sidebar-1',
			'description'   => esc_html__('Add widgets here.', 'v5imraan'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'v5imraan_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function v5imraan_scripts()
{
	$style_path = get_stylesheet_directory() . '/style.css';
	$style_version = filemtime($style_path);

	wp_enqueue_style('main-style', get_stylesheet_uri(), array(), $style_version);
	wp_style_add_data('main-style', 'rtl', 'replace');

	wp_enqueue_script('v5imraan-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'v5imraan_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}

// Disable Gutenberg for all post types
add_filter('use_block_editor_for_post', '__return_false', 10);

// Disable Gutenberg for widgets
add_filter('use_widgets_block_editor', '__return_false');



function mainJs()
{
	wp_enqueue_script('main-js', get_template_directory_uri() . '/js/main.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'mainJs');


function singlePostCss()
{
	if (is_single()) {
		wp_enqueue_style('single-post-styles', get_template_directory_uri() . '/css/post.css');
	}
}
add_action('wp_enqueue_scripts', 'singlePostCss');


function injectAdsense()
{
	if (is_front_page()) {
	} elseif (is_home()) {
		echo '<script data-ad-client="ca-pub-1106747567696734" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
	} else {
		echo '<script data-ad-client="ca-pub-1106747567696734" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
	}
}

add_action('wp_head', 'injectAdsense');

function block_jacks()
{
	header('X-FRAME-OPTIONS: SAMEORIGIN');
}
add_action('send_headers', 'block_jacks', 10);

add_filter("use_block_editor_for_post_type", "disable_gutenberg_editor");
function disable_gutenberg_editor()
{
	return false;
}


function custom_upload_mimes($existing_mimes = array())
{
	$existing_mimes['svg'] = 'image/svg+xml';
	return $existing_mimes;
}
add_filter('upload_mimes', 'custom_upload_mimes');
