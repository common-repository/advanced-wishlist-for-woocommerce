<?php

namespace AlgolWishlist\AdminExtensions;

use AlgolWishlist\Context;

class AdminPage
{
    const SLUG = 'algol_wishlist';

    /**
     * @var Context
     */
    protected $context;

    public function __construct()
    {
        $this->context = awlContext();
    }

    public function registerPage()
    {
        add_action('admin_menu', function () {
            add_submenu_page(
                'woocommerce',
                __('Wishlist', 'wc-wishlist'),
                __('Wishlist', 'wc-wishlist'),
                'manage_woocommerce',
                self::SLUG,
                array($this, 'showAdminPage'));
        });

        add_action("admin_init", function () {
            global $plugin_page;
            if ($plugin_page === self::SLUG) {
                add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
            }
        });
    }

    public function showAdminPage()
    {
        echo '<div class="wrap woocommerce"><div id="root"></div></div>';
    }

    public function enqueueScripts()
    {
        wp_enqueue_media();
        wp_enqueue_editor();

        wp_enqueue_script(
            'algol_wishlist_admin_app',
            ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/admin-app/build-app.js',
            [],
            ALGOL_WISHLIST_VERSION,
            true
        );

        $licenseSettingsFields = [];
        $error = false;

        wp_localize_script(
            'algol_wishlist_admin_app',
            'algolWishlistAdminAppData',
            [
                'host' => trailingslashit(get_site_url()),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'popularProductsTab' => [
                    'itemsPerPage' => 10,
                ],
                'licenseTab' => [
                    'dashboardLink' => admin_url('update-core.php'),
                    'license' => get_option('edd_awl_license_key') ?: '',
                    'status' => get_option('edd_awl_license_status'),
                    'error' => $error,
                    'settingsFields' => $licenseSettingsFields,
                ],
            ]
        );
        wp_enqueue_style(
            'algol_wishlist_admin_app_styles',
            ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/admin-app/bundle.css',
            [],
            ALGOL_WISHLIST_VERSION
        );
    }
}
