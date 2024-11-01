<?php
// phpcs:ignoreFile

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$snap_pixel_code = get_option('snap_pixel_code');
?>

<div class="wrap snap-pixel-wrapper">

    <form method="post" action="">
        <div class="css-15gqi4y sds-container">

            <div class="sds-card-header css-w5i3kt">
                <div class="css-3hb53f sds-card-title">
                    <input name="snap_pixel_csrf" type="hidden"
                        value="<?php echo wp_create_nonce('snap_pixel_csrf'); ?>" />
                    <h2><?php echo __('Snap Pixel Settings', $this->plugin_name); ?></h2>
                </div>
            </div>


            <table class="form-table" role="presentation">

                <tbody>
                    <tr>
                        <div class="sds-control css-wafs">
                          <b>Track conversions with the Snap Pixel Plugin for WordPress and WooCommerce</b>
                          <br />
                          The Snap Pixel measures the impact of your campaigns across devices, so you can see how many Snapchatters take action on your website after viewing or clicking on your ads.
                          By connecting the Snap Pixel Plugin to your WordPress or WooCommerce site, the Snap Pixel will be deployed to your website and begin tracking the actions users take on your website. This means your users' data, including hashed email address and hashed phone number, will be passed to Snap when available and entered on your site.
                          By clicking “Connect” below, you acknowledge that this data is Event Data as described in the <a
                          href=https://businesshelp.snapchat.com/en-US/a/snap-conversion-terms>Snap Conversion
                          Terms</a>. If you would like to omit certain event types or parameters, you should implement the Snap Pixel javascript code directly, rather than using this Plugin.
                        </div>
                    </tr>

                    <tr>    
                        <td>

                                <div class="sds-control sds-text-area css-w51az">
                                    <label class="css-5wvd91 sds-label sds-control-label">
                                        <?php echo __('Pixel ID', $this->plugin_name); ?>
                                    </label>
                                    <div class="sds-input-base">
                                        <input type="text" name="snap_pixel_code[pixel_id]" class="ant-input"
                                            value="<?php echo (isset($snap_pixel_code['pixel_id']) ? esc_attr($snap_pixel_code['pixel_id']) : ''); ?>">
                                        <span class="border-anchor"></span>
                                    </div>
                                </div>
                                <div class="hidden-checkboxes">
                                    <table class="" role="presentation">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div >
                                                        <span id="snap_pixel_places_woocommerce_cart_wrapper"
                                                            class="rc-checkbox sds-checkbox">
                                                            <input name="snap_pixel_code[viewcart]"
                                                                id="snap_pixel_places_woocommerce_cart" type="checkbox"
                                                                class="rc-checkbox-input"
                                                                checked="checked"
                                                                value="checked">
                                                            <span class="rc-checkbox-inner"></span>
                                                        </span>
                                                        <label for="snap_pixel_places_woocommerce_cart"
                                                            class="css-5wvd91 sds-label sds-label"><?php echo __('View Content', $this->plugin_name); ?></label>
                                                    </div>
                                                    <div >
                                                        <span id="snap_pixel_places_woocommerce_addtocart_wrapper"
                                                            class="rc-checkbox sds-checkbox">
                                                            <input name="snap_pixel_code[addtocart]"
                                                                id="snap_pixel_places_woocommerce_addtocart"
                                                                type="checkbox" class="rc-checkbox-input"
                                                                checked="checked"
                                                                value="checked">
                                                            <span class="rc-checkbox-inner"></span>
                                                        </span>
                                                        <label for="snap_pixel_places_woocommerce_addtocart"
                                                            class="css-5wvd91 sds-label sds-label"><?php echo __('Add To Cart', $this->plugin_name); ?></label>
                                                    </div>
                                                    <div >
                                                        <span id="snap_pixel_places_woocommerce_checkout_wrapper"
                                                            class="rc-checkbox sds-checkbox">
                                                            <input name="snap_pixel_code[checkout]"
                                                                id="snap_pixel_places_woocommerce_checkout"
                                                                type="checkbox" class="rc-checkbox-input"
                                                                checked="checked"
                                                                value="checked">
                                                            <span class="rc-checkbox-inner"></span>
                                                        </span>
                                                        <label for="snap_pixel_places_woocommerce_checkout"
                                                            class="css-5wvd91 sds-label sds-label"><?php echo __('Start Checkout', $this->plugin_name); ?></label>
                                                    </div>
                                                    <div >
                                                        <span id="snap_pixel_places_woocommerce_paymentinfo_wrapper"
                                                            class="rc-checkbox sds-checkbox">
                                                            <input name="snap_pixel_code[paymentinfo]"
                                                                id="snap_pixel_places_woocommerce_paymentinfo"
                                                                type="checkbox" class="rc-checkbox-input"
                                                                checked="checked"
                                                                value="checked">
                                                            <span class="rc-checkbox-inner"></span>
                                                        </span>
                                                        <label for="snap_pixel_places_woocommerce_paymentinfo"
                                                            class="css-5wvd91 sds-label sds-label"><?php echo __('Purchase', $this->plugin_name); ?></label>
                                                    </div>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="css-9udrzz">
                                <div class="">
                                    <p class="submit">
                                        <button data-testid="sds-button-story" type="submit"
                                            class="ant-btn sds-button regular sds-base-button default"
                                            name="save_snap_pixel"
                                            value="<?php echo __('Connect', $this->plugin_name); ?>"><?php echo __('Save Changes', $this->plugin_name); ?></button>
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>



        </div>

        <table class="form-table woocommerce_events" role="presentation">
            <tbody>
                <tr>
                    <td>
                        <input type="checkbox" name="snap_pixel_code[homepage]" value="checked"
                            id="snap_pixel_code_homepage" checked="checked">
                        <label
                            for="snap_pixel_code_homepage"><?php echo __('Front Page', $this->plugin_name); ?></label><br>
                        <input type="checkbox" name="snap_pixel_code[pages]" value="checked" id="snap_pixel_code_pages"
                            checked="checked">
                        <label for="snap_pixel_code_pages"><?php echo __('Pages', $this->plugin_name); ?></label><br>
                        <input type="checkbox" name="snap_pixel_code[posts]" value="checked" id="snap_pixel_code_posts"
                            checked="checked">
                        <label for="snap_pixel_code_posts"><?php echo __('Posts', $this->plugin_name); ?></label><br>
                        <input type="checkbox" name="snap_pixel_code[search]" value="checked"
                            id="snap_pixel_code_search" checked="checked">
                        <label
                            for="snap_pixel_code_search"><?php echo __('Search Results', $this->plugin_name); ?></label><br>
                        <input type="checkbox" name="snap_pixel_code[categories]" value="checked"
                            id="snap_pixel_code_categories" checked="checked">
                        <label
                            for="snap_pixel_code_categories"><?php echo __('Categories', $this->plugin_name); ?></label><br>
                        <input type="checkbox" name="snap_pixel_code[tags]" value="checked" id="snap_pixel_code_tags"
                            checked="checked">
                        <label
                            for="snap_pixel_code_tags"><?php echo __('Tags', $this->plugin_name); ?></label><br><br />
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="css-15gqi4y sds-container">

            <div class="sds-card-header css-w5i3kt">
                <div class="css-3hb53f sds-card-title">
                    <h2>Frequently Asked Questions</h2>
                </div>
            </div>

            <div class="css-1f5pm2f sds-card-body">
                <div class="sds-control css-wafs">
                    <h4>What data is passed via the Snap Pixel Plugin?</h4>
                    <p>We automatically deploy our standard recommended events, including: </p>
                    <ul class="bullets">
                      <li>Event Types: Page View, View Content, Add to Cart, Purchase</li>
                      <li>Event Variables: Item Ids, Price, Currency, Transaction ID</li>
                      <li>User Data: Hashed email address, hashed phone number, ip address and user agent</li>
                    </ul>

                    <h4>How can I customize which events are passed?</h4>
                    <p>
                      Our Plugin comes pre-built with standard events and is not customizable.
                      We recommend deploying the Snap Pixel javascript directly on your site.
                      You can follow these instructions <a href="https://businesshelp.snapchat.com/en-US/a/pixel-website-install">here</a>.
                    </p>

                    <h4>How can I confirm my pixel is firing properly? </h4>
                    <p>Download our <a href="https://businesshelp.snapchat.com/en-US/article/chrome-helper">Snap Pixel helper</a>, a Chrome extension that allows you to verify and troubleshoot your Snap Pixel integration.</p>

                    <h4>How do you connect the Pixel events to a Product Catalog?</h4>
                    <ul class="bullets">
                      <li>Visit your Snapchat Ads Manager and navigate to the ‘Catalogs’ tab. Learn more about creating your Catalog <a href="https://businesshelp.snapchat.com/en-US/article/create-catalog">here</a>.</li>
                    </ul>
                    <h4>If I use the Snap Pixel Plugin, will I be able to run Dynamic Ads?</h4>
                    <ul class="bullets">
                      <li>
                        Yes, all of the required events and parameters will be setup through our Snap Pixel Plugin.
                        However, you will still need to sync your Catalog and ensure the Item IDs in your Catalog match what’s being passed through Pixel.
                        You can learn more about Dynamic Ads requirements <a href="https://businesshelp.snapchat.com/en-US/article/snap-pixel-about">here</a>.
                      </li>
                    </ul>

                    <h4>Where can I learn more about Snap Pixel?</h4>
                    <ul class="bullets">
                      <li>Learn about Snap’s measurement capabilities <a href="https://forbusiness.snapchat.com/advertising/measurement">here</a>.</li>
                      <li>Visit our Business Help Center for <a href="https://businesshelp.snapchat.com/en-US/a/snap-pixel-faq">Pixel FAQs</a>, <a href="https://businesshelp.snapchat.com/en-US/a/pixel-custom-audience">Pixel Custom Audiences</a>, and more.</li>
                      <li>For specific questions, please reach out via our Chat support in Ads Manager. Instructions can be found <a href="https://businesshelp.snapchat.com/en-US/a/chat">here</a>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>
