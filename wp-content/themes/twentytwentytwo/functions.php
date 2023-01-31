<?php

/**
 * Twenty Twenty-Two functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Two
 * @since Twenty Twenty-Two 1.0
 */


$understrap_inc_dir = 'inc';

// Array of files to include.
$understrap_includes = array(
	'/ajax_product.php',
	'/cfs-options-screens.php',
);

// Include files.
foreach ($understrap_includes as $file) {
	require_once get_theme_file_path($understrap_inc_dir . $file);
}



if (!function_exists('twentytwentytwo_support')) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_support()
	{

		// Add support for block styles.
		add_theme_support('wp-block-styles');

		// Enqueue editor styles.
		add_editor_style('style.css');
	}

endif;

// Exit if accessed directly.
defined('ABSPATH') || exit;

// UnderStrap's includes directory.
$understrap_inc_dir = 'inc';

add_action('after_setup_theme', 'twentytwentytwo_support');

if (!function_exists('twentytwentytwo_styles')) :

	/**
	 * Enqueue styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_styles()
	{
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get('Version');

		$version_string = is_string($theme_version) ? $theme_version : false;
		wp_register_style(
			'twentytwentytwo-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style('twentytwentytwo-style');
	}

endif;

add_action('wp_enqueue_scripts', 'twentytwentytwo_styles');

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';



// Include files.
// foreach ($understrap_includes as $file) {
//     require_once get_theme_file_path($understrap_inc_dir . $file);
// }

require_once get_theme_file_path('post-type/product.php');
require_once get_theme_file_path('api/api.php');
require_once get_theme_file_path('post-type/order.php');


// static router
add_action('rest_api_init', function () {
	register_rest_route('my/v1', '/products-a', [
		'methods' => 'GET',
		'callback' => 'get_products_a',
		'permission_callback' => '__return_true',
	]);
});
// Get all projects and assign thumbnail
function get_products_a($params)
{
	$products =  get_posts([
		'post_type' => 'product',
		'posts_per_page' => 10
	]);
	foreach ($products as &$p) {
		$p->thumbnail = get_the_post_thumbnail_url($p->ID);
	}
	return $products;
}





function my_cfs_options_screens($screens)
{
    $screens[] = array(
        'name'          => 'options',
        'menu_title'    => __('Cấu hình website'),
        'page_title'    => __('Cấu hình website'),
        'menu_position' => 100,
        'icon'          => 'dashicons-admin-generic', // optional, dashicons-admin-generic is the default
        'field_groups'  =>  array('Cài đặt chung', 'Cấu hình website'),
    );

    return $screens;
}
add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );

