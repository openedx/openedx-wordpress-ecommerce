<?php 

namespace App\utils;

// Enrollment Request mode options
function get_enrollment_options() {
    return array(
        'Honor'             => __('Honor', 'woocommerce'),
        'Audit'             => __('Audit', 'woocommerce'),
        'Verified'          => __('Verified', 'woocommerce'),
        'Credit'            => __('Credit', 'woocommerce'),
        'Professional'      => __('Professional', 'woocommerce'),
        'No ID Professional' => __('No ID Professional', 'woocommerce'),
    );
}

