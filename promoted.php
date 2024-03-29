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
            add_filter('woocommerce_product_settings', array($this, 'add_product_settings'));
            add_filter('woocommerce_product_settings', array($this, 'ppd_show_banner'));
        }

        /** 
         *  Shows the banner when saved.
         *  A visual bug may occur on some themes. Still looking for the cause. 
         *    Is not the wp_head hook.
         */
        public function ppd_show_banner($settings){
            global $promoted_product_d;
            $promoted_product_d->show_banner();
            return $settings;
        }
    
        public function add_product_settings($settings) {
            $my_settings = array(
                'section_title' => array(
                    'name'     => __('Promoted Product Settings', 'woocommerce'),
                    'type'     => 'title',
                    'desc'     => '',
                    'id'       => 'wc_ppd_section_title'
                ),
                'promoted_product_title' => array(
                    'name' => __('Title', 'woocommerce'),
                    'type' => 'text',
                    'desc' => __('Title for the promoted product.', 'woocommerce'),
                    'id'   => 'ppd_promoted_product_title',
                    'css'  => 'min-width:300px;',
                    'default' => '',
                    'placeholder' => __('Tittle for banner.', 'woocommerce'),
                ),
                'background_color' => array(
                    'name' => __('Background Color', 'woocommerce'),
                    'type' => 'color',
                    'desc' => __('Choose the background color for the promoted product display.', 'woocommerce'),
                    'id'   => 'ppd_background_color',
                    'css'  => 'width:6em;',
                    'default' => '#f00'
                ),
                'text_color' => array(
                    'name' => __('Text Color', 'woocommerce'),
                    'type' => 'color',
                    'desc' => __('Choose the text color for the promoted product display.', 'woocommerce'),
                    'id'   => 'ppd_text_color',
                    'css'  => 'width:6em;',
                    'default' => '#fff'
                ),
                array(
                    'type' => 'sectionend',
                    'id' => 'custom_product_settings_section_end'
                )
            );
  
            return array_merge($my_settings, $settings);
        }
        
        /**
         * Calls the function that generates the HTML with the correspondet information when setted.
         *
         * @return void
         */
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
        
        /**
         * Controls the expiration of a given promotion. Check if the expiration date is set, or if the promotion has expired.
         *
         * @param int $product_id
         * @return boolean
         */
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
        
        /**
         * Retrieves the necesary information for generating the banner. Sets the correspondent link depending on current page. Sets the correspondent position with JS.
         *
         * @param int $product
         * @return void
         */
        private function display_banner($product) {
            $background_color = esc_attr(get_option('ppd_background_color')); 
            $text_color = esc_attr(get_option('ppd_text_color')); 
            $promoted_product_title = get_option('ppd_promoted_product_title', ''); 
            $product_id = $product->get_id();
            $custom_title = get_post_meta($product_id, 'ppd_custom_title');
            $custom_title = esc_html($custom_title[0] == '' ? $product->get_title() : $custom_title[0]);
        
            $link = esc_url(get_permalink($product_id));
            if(is_admin()){
                $link = get_admin_url() . 'post.php?post=' . $product_id . '&action=edit';
            }
            
            wp_enqueue_script('banner-script', plugins_url('/js/positions.js', __FILE__), array(), '1.0.0', true);
        
            echo "<div id='banner-ppd' style='width: 100%; padding: 1rem; text-align: center; background-color: {$background_color}; color: {$text_color};'>";
            echo "<h3 style='color: {$text_color} !important;'>{$promoted_product_title} <a href='" . $link . "' style='color: {$text_color} !important;'>" . $custom_title . "</a></h3>";
            echo "</div>";
        }
        
        /**
         * SQL search for the lasted promotion added to the database, using the 'ppd_hidden_date' meta_key. 
         * Sets a transient option for resource saving.
         *
         * @return void
         */
        public function find_current_promoted() {
            global $wpdb;
            
            $result = get_transient('ppd_current_promoted');
        
            if ($result === false) {
                $query = "
                    SELECT post_id
                    FROM {$wpdb->postmeta}
                    WHERE meta_key = 'ppd_hidden_date'
                    ORDER BY meta_value DESC
                    LIMIT 1;
                ";
                
                $result = $wpdb->get_var($query);
                
                set_transient('ppd_current_promoted', $result, 12 * HOUR_IN_SECONDS);
            }
        
            $current = get_option('ppd_current');

            if ($current !== $result) {
                update_option('ppd_current', $result !== null ? $result : 0);
            }
        }
    }
}

require_once 'includes/class-ppd-product-meta.php';

global $promoted_product_d;
$promoted_product_d = new Promoted_Product_D();
$promoted_product_d->find_current_promoted();

add_action('wp_head', array($promoted_product_d, 'show_banner'), 1);
