<?php
/**
 * assets.php
 *
 * Handles:
 * - loading frontend CSS for the B2B pricing box
 *
 * Purpose:
 * Keeps styling separate from PHP output.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', 'hdm_b2b_enqueue_assets');

function hdm_b2b_enqueue_assets() {
    wp_enqueue_style(
        'hdm-b2b-pricing',
        HDM_B2B_PLUGIN_URL . 'assets/css/b2b-pricing.css',
        [],
        '0.2'
    );
}