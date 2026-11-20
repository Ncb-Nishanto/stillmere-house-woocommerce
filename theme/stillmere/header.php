<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="st-skip" href="#main">Skip to content</a>
<header class="st-header">
    <p class="st-announcement">$6 flat-rate U.S. shipping</p>
    <div class="st-wrap st-header-row">
        <a class="st-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <svg viewBox="0 0 36 36" fill="none" aria-hidden="true">
                <path d="M18 31V13m0 9c-7 0-11-5-11-10 6 0 11 4 11 10Zm0-5c0-6 4-10 10-11 0 6-4 11-10 11Zm0 11c5 0 9-3 11-7-5 0-9 3-11 7Z" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Stillmere House
        </a>
        <nav class="st-nav" aria-label="Main navigation">
            <a href="<?php echo esc_url(home_url('/')); ?>" <?php if (is_front_page()) echo 'aria-current="page"'; ?>>Home</a>
            <a href="<?php echo esc_url(stillmere_shop_url()); ?>" <?php if (function_exists('is_shop') && is_shop()) echo 'aria-current="page"'; ?>>Shop</a>
        </nav>
        <?php if (function_exists('wc_get_cart_url')) : ?>
            <a class="st-cart" href="<?php echo esc_url(wc_get_cart_url()); ?>" <?php if (is_cart()) echo 'aria-current="page"'; ?>>
                <svg viewBox="0 0 20 22" fill="none" aria-hidden="true"><path d="M2.5 7.5h15l1 12h-17l1-12Zm4-1V5a3.5 3.5 0 0 1 7 0v1.5" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                Cart
            </a>
        <?php endif; ?>
    </div>
</header>
