<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Hide default WooCommerce prices for guests.
 */
add_filter('woocommerce_get_price_html', 'hdmb2b_hide_prices_for_guests', 9999, 2);

function hdmb2b_hide_prices_for_guests($price, $product) {

    if (is_user_logged_in()) {
        return $price;
    }

    return '';
}

/**
 * Hide add-to-cart form/button for guests on single product pages.
 */
add_action('wp', 'hdmb2b_hide_add_to_cart_for_guests');

function hdmb2b_hide_add_to_cart_for_guests() {

    if (is_user_logged_in()) {
        return;
    }

    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
}

/**
 * Show login/request box for guests on single product pages.
 */
add_action('woocommerce_single_product_summary', 'hdmb2b_show_guest_price_box', 25);

function hdmb2b_show_guest_price_box() {

    if (!is_product()) {
        return;
    }

    if (is_user_logged_in()) {
        return;
    }

    echo '<div class="hdmb2b-price-login-box">';
    echo '<strong>Price visible after B2B login</strong>';
    echo '<p>Log in or request B2B access to view your reseller price.</p>';

    echo '<div class="hdmb2b-price-login-actions">';
    echo '<a class="button" href="' . esc_url(wp_login_url(get_permalink())) . '">Log in</a>';
    echo '<a class="button hdmb2b-secondary-button" href="' . esc_url(site_url('/b2b-request/')) . '"> | Request a B2B account</a>';
    echo '</div>';

    echo '</div>';
}