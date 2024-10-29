<?php

namespace AlgolWishlist\VersionFree;

use AlgolWishlist\AdminExtensions\AdminPage;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use AlgolWishlist\Settings\StoreStrategy;
use AlgolWishlist\VersionFree\WishlistPageApp\WishlistPage;

defined('ABSPATH') or exit;

class PluginActions
{
    /**
     * @var string
     */
    protected $pluginFileFullPath;

    /**
     * @param string|null $pluginFileFullPath
     */
    public function __construct($pluginFileFullPath)
    {
        $this->pluginFileFullPath = $pluginFileFullPath;
    }

    /**
     *  Only a static class method or function can be used in an uninstallation hook.
     */
    public function register()
    {
        if ($this->pluginFileFullPath && file_exists($this->pluginFileFullPath)) {
            register_activation_hook($this->pluginFileFullPath, array($this, 'install'));
            add_filter(
                'plugin_action_links_' . plugin_basename(ALGOL_WISHLIST_PLUGIN_PATH . ALGOL_WISHLIST_PLUGIN_FILE),
                array($this, 'settingsLink')
            );
        }
    }

    public function settingsLink($actions)
    {
        $settingsLink = sprintf(
            '<a href=%s>%s</a>',
            admin_url('admin.php?page=' . AdminPage::SLUG),
            __('Manage', 'wc-wishlist')
        );
        array_unshift($actions, $settingsLink);

        return $actions;
    }

    public function install()
    {
        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        (new WishlistsRepositoryWordpress($wpdb))->createTable();
        (new ItemsOfWishlistRepositoryWordpress($wpdb))->createTable();

        (new WishlistPage())->install();
    }

    /**
     * Method required for tests
     */
    public function uninstall()
    {
        global $wpdb;

        if (awlContext()->getSettings()->getOption("uninstall_remove_data")) {
            (new ItemsOfWishlistRepositoryWordpress($wpdb))->deleteTable();
            (new WishlistsRepositoryWordpress($wpdb))->deleteTable();
            (new StoreStrategy())->truncate();
            (new WishlistPage())->uninstall();
        }
    }
}
