<?php
/**
 * reseller-tier.php
 *
 * Handles reseller tier user meta.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('show_user_profile', 'hdm_reseller_tier_field');
add_action('edit_user_profile', 'hdm_reseller_tier_field');

function hdm_reseller_tier_field($user) {

    if (!in_array('b2b_customer', (array) $user->roles, true)) {
        return;
    }

    $tier = get_user_meta($user->ID, 'hdm_reseller_tier', true);
    ?>

    <h2>HDM Reseller Tier</h2>

    <table class="form-table">
        <tr>
            <th>
                <label for="hdm_reseller_tier">Reseller Tier</label>
            </th>
            <td>
                <select name="hdm_reseller_tier" id="hdm_reseller_tier">
                    <option value="">Select tier</option>
                    <option value="silver" <?php selected($tier, 'silver'); ?>>Silver</option>
                    <option value="gold" <?php selected($tier, 'gold'); ?>>Gold</option>
                    <option value="platinum" <?php selected($tier, 'platinum'); ?>>Platinum</option>
                    <option value="vip" <?php selected($tier, 'vip'); ?>>VIP</option>
                </select>
                <p class="description">
                    Internal reseller tier used for B2B pricing. Not shown publicly.
                </p>
            </td>
        </tr>
    </table>

    <?php
}

add_action('personal_options_update', 'hdm_save_reseller_tier');
add_action('edit_user_profile_update', 'hdm_save_reseller_tier');

function hdm_save_reseller_tier($user_id) {

    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    $user = get_userdata($user_id);

    if (!$user || !in_array('b2b_customer', (array) $user->roles, true)) {
        delete_user_meta($user_id, 'hdm_reseller_tier');
        return;
    }

    if (isset($_POST['hdm_reseller_tier'])) {

        $tier = sanitize_text_field(wp_unslash($_POST['hdm_reseller_tier']));

        $allowed_tiers = [
            '',
            'silver',
            'gold',
            'platinum',
            'vip',
        ];

        if (in_array($tier, $allowed_tiers, true)) {
            update_user_meta($user_id, 'hdm_reseller_tier', $tier);
        }
    }
}

/**
 * Add reseller tier column after Role in Users overview.
 */
add_filter('manage_users_columns', 'hdm_add_reseller_tier_user_column');

function hdm_add_reseller_tier_user_column($columns) {

    $new_columns = [];

    foreach ($columns as $key => $label) {

        $new_columns[$key] = $label;

        if ($key === 'role') {
            $new_columns['hdm_reseller_tier'] = 'Reseller Tier';
        }
    }

    return $new_columns;
}

add_filter('manage_users_custom_column', 'hdm_show_reseller_tier_user_column', 10, 3);

function hdm_show_reseller_tier_user_column($value, $column_name, $user_id) {

    if ($column_name !== 'hdm_reseller_tier') {
        return $value;
    }

    $user = get_userdata($user_id);

    if (!$user || !in_array('b2b_customer', (array) $user->roles, true)) {
        return '—';
    }

    $tier = get_user_meta($user_id, 'hdm_reseller_tier', true);

    $labels = [
        'silver'   => 'Silver',
        'gold'     => 'Gold',
        'platinum' => 'Platinum',
        'vip'      => 'VIP',
    ];

    return $labels[$tier] ?? 'Not set';
}