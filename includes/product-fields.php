<?php
/**
 * product-fields.php
 *
 * Handles:
 * - WooCommerce product admin fields
 * - saving B2B product pricing data
 *
 * Purpose:
 * Store B2B pricing and tier discount data per product.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'woocommerce_product_options_pricing',
    'hdm_add_b2b_pricing_fields'
);

function hdm_add_b2b_pricing_fields() {

    echo '<div class="options_group">';

    woocommerce_wp_text_input([
        'id' => '_hdm_b2b_net_price',
        'label' => 'B2B Net Price (€)',
        'type' => 'number',
        'custom_attributes' => [
            'step' => '0.01',
            'min'  => '0'
        ]
    ]);

    woocommerce_wp_text_input([
        'id' => '_hdm_b2b_tier1_qty',
        'label' => 'Tier 1 Quantity',
        'type' => 'number'
    ]);

    woocommerce_wp_text_input([
        'id' => '_hdm_b2b_tier1_discount',
        'label' => 'Tier 1 Discount (%)',
        'type' => 'number'
    ]);

    woocommerce_wp_text_input([
        'id' => '_hdm_b2b_tier2_qty',
        'label' => 'Tier 2 Quantity',
        'type' => 'number'
    ]);

    woocommerce_wp_text_input([
        'id' => '_hdm_b2b_tier2_discount',
        'label' => 'Tier 2 Discount (%)',
        'type' => 'number'
    ]);

    echo '</div>';
}

add_action(
    'woocommerce_process_product_meta',
    'hdm_save_b2b_pricing_fields'
);

function hdm_save_b2b_pricing_fields($product_id) {

    $fields = [
        '_hdm_b2b_net_price',
        '_hdm_b2b_tier1_qty',
        '_hdm_b2b_tier1_discount',
        '_hdm_b2b_tier2_qty',
        '_hdm_b2b_tier2_discount'
    ];

    foreach ($fields as $field) {

        if (isset($_POST[$field])) {

            update_post_meta(
                $product_id,
                $field,
                sanitize_text_field(
                    wp_unslash($_POST[$field])
                )
            );
        }
    }
}