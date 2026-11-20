<?php
/**
 * Plugin Name: Stillmere Offline Checkout
 * Description: Keeps Stillmere checkout offline-only and disables outbound mail.
 * Version: 1.0.0
 * Requires PHP: 8.1
 * License: GPL-2.0-or-later
 */

defined('ABSPATH') || exit;

add_filter('woocommerce_available_payment_gateways', static function (array $gateways): array {
    return array_intersect_key($gateways, ['cod' => true]);
}, PHP_INT_MAX);

add_filter('pre_wp_mail', '__return_true', PHP_INT_MAX);
