<?php
/**
 * payment-terms.php
 *
 * Handles:
 * - payment term field on user profiles
 * - saving payment terms as user meta
 * - controlling invoice/BACS payment method visibility and labels
 *
 * Purpose:
 * Allows different B2B customers to have prepaid, Net 14 or Net 30 payment terms.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get current user's payment terms label
 */
function hdm_get_payment_terms_label($user_id = null) {

    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    $payment_terms = get_user_meta($user_id, 'hdm_payment_terms', true);

    $labels = [
        'prepaid'   => 'Prepayment',
        'invoice14' => 'Net 14 days',
        'invoice30' => 'Net 30 days',
    ];

    return $labels[$payment_terms] ?? '';
}

/**
 * Add payment terms field to user profile
 */
add_action('show_user_profile', 'hdm_payment_terms_field');
add_action('edit_user_profile', 'hdm_payment_terms_field');

function hdm_payment_terms_field($user) {

    $payment_terms = get_user_meta($user->ID, 'hdm_payment_terms', true);

    if (!$payment_terms) {
        $payment_terms = 'prepaid';
    }
    ?>

    <h2>HDM B2B Settings</h2>

    <table class="form-table">
        <tr>
            <th>
                <label for="hdm_payment_terms">Payment Terms</label>
            </th>
            <td>
                <select name="hdm_payment_terms" id="hdm_payment_terms">
                    <option value="prepaid" <?php selected($payment_terms, 'prepaid'); ?>>
                        Prepayment
                    </option>
                    <option value="invoice14" <?php selected($payment_terms, 'invoice14'); ?>>
                        Net 14 days
                    </option>
                    <option value="invoice30" <?php selected($payment_terms, 'invoice30'); ?>>
                        Net 30 days
                    </option>
                </select>
            </td>
        </tr>
    </table>

    <?php
}

/**
 * Save payment terms field
 */
add_action('personal_options_update', 'hdm_save_payment_terms');
add_action('edit_user_profile_update', 'hdm_save_payment_terms');

function hdm_save_payment_terms($user_id) {

    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    if (isset($_POST['hdm_payment_terms'])) {
        update_user_meta(
            $user_id,
            'hdm_payment_terms',
            sanitize_text_field(wp_unslash($_POST['hdm_payment_terms']))
        );
    }
}

/**
 * Control and rename BACS payment gateway based on B2B payment terms
 */
add_filter('woocommerce_available_payment_gateways', 'hdm_b2b_payment_gateways');

function hdm_b2b_payment_gateways($gateways) {

    if (is_admin()) {
        return $gateways;
    }

    if (!isset($gateways['bacs'])) {
        return $gateways;
    }

    if (!hdm_is_b2b_customer()) {
        unset($gateways['bacs']);
        return $gateways;
    }

    $payment_terms = get_user_meta(get_current_user_id(), 'hdm_payment_terms', true);

    if ($payment_terms === 'prepaid') {
        $gateways['bacs']->title = 'Pay on invoice — Prepayment';
        $gateways['bacs']->description = 'Payment is required before shipment.';
        return $gateways;
    }

    if ($payment_terms === 'invoice14') {
        $gateways['bacs']->title = 'Pay on invoice — Net 14 days';
        $gateways['bacs']->description = 'You may pay within 14 days after invoice date.';
        return $gateways;
    }

    if ($payment_terms === 'invoice30') {
        $gateways['bacs']->title = 'Pay on invoice — Net 30 days';
        $gateways['bacs']->description = 'You may pay within 30 days after invoice date.';
        return $gateways;
    }

    unset($gateways['bacs']);

    return $gateways;
}

/**
 * Save B2B payment terms on the order
 */
add_action('woocommerce_checkout_create_order', 'hdm_save_payment_terms_to_order', 20, 2);

function hdm_save_payment_terms_to_order($order, $data) {

    if (!hdm_is_b2b_customer()) {
        return;
    }

    $payment_terms = get_user_meta(get_current_user_id(), 'hdm_payment_terms', true);

    if (!$payment_terms) {
        $payment_terms = 'prepaid';
    }

    $labels = [
        'prepaid'   => 'Prepayment',
        'invoice14' => 'Net 14 days',
        'invoice30' => 'Net 30 days',
    ];

    $label = $labels[$payment_terms] ?? $payment_terms;

    $order->update_meta_data('_hdm_payment_terms', $payment_terms);
    $order->update_meta_data('_hdm_payment_terms_label', $label);
}

/**
 * Show B2B payment terms in admin order screen
 */
add_action('woocommerce_admin_order_data_after_billing_address', 'hdm_show_payment_terms_admin_order');

function hdm_show_payment_terms_admin_order($order) {

    $label = $order->get_meta('_hdm_payment_terms_label');

    if (!$label) {
        return;
    }

    echo '<p><strong>B2B payment term:</strong> ' . esc_html($label) . '</p>';
}