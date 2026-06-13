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
    echo '<div class="hdm-b2b-title">B2B Pricing</div>';

    echo '<div class="hdm-b2b-main-price">';
    echo '<div class="hdm-b2b-label">Your B2B price</div>';
    echo '<div class="hdm-b2b-price">' . wc_price($b2b_price) . '</div>';
    echo '</div>';

    if ($retail_price) {
        echo '<div class="hdm-b2b-retail">Retail price: ' . wc_price($retail_price) . '</div>';
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
 * Hide standard WooCommerce price for B2B users
 *
 * Purpose:
 * Prevent duplicate pricing display when the custom
 * B2B pricing box is shown.
 */

add_filter(
    'woocommerce_get_price_html',
    'hdm_hide_standard_price_for_b2b',
    20,
    2
);

/* function hdm_hide_standard_price_for_b2b($price, $product) {

    if (!hdm_is_b2b_customer()) {
        return $price;
    }

    return '';
} */


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
    echo '<strong>Payment terms:</strong> ' . esc_html(get_user_meta($user->ID, 'hdm_payment_terms', true));
    echo '</div>';

    return ob_get_clean();
});