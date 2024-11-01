<?php
/**
 * Plugin Name: Snap Pixel
 * Plugin URI:  https://wordpress.org/plugins/snap-pixel
 * Description: Reach an engaged audience with Snapchat Ads for Business, an advertising platform for achieving your business goals
 * Version:     1.0.0
 * Author:      Snapchat
 * Author URI:  https://snapchat.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: snap_pixel.
 */
// phpcs:ignoreFile

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('snap_pixel')) {
	class snap_pixel
	{
		public $plugin_name = '';

		public function __construct()
		{
			$this->plugin_name = 'snap_pixel';

			// Add link after 'Media'
			add_action('admin_menu', array($this, 'snap_pixel_menu'));

			// Add link after 'Media'
			add_action('template_redirect', array($this, 'snap_pixel_place_code'));

			// Admin notice for snap pixel id
			add_action('admin_notices', array($this, 'snap_pixel_checks'));

			// Setting links on plugin page
			add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'snap_pixel_settings_link'));

			// Snap pixel about link on plugin row meta
			add_filter('plugin_row_meta', array(__CLASS__, 'snap_pixel_row_meta'), 10, 2);

			// Enqueue for the admin section styles and javascript
			add_action('admin_enqueue_scripts', array($this, 'admin_style_scripts'));

			// Language support
			add_action('plugins_loaded', array($this, 'snap_pixel_plugin_textdomain'));

			// Activation Hook
			register_activation_hook(dirname(__FILE__, 4).'/snap-pixel-for-woocommerce.php', array($this, 'snap_pixel_activate'));

			// Pixel Injection Functions
			include_once('includes/function.php');

			$snap_pixel = new snap_pixel_functions();
		}

		/**
		 * Set Plugin row meta.
		 *
		 * @param mixed $links
		 * @param mixed $file
		 *
		 * @return array
		 */
		public static function snap_pixel_row_meta($links, $file)
		{
			if (plugin_basename(__FILE__) === $file) {
				$row_meta = array(
					'docs'    => '<a href="'.esc_url('https://businesshelp.snapchat.com/en-US/article/snap-pixel-about').'" aria-label="'.esc_attr__('About Snap Pixel', 'snap_pixel').'" title="'.esc_attr__('About Snap Pixel', 'snap_pixel').'">'.esc_html__('About Snap Pixel', 'snap_pixel').'</a>',
				);

				return array_merge($links, $row_meta);
			}

			return (array) $links;
		}

		/**
		 * Set setting link on plugin page.
		 *
		 * @param mixed $links
		 *
		 * @return array
		 */
		public function snap_pixel_settings_link($links)
		{
			$links[] = '<a href="' . admin_url('admin.php?page=snap-pixel') . '">' . __('Settings') . '</a>';

			return $links;
		}

		/**
		 * Set admin notice for snap pixel id.
		 *
		 * @return string
		 */
		public function snap_pixel_checks()
		{
			$snap_pixel_code = get_option('snap_pixel_code');
			$pixel_id = (isset($snap_pixel_code['pixel_id']) ? $snap_pixel_code['pixel_id'] : '');
			if (!$pixel_id) {
				echo $this->get_message_html(
					sprintf(
						__(
							'%1$sSnap Pixel for WordPress is almost ready.%2$s To complete your configuration, add the %3$s Snap Pixel ID%4$s.',
							$this->plugin_name
						),
						'<strong>',
						'</strong>',
						'<a href="'.esc_url(admin_url('admin.php?page=snap-pixel')).'">',
						'</a>'
					),
					'info'
				);
			}
		}

		/**
		 * Get message
		 *
		 * @param mixed $message
		 * @param mixed $type
		 *
		 * @return string Error
		 */
		public function get_message_html($message, $type = 'error')
		{
			ob_start(); ?>
			<div class="notice is-dismissible notice-<?php echo $type; ?>">
				<p><?php echo $message; ?></p>
			</div>
			<?php

			return ob_get_clean();
		}

		/**
		 * Plugin language support
		 */
		public function snap_pixel_plugin_textdomain()
		{
			$plugin_rel_path = basename(dirname(__FILE__)).'/languages';
			load_plugin_textdomain('snap_pixel', false, $plugin_rel_path);
		}

		/**
		 * Conditions to place pixel code
		 *
		 * @return none
		 */
		public function snap_pixel_place_code()
		{
			include_once('admin/snap_pixel_place_code.php');
		}

		/**
		 * Admin Menu
		 *
		 * @return none
		 */
		public function snap_pixel_menu()
		{
			add_menu_page(__('Snap Pixel', $this->plugin_name), __('Snap Pixel', $this->plugin_name), 'manage_options', 'snap-pixel', array($this, 'snap_pixel_backend'), plugin_dir_url(__FILE__).'assets/images/snap-menuicon.png');
		}

		/**
		 * Save Settings.
		 *
		 * @return none
		 */
		public function snap_pixel_backend()
		{
			if (isset($_POST['save_snap_pixel'])) {
				if (!isset($_POST['snap_pixel_csrf'])) {
					die("Missing required required parameters to update snap pixel code");
				}

				if (!wp_verify_nonce($_POST['snap_pixel_csrf'],'snap_pixel_csrf')) {
					die("Unable to validate form credentials.");
				}
				// overwrite tracking settings so they're always on
				$_POST['snap_pixel_code']['viewcart'] = 'checked';
				$_POST['snap_pixel_code']['addtocart'] = 'checked';
				$_POST['snap_pixel_code']['checkout'] = 'checked';
				$_POST['snap_pixel_code']['paymentinfo'] = 'checked';
				$_POST['snap_pixel_code']['homepage'] = 'checked';
				$_POST['snap_pixel_code']['pages'] = 'checked';
				$_POST['snap_pixel_code']['posts'] = 'checked';
				$_POST['snap_pixel_code']['search'] = 'checked';
				$_POST['snap_pixel_code']['categories'] = 'checked';
				$_POST['snap_pixel_code']['tags'] = 'checked';
				$snap_pixel_code = isset($_POST['snap_pixel_code']) ? wp_unslash($_POST['snap_pixel_code']) : '';
				
				update_option('snap_pixel_code', $snap_pixel_code);
			}
			
			ob_start();
			include_once('admin/snap_pixel_backend.php');
			$content = ob_get_clean();

			echo $content;
		}

		/**
		 * Load Backend Admin CSS
		 *
		 * @param mixed $page
		 */
		public function admin_style_scripts($page)
		{
			wp_enqueue_style('snap-pixel-admin-style', plugin_dir_url(__FILE__).'assets/css/snap-pixel-admin.css');
			wp_enqueue_style('snap-pixel-admin-font', 'https://snap-design-system.storage.googleapis.com/fonts/fonts.css');
		}

		/**
		 * Activate plugin hook
		 */
		public function snap_pixel_activate()
		{
			$snap_pixel_code = array(
				'homepage'    => 'checked',
				'pages'       => 'checked',
				'posts'       => 'checked',
				'search'      => 'checked',
				'categories'  => 'checked',
				'tags'        => 'checked',
				'viewcart'    => 'checked',
				'checkout'    => 'checked',
				'paymentinfo' => 'checked',
				'addtocart'   => 'checked',
			);

			update_option('snap_pixel_code', $snap_pixel_code);
		}
	}

	$snap_pixel = new snap_pixel();
}
