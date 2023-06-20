<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PPD_Settings')) {
    class PPD_Settings extends WC_Settings_Page {
        public function __construct() {
            $this->id    = 'promoted_product';
            $this->label = __('Promoted Product', 'woocommerce');

            add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
            add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
        }

        public function get_settings() {
            $settings = array(
                'section_title' => array(
                    'name'     => __('Promoted Product Settings', 'woocommerce'),
                    'type'     => 'title',
                    'desc'     => '',
                    'id'       => 'wc_' . $this->id . '_section_title'
                ),
                'promoted_product_title' => array(
                    'name' => __('Title', 'woocommerce'),
                    'type' => 'text',
                    'desc' => __('Title for the promoted product.', 'woocommerce'),
                    'id'   => 'ppd_promoted_product_title',
                    'css'  => 'min-width:300px;',
                    'default' => 'FLASH SALE:'
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
                'product_id' => array(
                    'name' => __('Promoted Product', 'woocommerce'),
                    'type' => 'single_select_product',
                    'desc' => __('Choose the product to promote.', 'woocommerce'),
                    'id'   => 'ppd_product_id',
                    'css'  => 'min-width:300px;',
                    'desc_tip' => true
                ),
                'section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wc_' . $this->id . '_section_end'
                )
            );

            return apply_filters('wc_' . $this->id . '_settings', $settings);
        }
    }
}
