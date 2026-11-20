<?php

add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('html5', ['search-form', 'style', 'script']);
});

add_action('wp_enqueue_scripts', static function (): void {
    $dependencies = wp_style_is('woocommerce-general', 'enqueued') ? ['woocommerce-general'] : [];
    wp_enqueue_style('stillmere', get_stylesheet_uri(), $dependencies, wp_get_theme()->get('Version'));
}, 20);

add_action('init', static function (): void {
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
});

function stillmere_shop_url(): string
{
    return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
}

add_shortcode('stillmere_shop_link', static function (): string {
    return '<a class="st-button" href="' . esc_url(stillmere_shop_url()) . '">Shop the collection</a>';
});

add_shortcode('stillmere_hero_photo', static function (): string {
    return '<img class="st-hero-image" src="' . esc_url(get_theme_file_uri('/assets/stillmere-table.jpg')) . '" width="1536" height="1024" fetchpriority="high" alt="A moss-green stoneware pitcher, oat cups, and flax linen on an oak table">';
});
