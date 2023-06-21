<?php
/*
 * Plugin Name: WooCommerce Promoted Product
 * Description: This plugin allows you to promote a product on "every" page.
 * Version: 1.0
 * Author: Daniel Paz
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


if (!class_exists('Promoted_Product_D')) {
    class Promoted_Product_D {
        public function __construct() {
            add_filter('woocommerce_get_settings_pages', array($this, 'add_settings_page'), 15);
        }

        public function add_settings_page($settings) {
            require_once 'includes/class-ppd-settings.php';
            $settings[] = new PPD_Settings();
            return $settings;
        }
        
        public function show_banner() {
            $product_id = get_option('ppd_current', 0); 
            if (!$product_id == 0) {
                $product = $this->get_product($product_id);
                if ($product && $this->is_promotion_active($product_id)) {
                    $this->display_banner($product);
                }
            }
        }
    
        private function get_product($product_id) {
            return wc_get_product($product_id);
        }
    
        private function is_promotion_active($product_id) {
            $set_expiry = get_post_meta($product_id, 'ppd_set_expiry');
            $expiry_date = get_post_meta($product_id, 'ppd_expiry_date');
    
            if($set_expiry[0] == 'yes'){
                $now = new DateTime();
                $expiry = DateTime::createFromFormat('Y-m-d H:i', $expiry_date[0]);
                if ($now > $expiry) {
                    update_post_meta($product_id, 'ppd_promote', 'no');
                    update_post_meta($product_id, 'ppd_set_expiry', 'no');
                    return false;
                }
            }
            return true;
        }
    
        private function display_banner($product) {
            $background_color = get_option('ppd_background_color'); 
            $text_color = get_option('ppd_text_color'); 
            $promoted_product_title = get_option('ppd_promoted_product_title', ''); 
            $product_id = $product->get_id();
            $custom_title = get_post_meta($product_id, 'ppd_custom_title');
            $custom_title = $custom_title[0] == '' ? $product->get_title() : $custom_title[0];
    
            $link = get_permalink($product_id);
            if(is_admin()){
                $link = get_admin_url() . 'post.php?post=' . $product_id . '&action=edit';
            }
    
            echo "<div id='banner' style='width: 100%; padding: 1rem; text-align: center; background-color: {$background_color}; color: {$text_color};'>";
            echo "<h3 style='color: {$text_color} !important;'>{$promoted_product_title} <a href='" . $link . "' style='color: {$text_color} !important;'>" . $custom_title . "</a></h3>";
            echo "</div>";
        }
        public function front_page_banner() {
            if (is_front_page()) {
                $this->show_banner();
            }
        }

        public function find_current_promoted() {
            global $wpdb;
            
            $query = "
                SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key = 'ppd_hidden_date'
                ORDER BY meta_value DESC
                LIMIT 1;
            ";
            
            $result = $wpdb->get_var($query);
            
            if ($result !== null) {
                update_option('ppd_current', $result);
            } else {
                update_option('ppd_current', 0);
            }
        }
    }
}

require_once 'includes/class-ppd-product-meta.php';

global $promoted_product_d;
$promoted_product_d = new Promoted_Product_D();
$promoted_product_d->find_current_promoted();

add_action('woocommerce_before_main_content', array($promoted_product_d, 'show_banner'), 1);
add_action('loop_start', array($promoted_product_d, 'front_page_banner'));

