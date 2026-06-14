<?php
/**
 * roles.php
 *
 * Handles:
 * - helper functions for detecting B2B users
 *
 * Purpose:
 * Central location for role-related checks.
 */

if (!defined('ABSPATH')) {
    exit;
}

function hdm_is_b2b_customer() {
    $user = wp_get_current_user();

    return in_array(
        'b2b_customer',
        (array) $user->roles,
        true
    );
}

function hdm_get_reseller_tier($user_id = null) {

    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return '';
    }

    $tier = get_user_meta($user_id, 'hdm_reseller_tier', true);

    $allowed_tiers = [
        'silver',
        'gold',
        'platinum',
        'vip',
    ];

    if (!in_array($tier, $allowed_tiers, true)) {
        return '';
    }

    return $tier;
}