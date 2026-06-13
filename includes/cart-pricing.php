<?php
/**
 * cart-pricing.php
 *
 * Handles:
 * - applying B2B prices in WooCommerce cart
 * - recalculating tier prices when quantities change
 *
 * Purpose:
 * Ensures checkout/cart totals use the actual B2B calculated price.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('woocommerce_before_calculate_totals', 'hdm_apply_b2b_cart_prices');

function hdm_apply_b2b_cart_prices($cart) {

    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    if (!hdm_is_b2b_customer()) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item) {

        $product_id = $cart_item['product_id'];
        $qty = $cart_item['quantity'];

        $b2b_price = hdm_get_b2b_price($product_id, $qty);

        if ($b2b_price !== false) {
            $cart_item['data']->set_price($b2b_price);
        }
    }
}