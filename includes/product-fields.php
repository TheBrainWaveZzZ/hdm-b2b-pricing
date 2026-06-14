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

add_filter('woocommerce_product_data_tabs', 'hdm_add_b2b_product_data_tab');

function hdm_add_b2b_product_data_tab($tabs) {

    $tabs['hdm_b2b_pricing'] = [
        'label'    => 'B2B Pricing',
        'target'   => 'hdm_b2b_pricing_product_data',
        'class'    => [],
        'priority' => 15,
    ];

    return $tabs;
}

add_action(
    'woocommerce_product_data_panels',
    'hdm_add_b2b_pricing_fields'
);

function hdm_add_b2b_pricing_fields() {

    echo '<div id="hdm_b2b_pricing_product_data" class="panel woocommerce_options_panel hidden">';
    echo '<div class="options_group">';

    woocommerce_wp_text_input([
    'id' => '_hdm_b2b_price_silver',
    'label' => 'B2B Silver Price (€)',
    'type' => 'number',
    'custom_attributes' => [
        'step' => '0.01',
        'min'  => '0'
    ]
]);

woocommerce_wp_text_input([
    'id' => '_hdm_b2b_price_gold',
    'label' => 'B2B Gold Price (€)',
    'type' => 'number',
    'custom_attributes' => [
        'step' => '0.01',
        'min'  => '0'
    ]
]);

woocommerce_wp_text_input([
    'id' => '_hdm_b2b_price_platinum',
    'label' => 'B2B Platinum Price (€)',
    'type' => 'number',
    'custom_attributes' => [
        'step' => '0.01',
        'min'  => '0'
    ]
]);

woocommerce_wp_text_input([
    'id' => '_hdm_b2b_price_vip',
    'label' => 'B2B VIP Price (€)',
    'type' => 'number',
    'custom_attributes' => [
        'step' => '0.01',
        'min'  => '0'
    ]
]);


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
        'label' => 'Tier 1 Quantity - Legacy NET PRICE',
        'type' => 'number'
    ]);

    woocommerce_wp_text_input([
        'id' => '_hdm_b2b_tier1_discount',
        'label' => 'Tier 1 Discount (%) - Legacy NET PRICE',
        'type' => 'number'
    ]);

    woocommerce_wp_text_input([
        'id' => '_hdm_b2b_tier2_qty',
        'label' => 'Tier 2 Quantity - Legacy NET PRICE',
        'type' => 'number'
    ]);


    woocommerce_wp_text_input([
        'id' => '_hdm_b2b_tier2_discount',
        'label' => 'Tier 2 Discount (%) - Legacy NET PRICE',
        'type' => 'number'
    ]);

    echo '</div>';
    echo '</div>';
}

add_action(
    'woocommerce_process_product_meta',
    'hdm_save_b2b_pricing_fields'
);

function hdm_save_b2b_pricing_fields($product_id) {

    $fields = [
    '_hdm_b2b_net_price',

    '_hdm_b2b_price_silver',
    '_hdm_b2b_price_gold',
    '_hdm_b2b_price_platinum',
    '_hdm_b2b_price_vip',

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