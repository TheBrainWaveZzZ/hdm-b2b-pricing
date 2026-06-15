<?php
/**
 * frontend-shortcodes.php
 *
 * Handles:
 * - B2B price box shortcode
 * - debug shortcode for testing current user role/payment terms
 *
 * Purpose:
 * Gives Elementor/Block templates a reliable way to show plugin output.
 */

if (!defined('ABSPATH')) {
    exit;
}



/**
 * Show B2B price box
 */
function hdm_show_b2b_price_box() {

    if (!hdm_is_b2b_customer()) {
        return;
    }

    global $product;

    if (!$product) {
        return;
    }

    $product_id = $product->get_id();
    $b2b_price = hdm_get_b2b_price($product_id, 1);

    if ($b2b_price === false) {
        return;
    }

    $retail_price = $product->get_regular_price();

    $tier1_qty = (int) get_post_meta($product_id, '_hdm_b2b_tier1_qty', true);
    $tier2_qty = (int) get_post_meta($product_id, '_hdm_b2b_tier2_qty', true);
    $tier1_discount = (float) get_post_meta($product_id, '_hdm_b2b_tier1_discount', true);
    $tier2_discount = (float) get_post_meta($product_id, '_hdm_b2b_tier2_discount', true);

    echo '<div class="hdm-b2b-box">';

    echo '<div class="hdm-b2b-main-price">';
    echo '<div class="hdm-b2b-label">Your price</div>';
    echo '<div class="hdm-b2b-price">' . wc_price($b2b_price) . '</div>';
    echo '</div>';

    if ($retail_price) {
        echo '<div class="hdm-b2b-retail">Recommended Retail Price: ' . wc_price($retail_price) . '</div>';
    }

   if ($tier1_qty || $tier2_qty) {

    echo '<div class="hdm-b2b-tier-table">';
    echo '<div class="hdm-b2b-tier-title">Volume pricing</div>';

    echo '<table>';

    echo '<thead>';
    echo '<tr>';
    echo '<th>Qty</th>';
    echo '<th>Discount</th>';
    echo '<th>Net price</th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tbody>';

    if ($tier1_qty) {
        echo '<tr>';
        echo '<td>' . esc_html($tier1_qty) . '+</td>';
        echo '<td>-' . esc_html($tier1_discount) . '%</td>';
        echo '<td>' . wc_price(hdm_get_b2b_price($product_id, $tier1_qty)) . '</td>';
        echo '</tr>';
    }

    if ($tier2_qty) {
        echo '<tr>';
        echo '<td>' . esc_html($tier2_qty) . '+</td>';
        echo '<td>-' . esc_html($tier2_discount) . '%</td>';
        echo '<td>' . wc_price(hdm_get_b2b_price($product_id, $tier2_qty)) . '</td>';
        echo '</tr>';
    }

    $b2b_price = hdm_get_b2b_price($product_id, 1);

echo '<strong>Pricing Engine Result:</strong> '
    . ($b2b_price !== false ? wc_price($b2b_price) : 'NO B2B PRICE')
    . '<br>';

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
}

/**
 * Shortcode: [hdm_b2b_price_box]
 */
add_shortcode('hdm_b2b_price_box', function () {
    ob_start();
    hdm_show_b2b_price_box();
    return ob_get_clean();
});

/**
 * Shortcode: [hdm_debug_user_role]
 */
add_shortcode('hdm_debug_user_role', function () {

    $user = wp_get_current_user();

    ob_start();

    echo '<div style="padding:10px;border:2px solid red;margin:10px 0;">';
    echo '<strong>HDM DEBUG</strong><br>';
    echo '<strong>Logged in:</strong> ' . (is_user_logged_in() ? 'YES' : 'NO') . '<br>';
    echo '<strong>User ID:</strong> ' . esc_html($user->ID) . '<br>';
    echo '<strong>Roles:</strong> ' . esc_html(implode(', ', (array) $user->roles)) . '<br>';
    echo '<strong>Pricing mode:</strong> ' . esc_html(hdm_b2b_get_pricing_mode()) . '<br>';

    $tier = hdm_get_reseller_tier($user->ID);

    echo '<strong>Reseller tier:</strong> ' . esc_html($tier ?: 'Not set') . '<br>';
    echo '<strong>Payment terms:</strong> ' . esc_html(get_user_meta($user->ID, 'hdm_payment_terms', true)) . '<br>';

    global $product;

    if ($product) {
        $product_id = $product->get_id();
        $tier_price = hdm_get_tier_price($product_id, $tier);

        echo '<strong>Product ID:</strong> ' . esc_html($product_id) . '<br>';
        echo '<strong>Tier price found:</strong> ' . ($tier_price !== false ? 'YES' : 'NO') . '<br>';

        if ($tier_price !== false) {
            echo '<strong>Tier price:</strong> ' . wc_price($tier_price) . '<br>';
        }
    }

    $b2b_price = hdm_get_b2b_price($product_id, 1);

echo '<strong>Pricing Engine Result:</strong> ' . wc_price($b2b_price) . '<br>';

    echo '</div>';

    return ob_get_clean();
});

/**
 * Auto show B2B price box on normal WooCommerce product pages.
 * Useful for testing without Elementor.
 */

add_action(
    'woocommerce_after_single_product_summary',
    'hdm_auto_show_b2b_price_box',
    5
);

function hdm_auto_show_b2b_price_box() {

    if (!hdm_is_b2b_customer()) {
        return;
    }

    echo do_shortcode('[hdm_b2b_price_box]');
}

/**
 * Show debug box on single product page for admins only.
 */
add_action(
    'woocommerce_after_single_product_summary',
    'hdm_show_debug_shortcode_on_product_page',
    6
);

function hdm_show_debug_shortcode_on_product_page() {

    if (!hdm_b2b_debug_mode_enabled()) {
        return;
    }

    if (!current_user_can('manage_options') && !hdm_is_b2b_customer()) {
    return;
}

    echo do_shortcode('[hdm_debug_user_role]');
}
