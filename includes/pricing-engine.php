<?php
/**
 * pricing-engine.php
 *
 * Handles:
 * - B2B price calculations
 * - quantity tier discount calculations
 *
 * Purpose:
 * Central pricing engine used by:
 * - frontend display
 * - cart calculations
 * - future checkout/order logic
 */

if (!defined('ABSPATH')) {
    exit;
}

function hdm_get_b2b_price($product_id, $qty = 1) {

    $net_price = (float) get_post_meta(
        $product_id,
        '_hdm_b2b_net_price',
        true
    );

    if (!$net_price) {
        return false;
    }

    $tier1_qty = (int) get_post_meta(
        $product_id,
        '_hdm_b2b_tier1_qty',
        true
    );

    $tier1_discount = (float) get_post_meta(
        $product_id,
        '_hdm_b2b_tier1_discount',
        true
    );

    $tier2_qty = (int) get_post_meta(
        $product_id,
        '_hdm_b2b_tier2_qty',
        true
    );

    $tier2_discount = (float) get_post_meta(
        $product_id,
        '_hdm_b2b_tier2_discount',
        true
    );

    $discount = 0;

    if ($tier2_qty && $qty >= $tier2_qty) {

        $discount = $tier2_discount;

    } elseif ($tier1_qty && $qty >= $tier1_qty) {

        $discount = $tier1_discount;
    }

    return $net_price * (1 - ($discount / 100));
}

function hdm_get_tier_price($product_id, $tier) {

    $meta_map = [
        'silver'   => '_hdm_b2b_price_silver',
        'gold'     => '_hdm_b2b_price_gold',
        'platinum' => '_hdm_b2b_price_platinum',
        'vip'      => '_hdm_b2b_price_vip',
    ];

    if (!isset($meta_map[$tier])) {
        return false;
    }

    $price = get_post_meta(
        $product_id,
        $meta_map[$tier],
        true
    );

    if ($price === '' || $price === null) {
        return false;
    }

    return (float) $price;
}