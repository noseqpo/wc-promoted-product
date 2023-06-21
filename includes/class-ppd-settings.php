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
                'section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wc_' . $this->id . '_section_end'
                )
            );

            return apply_filters('wc_' . $this->id . '_settings', $settings);
        }

        /**
         * Display the banner on the settings page. 
         *
         * @return void
         */
        public function output_html() {
            global $promoted_product_d;
            $promoted_product_d->show_banner();
        }

        public function output() {
            $settings = $this->get_settings(  );
            WC_Admin_Settings::output_fields( $settings );
            $this->output_html();
        }

        public function save() {
            $settings = $this->get_settings(  );
            WC_Admin_Settings::save_fields( $settings );
        }   
    }
}