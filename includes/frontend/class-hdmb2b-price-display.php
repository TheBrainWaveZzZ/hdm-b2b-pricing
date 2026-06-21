<?php
if (!defined('ABSPATH')) {
    exit;
}

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
    echo '<a class="button hdmb2b-secondary-button" href="' . esc_url(site_url('/b2b-request/')) . '">Request B2B account</a>';
    echo '</div>';

    echo '</div>';
}