<?php

namespace AlgolWishlist\Repositories\ThemeOptions;

class ThemeModificationsRepository
{
    const OPTION_NAME = 'woocommerce_awl_wishlists';

    public function getModifications()
    {
        return function_exists("get_theme_mod") ? get_theme_mod(self::OPTION_NAME) : [];
    }

    public function drop()
    {
        if (function_exists("remove_theme_mod")) {
            remove_theme_mod(self::OPTION_NAME);
        }
    }

    public function truncate()
    {
        if (function_exists("set_theme_mod")) {
            set_theme_mod(self::OPTION_NAME, []);
        }
    }
}
