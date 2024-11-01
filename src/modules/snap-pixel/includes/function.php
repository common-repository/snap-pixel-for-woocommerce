<?php
// phpcs:ignoreFile

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('snap_pixel_functions')) {
	class snap_pixel_functions
	{
		public $plugin_name = '';
		public $pixel_id = '';

		// WordPress Events
		public $homepage = '';
		public $pages = '';
		public $posts = '';
		public $search = '';
		public $categories = '';
		public $tags = '';

		// WooCommerce Events
		public $addtocart_class = '';
		public $viewcart = '';
		public $checkout = '';
		public $paymentinfo = '';
		public $addtocart = '';
		public $purchase = '';

		public function __construct()
		{
			$snap_pixel_code = get_option('snap_pixel_code');

			$this->plugin_name = 'snap_pixel';
			$this->pixel_id = (isset($snap_pixel_code['pixel_id']) ? $snap_pixel_code['pixel_id'] : '');

			// WordPress Events
			$this->homepage = (isset($snap_pixel_code['homepage']) ? $snap_pixel_code['homepage'] : '');
			$this->pages = (isset($snap_pixel_code['pages']) ? $snap_pixel_code['pages'] : '');
			$this->posts = (isset($snap_pixel_code['posts']) ? $snap_pixel_code['posts'] : '');
			$this->search = (isset($snap_pixel_code['search']) ? $snap_pixel_code['search'] : '');
			$this->categories = (isset($snap_pixel_code['categories']) ? $snap_pixel_code['categories'] : '');
			$this->tags = (isset($snap_pixel_code['tags']) ? $snap_pixel_code['tags'] : '');

			// WooCommerce Events
			$this->addtocart_class = (isset($snap_pixel_code['addtocart_class']) ? $snap_pixel_code['addtocart_class'] : '');
			$this->viewcart = (isset($snap_pixel_code['viewcart']) ? $snap_pixel_code['viewcart'] : '');
			$this->checkout = (isset($snap_pixel_code['checkout']) ? $snap_pixel_code['checkout'] : '');
			$this->paymentinfo = (isset($snap_pixel_code['paymentinfo']) ? $snap_pixel_code['paymentinfo'] : '');
			$this->addtocart = (isset($snap_pixel_code['addtocart']) ? $snap_pixel_code['addtocart'] : '');
			$this->purchase = (isset($snap_pixel_code['purchase']) ? $snap_pixel_code['purchase'] : '');

			// Make sure add to cart event is fired on all pages
      if (!isset($_GET['wc-ajax'])) {
        add_action('woocommerce_add_to_cart', array($this, 'snap_pixel_code_add_to_cart'), 10, 6);
      }
		}

		public function get_user_email() {
			$current_user = wp_get_current_user();

			// not logged in
			if ($current_user->ID == 0) {
				return '';
			}

			return $current_user->user_email;
		}

		public function snap_pixel_woocommerce_exists()
		{
			return class_exists('woocommerce');
		}

		public function snap_pixel_code_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
		{
			if (!$this->snap_pixel_woocommerce_exists() || $this->addtocart !== 'checked') {
				return;
			}

			$term_list = wp_get_post_terms($product_id, 'product_cat', array(
				'fields' => 'ids',
			));

			$cat_id = (int) $term_list[0];
			$category = get_term($cat_id, 'product_cat');
			$product_category = $category->name;
			$_product = wc_get_product($product_id);
			$product_price = $_product->get_price();
			$product_currency = get_woocommerce_currency(); ?>
			<!-- ADD_CART Snap Pixel Event -->
			<script>
				setTimeout(function() {
					snaptr('track', 'ADD_CART', {
						'currency': '<?php echo esc_js($product_currency); ?>',
						'price': <?php echo esc_js($product_price); ?>,
						'item_category': '<?php echo esc_js($product_category); ?>',
						'item_ids': [
							'<?php echo esc_js($product_id); ?>'
						]
					});
				}, 1500);
			</script>
			<!-- /ADD_CART Snap Pixel Event -->
			<?php
		}

		public function snap_pixel_code_everywhere()
		{
			$user_email = esc_js($this->get_user_email());

			?>
			<!-- INIT Snap Pixel Event -->
			<script type="text/javascript">
				(function(win, doc, sdk_url){
					if (win.snaptr) {
						return;
					}

					var tr=win.snaptr=function() {
						tr.handleRequest ? tr.handleRequest.apply(tr, arguments):tr.queue.push(arguments);
					};
					tr.queue = [];
					var s='script';
					var new_script_section=doc.createElement(s);
					new_script_section.async=!0;
					new_script_section.src=sdk_url;
					var insert_pos=doc.getElementsByTagName(s)[0];
					insert_pos.parentNode.insertBefore(new_script_section, insert_pos);
				})(window, document, 'https://sc-static.net/scevent.min.js');

				snaptr('init', '<?php echo esc_js($this->pixel_id); ?>', {
					integration: 'woocommerce',
					<?php echo $user_email ? "'user_email': '{$user_email}'" : ''; ?>
				});

				snaptr('track', 'PAGE_VIEW');
			</script>
			<!-- /INIT Snap Pixel Event -->
			<?php
		}

		public function snap_pixel_code_checkout()
		{
			if (!$this->snap_pixel_woocommerce_exists()) {
				return;
			}

			global $woocommerce;

			$price = $woocommerce->cart->total;
			$product_currency = get_woocommerce_currency();
			$num_items = $woocommerce->cart->get_cart_contents_count();
			$product_ids = array();

			foreach (WC()->cart->get_cart() as $cart_item) {
				$product_ids[] = (string) $cart_item['product_id'];
			}

			$product_ids = json_encode($product_ids); ?>
			<!-- START_CHECKOUT Snap Pixel Event -->
			<script>
				snaptr('track', 'START_CHECKOUT', {
					'currency': '<?php echo esc_js($product_currency); ?>',
					'price': <?php echo esc_js($price); ?>,
					'number_items': '<?php echo esc_js($num_items); ?>',
					'item_ids': <?php echo $product_ids; ?>
				});
			</script>
			<!-- /START_CHECKOUT Snap Pixel Event -->
			<?php
		}

		public function snap_pixel_code_paymentinfo($order_id)
		{
			if (!$this->snap_pixel_woocommerce_exists()) {
				return;
			}

			global $woocommerce;

			$orders = new WC_Order($order_id);
			$order_shipping_total = $orders->get_total();

			$order = $orders->get_items();
			$content_ids = array();
			$num_items = 0;

			if (is_array($order)) {
				foreach ($order as $order_product_detail) {
					$num_items += (int) $order_product_detail['qty'];
					$content_ids[] = (string) $order_product_detail['product_id'];
				}
			}

			$content_ids = json_encode($content_ids);

			$product_currency = get_woocommerce_currency(); ?>
			<!-- PURCHASE Snap Pixel Event -->
			<script>
				snaptr('track', 'ADD_BILLING');

				snaptr('track', 'PURCHASE', {
					'item_ids': <?php echo $content_ids; ?>,
					'currency': '<?php echo esc_js($product_currency); ?>',
					'price': <?php echo esc_js($order_shipping_total); ?>,
					'number_items': '<?php echo esc_js($num_items); ?>',
					'transaction_id': '<?php echo esc_js($order_id); ?>'
				});
			</script>
			<!-- /PURCHASE Snap Pixel Event -->
			<?php
		}

		public function snap_pixel_code_addtocart_shop()
		{
			if (!$this->snap_pixel_woocommerce_exists()) {
				return;
			}

			$product_currency = get_woocommerce_currency(); ?>
			<!-- ADD_CART Snap Pixel Event -->
			<script>
				jQuery( 'body' ).on( 'added_to_cart', function(e, h, w, button) {
					var product_id = button.data("product_id") || button.val();
					snaptr('track', 'ADD_CART', {
					  'currency': '<?php echo esc_js($product_currency); ?>',
						'price': '',
						'item_category': '',
						'item_ids': [
						  product_id
						]
					});
				});
			</script>
			<!-- /ADD_CART Snap Pixel Event -->
			<?php
		}

		public function snap_pixel_code_viewcontent()
		{
			if (!$this->snap_pixel_woocommerce_exists()) {
				return;
			} ?>
			<!-- VIEW_CONTENT Snap Pixel Event -->
			<script>
				snaptr('track', 'VIEW_CONTENT', {
					item_ids: [ <?php echo wc_get_product()->id; ?> ]
				});
			</script>
			<!-- /VIEW_CONTENT Snap Pixel Event -->
			<?php
		}

		public function snap_pixel_code_search()
		{
			$search_string = get_search_query(false);
			$search_string = esc_js($search_string);

			?>
			<!-- SEARCH Snap Pixel Event -->
			<script>
				snaptr('track', 'SEARCH', {
					'search_string': "<?php echo $search_string; ?>"
				});
			</script>
			<!-- /SEARCH Snap Pixel Event -->
			<?php
		}
	}
}
