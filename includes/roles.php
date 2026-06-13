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