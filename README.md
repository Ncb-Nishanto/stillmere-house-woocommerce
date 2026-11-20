# Stillmere House

Stillmere House is a fictional homewares store built with WordPress, WooCommerce, and a custom classic theme. The collection includes six physical products across Table, Home, and Bath. The Oat Stoneware Cup has two independently priced and stocked variations.

## Portfolio walkthrough

1. Start on the homepage and open **Shop the collection**. Browse the shop and open **Oat Stoneware Cup**.
2. Choose **Single cup** ($18) or **Set of two** ($32). Add one to the cart and use **Update cart** to change the quantity. The test fixture offers $6 standard U.S. shipping.
3. Proceed to checkout as a guest. Use fictional details such as Taylor Example, 100 Demo Way, Portland, OR 97201, and `taylor@example.test`. Select **Cash on delivery (demo)** and place the order.
4. View the order confirmation, then open **WooCommerce → Orders** to inspect the test order, payment method, variation, and quantities. A test checkout uses WooCommerce’s actual order and stock APIs. The demo gateway collects no payment and the local demo sends no email.
5. Open the Home page in the WordPress block editor and the cup in the WooCommerce product editor to show how the content and catalog can be edited.

## Run locally

Create a fresh local WordPress installation with PHP 8.1+ and MySQL or MariaDB. Check that your local server supports pretty permalinks. Set `PROJECT` to the absolute path of this checkout and run the following with WP-CLI from the WordPress root. On Windows, use Git Bash or copy the two directories with File Explorer and then run the WP-CLI commands.

```sh
PROJECT="/absolute/path/to/home-lifestyle-woocommerce-demo"
cp -R "$PROJECT/theme/stillmere" wp-content/themes/
cp -R "$PROJECT/plugins/stillmere-demo" wp-content/plugins/
wp plugin install woocommerce --activate
wp plugin activate stillmere-demo
wp theme activate stillmere
wp eval-file "$PROJECT/scripts/seed.php"
```

Open the URL printed by the seed script. The script builds the pages and catalog, attaches local images, and sets the shop currency, U.S. shipping, guest checkout, and offline gateway. It leaves previously seeded page and product content in place on a rerun. Store-level demo settings are applied each time.

Use a dedicated install. The demo plugin permits only WooCommerce’s Cash on Delivery gateway and suppresses outbound WordPress mail. The cart and checkout use WooCommerce’s supported classic shortcodes and templates. No payment service, external theme, or paid extension is required.

## Verification

Tested locally in Chromium at 1440×1000 and 390×844 with WordPress 7.1.5, WooCommerce 11.3.1, and PHP 8.3.37 using the WordPress SQLite integration. Verified all six product routes, both variation prices, add to cart, a quantity update from one to two packs, mobile navigation and checkout layout, and a complete desktop guest checkout. WooCommerce saved the $70.00 order as Processing via Cash on Delivery, recorded $6.00 shipping, and reduced the selected variation’s stock by two. A seed rerun preserved the six-product catalog and remaining stock.

[Desktop home](screenshots/desktop-home.png) · [Mobile home](screenshots/mobile-home.png) · [Mobile variable product](screenshots/mobile-product.png) · [Mobile checkout](screenshots/mobile-checkout.png)

## Data and imagery

- `data/home.html` contains the homepage’s editable WordPress blocks.
- `data/products.json` contains product descriptions, SKUs, prices, and variation stock.
- `data/images/` contains the catalog photos imported into the media library by the seed script.
- The original images were generated with OpenAI ImageGen. The hero brief was a moss-green pitcher, oat stoneware cups, and flax linen on a warm oak table. The catalog brief was six centered product photographs of a cup, runner, pitcher, candle, tray, and towel against pale natural materials.

See [WP-CLI installation](https://make.wordpress.org/cli/handbook/guides/installing/), [WooCommerce classic page shortcodes](https://woocommerce.com/document/woocommerce-shortcodes/page-shortcodes/), and [WooCommerce Cash on Delivery](https://woocommerce.com/document/cash-on-delivery/).
