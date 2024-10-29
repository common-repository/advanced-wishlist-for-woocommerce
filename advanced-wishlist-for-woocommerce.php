<?php
/**
 * Plugin Name: Advanced Wishlist for WooCommerce
 * Plugin URI:
 * Description: Adds wishlists to WooCommerce store, so owner can track popular products
 * Version: 1.0.0
 * Author: AlgolPlus
 * Author URI: https://algolplus.com/
 * WC requires at least: 6.0
 * WC tested up to: 7.3
 *
 * Text Domain: wc-wishlist
 * Domain Path: /languages
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//Stop if another version is active!
if (defined('ALGOL_WISHLIST_PLUGIN_FILE')) {
    add_action('admin_notices', function () {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php
                echo esc_html__('Please, ', 'wc-wishlist') . '<a href="plugins.php">' .
                    esc_html__('deactivate', 'wc-wishlist') . '</a>' .
                    esc_html__(' Free version of WC Wishlists!', 'wc-wishlist');
                ?></p>
        </div>
        <?php
    });

    return;
}

define('ALGOL_WISHLIST_MIN_PHP_VERSION', '7.2.0');
define('ALGOL_WISHLIST_MIN_WC_VERSION', '6.0');
define('ALGOL_WISHLIST_VERSION', '1.0.0');

define('ALGOL_WISHLIST_PLUGIN_FILE', basename(__FILE__));
define('ALGOL_WISHLIST_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ALGOL_WISHLIST_PLUGIN_URL', plugins_url('', __FILE__));

include_once "vendor/autoload.php";
include_once "awlContext.php";

(\AlgolWishlist\Factory::get("PluginActions", __FILE__))->register();
(new \AlgolWishlist\Main())->startUp();
