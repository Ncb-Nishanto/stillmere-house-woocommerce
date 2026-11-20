<?php
/**
 * Run with wp eval-file scripts/seed.php from a dedicated local installation.
 */

if (!class_exists('WooCommerce') || 'stillmere' !== get_stylesheet() || !in_array('stillmere-demo/stillmere-demo.php', get_option('active_plugins', []), true)) {
    WP_CLI::error('Activate WooCommerce, Stillmere Offline Checkout, and the Stillmere House theme first.');
}

$project = dirname(__DIR__);
$catalog = json_decode(file_get_contents($project . '/data/products.json'), true, flags: JSON_THROW_ON_ERROR);
require_once ABSPATH . 'wp-admin/includes/image.php';

function stillmere_seed_page(string $slug, string $title, string $content, string $option = ''): int
{
    $assigned_id = $option ? (int) get_option($option) : 0;
    $page = $assigned_id ? get_post($assigned_id) : get_page_by_path($slug);
    if ($page && ('page' !== $page->post_type || ($page->ID !== $assigned_id && !get_post_meta($page->ID, '_stillmere_seed', true)))) {
        WP_CLI::error("A page with slug {$slug} belongs to another setup. Use a fresh WordPress installation.");
    }
    if ($page && get_post_meta($page->ID, '_stillmere_seed', true)) {
        return $page->ID;
    }
    $id = wp_insert_post(wp_slash([
        'ID' => $page ? $page->ID : 0,
        'post_name' => $slug,
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_content' => $content,
        'meta_input' => ['_stillmere_seed' => 1],
    ]), true);
    if (is_wp_error($id)) {
        WP_CLI::error($id->get_error_message());
    }
    return (int) $id;
}

function stillmere_seed_image(array $record, string $project): int
{
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_key' => '_stillmere_asset',
        'meta_value' => $record['image'],
        'fields' => 'ids',
        'numberposts' => 1,
    ]);
    if ($existing) {
        return (int) $existing[0];
    }
    $path = $project . '/data/images/' . $record['image'];
    if (!is_readable($path)) {
        WP_CLI::error('Missing catalog photo ' . $record['image']);
    }
    $upload = wp_upload_bits($record['image'], null, file_get_contents($path));
    if ($upload['error']) {
        WP_CLI::error($upload['error']);
    }
    $id = wp_insert_attachment([
        'post_mime_type' => 'image/jpeg',
        'post_title' => $record['name'],
        'post_status' => 'inherit',
    ], $upload['file'], 0, true);
    if (is_wp_error($id)) {
        WP_CLI::error($id->get_error_message());
    }
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));
    update_post_meta($id, '_wp_attachment_image_alt', $record['alt']);
    update_post_meta($id, '_stillmere_asset', $record['image']);
    return $id;
}

$home_id = stillmere_seed_page('home', 'Home', file_get_contents($project . '/data/home.html'));
update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);
foreach ([
    'shop' => ['Shop', ''],
    'cart' => ['Cart', '[woocommerce_cart]'],
    'checkout' => ['Checkout', '[woocommerce_checkout]'],
    'myaccount' => ['My account', '[woocommerce_my_account]'],
] as $key => [$title, $content]) {
    $option = 'woocommerce_' . $key . '_page_id';
    $slug = 'myaccount' === $key ? 'my-account' : $key;
    update_option($option, stillmere_seed_page($slug, $title, $content, $option));
}

foreach ([
    'blogname' => 'Stillmere House',
    'blogdescription' => 'Homewares in natural materials',
    'permalink_structure' => '/%postname%/',
    'woocommerce_default_country' => 'US:OR',
    'woocommerce_allowed_countries' => 'specific',
    'woocommerce_specific_allowed_countries' => ['US'],
    'woocommerce_ship_to_countries' => '',
    'woocommerce_ship_to_destination' => 'billing_only',
    'woocommerce_default_customer_address' => 'base',
    'woocommerce_currency' => 'USD',
    'woocommerce_calc_taxes' => 'no',
    'woocommerce_enable_coupons' => 'no',
    'woocommerce_enable_guest_checkout' => 'yes',
    'woocommerce_enable_checkout_login_reminder' => 'no',
    'woocommerce_enable_signup_and_login_from_checkout' => 'no',
    'woocommerce_checkout_privacy_policy_text' => 'Checkout details are stored in the local WordPress database.',
    'woocommerce_coming_soon' => 'no',
    'woocommerce_store_pages_only' => 'no',
    'woocommerce_enable_reviews' => 'no',
] as $key => $value) {
    update_option($key, $value);
}

update_option('woocommerce_cod_settings', [
    'enabled' => 'yes',
    'title' => 'Cash on delivery',
    'description' => 'This is a test checkout. Use sample details. No payment or delivery will take place.',
    'instructions' => 'Your test order was saved. No payment is due.',
    'enable_for_methods' => [],
    'enable_for_virtual' => 'no',
]);

$store_zone_id = 0;
foreach (WC_Shipping_Zones::get_zones() as $zone_data) {
    if ('Stillmere United States' === $zone_data['zone_name']) {
        $store_zone_id = $zone_data['zone_id'];
        break;
    }
}
$zone = new WC_Shipping_Zone($store_zone_id);
$zone->set_zone_name('Stillmere United States');
$zone->set_zone_order(0);
$zone->set_locations([['code' => 'US', 'type' => 'country']]);
$zone->save();
$flat_rate_id = 0;
foreach ($zone->get_shipping_methods() as $method) {
    if ('flat_rate' === $method->id) {
        $flat_rate_id = $method->get_instance_id();
        break;
    }
}
$flat_rate_id = $flat_rate_id ?: $zone->add_shipping_method('flat_rate');
update_option('woocommerce_flat_rate_' . $flat_rate_id . '_settings', [
    'title' => 'Standard shipping',
    'cost' => '6.00',
    'tax_status' => 'none',
]);
WC_Cache_Helper::get_transient_version('shipping', true);

foreach ($catalog as $position => $record) {
    $existing_id = wc_get_product_id_by_sku($record['sku']);
    $variable = isset($record['options']);
    if ($existing_id && !get_post_meta($existing_id, '_stillmere_seed', true)) {
        WP_CLI::error('A product already uses fixture SKU ' . $record['sku']);
    }
    $product = $existing_id
        ? wc_get_product($existing_id)
        : ($variable ? new WC_Product_Variable() : new WC_Product_Simple());
    if (!$existing_id) {
        $category = term_exists($record['category'], 'product_cat');
        $category = $category ?: wp_insert_term($record['category'], 'product_cat');
        if (is_wp_error($category)) {
            WP_CLI::error($category->get_error_message());
        }
        $product->set_sku($record['sku']);
        $product->set_slug($record['slug']);
        $product->set_name($record['name']);
        $product->set_status('publish');
        $product->set_short_description($record['summary']);
        $product->set_description($record['description']);
        $product->set_image_id(stillmere_seed_image($record, $project));
        $product->set_category_ids([(int) $category['term_id']]);
        $product->set_menu_order($position);
        $product->set_reviews_allowed(false);
        $product->update_meta_data('_stillmere_seed', 1);
        if ($variable) {
            $attribute = new WC_Product_Attribute();
            $attribute->set_name('Pack size');
            $attribute->set_options(array_column($record['options'], 'name'));
            $attribute->set_visible(true);
            $attribute->set_variation(true);
            $product->set_attributes([$attribute]);
        } else {
            $product->set_regular_price($record['price']);
            $product->set_manage_stock(true);
            $product->set_stock_quantity($record['stock']);
        }
        $product->save();
    }

    if ($variable) {
        foreach ($record['options'] as $option) {
            $variation_id = wc_get_product_id_by_sku($option['sku']);
            if ($variation_id) {
                $variation = wc_get_product($variation_id);
                if (!$variation || $variation->get_parent_id() !== $product->get_id()) {
                    WP_CLI::error('Another product already uses variation SKU ' . $option['sku']);
                }
                continue;
            }
            $variation = new WC_Product_Variation();
            $variation->set_parent_id($product->get_id());
            $variation->set_status('publish');
            $variation->set_sku($option['sku']);
            $variation->set_attributes(['pack-size' => $option['name']]);
            $variation->set_regular_price($option['price']);
            $variation->set_manage_stock(true);
            $variation->set_stock_quantity($option['stock']);
            $variation->save();
        }
        WC_Product_Variable::sync($product->get_id());
    }
}

flush_rewrite_rules(false);
WP_CLI::success('Stillmere House is ready at ' . home_url('/'));
