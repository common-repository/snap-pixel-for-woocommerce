<?php
// phpcs:ignoreFile

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $query, $post;

$snap_pixel_code = get_option('snap_pixel_code');

// WordPress Events
$homepage = (isset($snap_pixel_code['homepage']) ? $snap_pixel_code['homepage'] : '');
$pages = (isset($snap_pixel_code['pages']) ? $snap_pixel_code['pages'] : '');
$posts = (isset($snap_pixel_code['posts']) ? $snap_pixel_code['posts'] : '');
$search = (isset($snap_pixel_code['search']) ? $snap_pixel_code['search'] : '');
$categories = (isset($snap_pixel_code['categories']) ? $snap_pixel_code['categories'] : '');
$tags = (isset($snap_pixel_code['tags']) ? $snap_pixel_code['tags'] : '');

// WooCommerce Events
$addtocart_class = (isset($snap_pixel_code['addtocart_class']) ? $snap_pixel_code['addtocart_class'] : '');
$viewcart = (isset($snap_pixel_code['viewcart']) ? $snap_pixel_code['viewcart'] : '');
$checkout = (isset($snap_pixel_code['checkout']) ? $snap_pixel_code['checkout'] : '');
$paymentinfo = (isset($snap_pixel_code['paymentinfo']) ? $snap_pixel_code['paymentinfo'] : '');
$addtocart = (isset($snap_pixel_code['addtocart']) ? $snap_pixel_code['addtocart'] : '');

$snap_pixel = new snap_pixel_functions();

add_action('wp_footer', array($snap_pixel, 'snap_pixel_code_addtocart_shop'), 2);
// Front Page
if (is_home() || is_front_page()) {
	if ($homepage === 'checked') {
		add_action('wp_head', array($snap_pixel, 'snap_pixel_code_everywhere'), 2);
	}

// Other Pages
} elseif (is_page() || is_page_template()) {
	$page_slug = $post->post_name;

	if ($pages === 'checked') {
		add_action('wp_head', array($snap_pixel, 'snap_pixel_code_everywhere'), 2);

		if ($page_slug === 'checkout') {
			// Checkout Page
			if ($checkout === 'checked' && !isset($_GET['key'])) {
				add_action('wp_footer', array($snap_pixel, 'snap_pixel_code_checkout'));

			// Order Received Page
			} elseif ($paymentinfo === 'checked' && is_wc_endpoint_url('order-received')) {
				add_action('woocommerce_thankyou', array($snap_pixel, 'snap_pixel_code_paymentinfo'));
			}
		}
	}

	// Any Shopping Page
} elseif (function_exists('is_shop') && is_shop()) {
	if ($addtocart === 'checked') {
		add_action('wp_head', array($snap_pixel, 'snap_pixel_code_everywhere'), 2);
		add_action('wp_footer', array($snap_pixel, 'snap_pixel_code_addtocart_shop'), 2);
	}

	// Detail Pages
} elseif (is_single()) {
	// Single Post (Detail Page)
	if ($posts === 'checked') {
		add_action('wp_head', array($snap_pixel, 'snap_pixel_code_everywhere'), 2);
	}

	// Add To Cart Event
	if (!is_singular('post')) {
		if ($addtocart === 'checked') {
			add_action('woocommerce_add_to_cart', array($snap_pixel, 'snap_pixel_code_add_to_cart'), 10, 6);
		}
	}

	// Product Detail Page
	if (function_exists('is_product') && is_product()) {
		if ($viewcart === 'checked') {
			add_action('wp_footer', array($snap_pixel, 'snap_pixel_code_viewcontent'));
		}
	}

	// Search Page
} elseif (is_search()) {
	if ($search === 'checked') {
		add_action('wp_head', array($snap_pixel, 'snap_pixel_code_everywhere'), 2);
		add_action('wp_footer', array($snap_pixel, 'snap_pixel_code_search'), 2);
	}

	// Category List Page
} elseif (is_category()) {
	if ($categories === 'checked') {
		add_action('wp_head', array($snap_pixel, 'snap_pixel_code_everywhere'), 2);
	}

	// Tag Page
} elseif (is_tag()) {
	if ($tags === 'checked') {
		add_action('wp_head', array($snap_pixel, 'snap_pixel_code_everywhere'), 2);
	}
}
