<?php
/**
 * admin-settings.php
 *
 * Handles global plugin settings.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add HDM B2B settings tab to WooCommerce settings.
 */
add_filter('woocommerce_settings_tabs_array', 'hdm_b2b_add_settings_tab', 50);

function hdm_b2b_add_settings_tab($settings_tabs) {

    $settings_tabs['hdm_b2b'] = 'HDM B2B';

    return $settings_tabs;
}

/**
 * Output settings fields.
 */
add_action('woocommerce_settings_tabs_hdm_b2b', 'hdm_b2b_settings_tab_content');

function hdm_b2b_settings_tab_content() {

    woocommerce_admin_fields(hdm_b2b_get_settings());
}

/**
 * Save settings fields.
 */
add_action('woocommerce_update_options_hdm_b2b', 'hdm_b2b_save_settings');

function hdm_b2b_save_settings() {

    woocommerce_update_options(hdm_b2b_get_settings());
}

/**
 * Define settings.
 */
function hdm_b2b_get_settings() {

    return [
        [
            'title' => 'HDM B2B Pricing Settings',
            'type'  => 'title',
            'desc'  => 'Configure how B2B pricing should work on this site.',
            'id'    => 'hdm_b2b_settings_title',
        ],
        [
            'title'   => 'Pricing mode',
            'desc'    => 'Choose how this site calculates B2B prices.',
            'id'      => 'hdm_b2b_pricing_mode',
            'type'    => 'select',
            'default' => 'legacy',
            'options' => [
                'legacy' => 'Legacy mode — one B2B net price + quantity discounts',
                'tiered' => 'Reseller tier mode — Silver / Gold / Platinum / VIP prices',
            ],
        ],
        [
            'type' => 'sectionend',
            'id'   => 'hdm_b2b_settings_end',
        ],
    ];
}

/**
 * Helper: get active pricing mode.
 */
function hdm_b2b_get_pricing_mode() {

    $mode = get_option('hdm_b2b_pricing_mode', 'legacy');

    $allowed_modes = [
        'legacy',
        'tiered',
    ];

    if (!in_array($mode, $allowed_modes, true)) {
        return 'legacy';
    }

    return $mode;
}