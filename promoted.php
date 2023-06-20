<?php
/*
Plugin Name: WooCommerce Promoted Product
Description: This plugin allows you to promote a product on every page.
Version: 1.0
Author: Daniel Paz
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}



if (!class_exists('Promoted_Product_Display')) {
    class Promoted_Product_Display {
        public function __construct() {
            add_filter('woocommerce_get_settings_pages', array($this, 'add_settings_page'), 15);
            add_action('wp_head', array($this, 'display_promoted_product'));
        }

        public function add_settings_page($settings) {
            require_once 'includes/class-ppd-settings.php';
            $settings[] = new PPD_Settings();
            return $settings;
        }

        public function display_promoted_product() {
            $product_id = get_option('ppd_product_id', 0); // default to product ID 0 if not set
            $background_color = get_option('ppd_background_color', '#ffffff'); // default to white
            $text_color = get_option('ppd_text_color', '#000000'); // default to black
            $promoted_product_title = get_option('ppd_promoted_product_title', 'FLASH SALE:'); // default title
        
            $product = wc_get_product($product_id);
        
            // If product doesn't exist or isn't visible, don't display anything
            if (!$product || !$product->is_visible()) return;
        
            // Get product metadata
            $promote = get_post_meta($product_id, 'ppd_promote', true);
            $custom_title = get_post_meta($product_id, 'ppd_custom_title', true);
            $set_expiry = get_post_meta($product_id, 'ppd_set_expiry', true);
            $expiry_date = get_post_meta($product_id, 'ppd_expiry_date', true);
        
            // Apply validation
            $now = new DateTime();
            $expiry = DateTime::createFromFormat('Y-m-d H:i', $expiry_date);
            if ('yes' === $promote && (!$set_expiry || $now < $expiry)) {
                $display_title = empty($custom_title) ? $product->get_name() : $custom_title;
        
                echo "<div id='promoted-p' style='background-color: {$background_color}; color: {$text_color}; padding: 10px; position: fixed; bottom: 0; width: 100%; text-align: center;'>";
                echo "<h3>{$promoted_product_title}: <a href='" . get_permalink($product_id) . "' style='color: {$text_color};'>" . $display_title . "</a></h3>";
                echo "</div>";
            }
        }        
    }
    new Promoted_Product_Display();
}

// Include the product meta class 
require_once 'includes/class-ppd-product-meta.php';
function show_banner() {
    ?>
    <div id="banner" style="width: 100%; padding: 1rem; text-align: center; background-color: navy; color: white;">
        <p>
            Breaking News! This is the banner for the news part of the header!
        </p>
    </div>
    <?php
}

function front_page_banner() {
	if ( is_front_page() ) {	
		show_banner();
	}
}

add_action( 'woocommerce_before_main_content', 'show_banner', 1 );
add_action( 'loop_start', 'front_page_banner' );

