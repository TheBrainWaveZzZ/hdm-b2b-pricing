<?php
/**
 * Plugin Name: HDM B2B Pricing Prototype
 * Description: Scope 1/2 - Custom B2B pricing prototype for WooCommerce.
 * Version: 0.2.1
 * Author: HDM
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin paths
 */
define('HDM_B2B_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('HDM_B2B_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Create B2B customer role on plugin activation
 */
register_activation_hook(__FILE__, function () {

    add_role(
        'b2b_customer',
        'B2B Customer',
        [
            'read' => true,
        ]
    );
});

/**
 * Include plugin files
 */
require_once HDM_B2B_PLUGIN_PATH . 'includes/roles.php';
require_once HDM_B2B_PLUGIN_PATH . 'includes/assets.php';
require_once HDM_B2B_PLUGIN_PATH . 'includes/product-fields.php';
require_once HDM_B2B_PLUGIN_PATH . 'includes/pricing-engine.php';
require_once HDM_B2B_PLUGIN_PATH . 'includes/frontend-shortcodes.php';
require_once HDM_B2B_PLUGIN_PATH . 'includes/cart-pricing.php';
require_once HDM_B2B_PLUGIN_PATH . 'includes/payment-terms.php';