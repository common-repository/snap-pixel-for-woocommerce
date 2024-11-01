<?php
/**
 * Plugin initialization.
 *
 * @package snap-pixel-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initializes the plugin.
 */
function snap_pixel_for_woocommerce_init() {
}

add_action( 'init', 'snap_pixel_for_woocommerce_init' );

require dirname( __FILE__ ) . '/modules/snap-pixel/index.php';
