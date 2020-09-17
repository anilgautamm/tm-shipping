<?php

/**
 * Plugin Name: TM Shipping Checkout
 * Plugin URI: 
 * Description: This plugin modify the shipping options on checkout page
 * Version: 1.1.0
 * Author: Manish
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!function_exists('pr')) {

    function pr($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

}

//hook fire on plugin activation
register_activation_hook(__FILE__, 'tm_shipping_checkout_activation');

function tm_shipping_checkout_activation() {
    //check if in8sync plugin is installed and activated
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        //deactivate plugin if parent in8sync is not activated
        deactivate_plugins(__FILE__);
        echo '<div id="error" class="error notice is-dismissible"><p>Plugin can\'t be activated.This plugin depends on WooCommerce.</div>';
    }

}

register_deactivation_hook(__FILE__, 'tm_shipping_checkout_deactivation');

function tm_shipping_checkout_deactivation() {

}

//plugins load hook
add_action('plugins_loaded', 'init_tm_shipping_checkout_plugin', 10);

function init_tm_shipping_checkout_plugin() {
    //define plugin URI and PATH
    define('TM_SHIPPING_CHECKOUT_URL', plugin_dir_url(__FILE__));
    define('TM_SHIPPING_CHECKOUT_PATH', plugin_dir_path(__FILE__));
    
    define('ASSET_VERSION', '3.0');

     require_once TM_SHIPPING_CHECKOUT_PATH . '/inc/functions.php';

}