<?php

namespace AlgolWishlist\VersionFree\SettingsConfig;

use AlgolWishlist\Settings\SettingsFramework\OptionBuilder;
use AlgolWishlist\Settings\SettingsFramework\OptionsList;
use AlgolWishlist\Settings\SettingsFramework\OptionsManager;
use AlgolWishlist\Settings\StoreStrategy;

defined('ABSPATH') or exit;

class OptionsInstaller
{
    public static function install()
    {
        $settings = new OptionsManager(new StoreStrategy());
        $optionsList = new OptionsList();

        static::registerSettings($optionsList);

        $settings->installOptions($optionsList);
        $settings->load();

        return $settings;
    }

    /**
     * @param OptionsList $optionsList
     */
    public static function registerSettings(&$optionsList)
    {
        $builder = new OptionBuilder();

        $optionsList->register(
            $builder::boolean(
                'show_at_product_page',
                true,
                __('Show button at product page', 'wc-wishlist')
            ),
            $builder::boolean(
                'show_at_shop_pages',
                false,
                __('Show button at shop pages', 'wc-wishlist')
            ),
            $builder::selective(
                'product_in_wishlist',
                __('If product already in wishlist', 'wc-wishlist'),
                [
                    "show_remove_from_wishlist" => "Show Remove from Wishlist",
                    "show_view_wishlist" => "Show View Wishlist",
                    "show_add_to_wishlist" => "Show Add to Wishlist",
                ],
                "show_remove_from_wishlist"
            ),
            $builder::boolean(
                'redirect_to_cart',
                false,
                __('Redirect to the cart after adding item from wishlist', 'wc-wishlist')
            ),
            $builder::boolean(
                'remove_if_added_to_cart',
                true,
                __('Remove item from wishlist after adding to the cart', 'wc-wishlist')
            ),
            $builder::boolean(
                'share_wishlist',
                true,
                __('Share wishlist', 'wc-wishlist')
            ),
            $builder::boolean(
                'uninstall_remove_data',
                false,
                __('Remove all data on uninstall', 'wc-wishlist')
            )
        );

        $optionsList->register(
            $builder::selectiveWithCallback(
                'wishlist_page',
                __('Wishlist page', 'wc-wishlist'),
                function () {
                    $pages = get_pages(
                        [
                            'sort_column' => 'menu_order',
                            'sort_order' => 'ASC',
                            'hierarchical' => 0,
                        ]
                    );

                    $options = array();
                    foreach ($pages as $page) {
                        $options[$page->ID] = !empty($page->post_title) ? $page->post_title : '#' . $page->ID;
                    }

                    return $options;
                }
            )
        );
    }
}
