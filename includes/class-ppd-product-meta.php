<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PPD_Product_Meta')) {
    class PPD_Product_Meta {
        public function __construct() {
            add_action('woocommerce_product_options_general_product_data', array($this, 'add_fields'));
            add_action('woocommerce_process_product_meta', array($this, 'save_fields'), 10, 2);
        }

        public function add_fields() {
            echo '<div class="options_group">';

            woocommerce_wp_checkbox(array(
                'id' => 'ppd_promote',
                'wrapper_class' => 'show_if_simple',
                'label' => __('Promote this product?', 'woocommerce'),
                'description' => __('Check this if you want to promote this product.', 'woocommerce'),
                'default' => '0',
                'desc_tip' => true,
            ));

            woocommerce_wp_text_input(array(
                'id' => 'ppd_custom_title',
                'label' => __('Custom Title', 'woocommerce'),
                'description' => __('Enter a custom title to be shown instead of the product title.', 'woocommerce'),
                'desc_tip' => true,
            ));

            woocommerce_wp_checkbox(array(
                'id' => 'ppd_set_expiry',
                'wrapper_class' => 'show_if_simple',
                'label' => __('Set Expiration Date?', 'woocommerce'),
                'description' => __('Check this if you want to set an expiration date for the promotion.', 'woocommerce'),
                'default' => '0',
                'desc_tip' => true,
            ));

            woocommerce_wp_text_input(array(
                'id' => 'ppd_expiry_date',
                'wrapper_class' => 'show_if_simple',
                'label' => __('Expiration Date', 'woocommerce'),
                'description' => __('Enter the expiration date in the format: YYYY-MM-DD HH:MM.', 'woocommerce'),
                'desc_tip' => true,
                'placeholder' => 'YYYY-MM-DD HH:MM',
            ));

            echo '</div>';
        }

        public function save_fields($post_id, $post) {
            update_post_meta($post_id, 'ppd_promote', isset($_POST['ppd_promote']) ? 'yes' : 'no');
            update_post_meta($post_id, 'ppd_custom_title', sanitize_text_field($_POST['ppd_custom_title']));
            update_post_meta($post_id, 'ppd_set_expiry', isset($_POST['ppd_set_expiry']) ? 'yes' : 'no');
            update_post_meta($post_id, 'ppd_expiry_date', sanitize_text_field($_POST['ppd_expiry_date']));
        }
    }
    new PPD_Product_Meta();
}
