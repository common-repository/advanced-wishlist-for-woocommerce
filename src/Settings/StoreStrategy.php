<?php

namespace AlgolWishlist\Settings;

use AlgolWishlist\Settings\SettingsFramework\Exceptions\KeyNotFound;
use AlgolWishlist\Settings\SettingsFramework\Interfaces\StoreStrategyInterface;
use AlgolWishlist\Settings\SettingsFramework\OptionsList;

defined('ABSPATH') or exit;

class StoreStrategy implements StoreStrategyInterface
{
    const OPTION_KEY = 'algol_wc_wishlist_settings';

    /**
     * @param OptionsList $optionsList
     */
    public function save($optionsList)
    {
        if ($optionsList->getOptionsArray()) {
            update_option(self::OPTION_KEY, $optionsList->getOptionsArray());
            wp_cache_flush();
        }
    }

    /**
     * @param OptionsList $optionsList
     */
    public function load($optionsList)
    {
        $options = get_option(self::OPTION_KEY, array());

        foreach ($options as $key => $value) {
            try {
                $option = $optionsList->getByKey($key);
                $option->set($value);
            } catch (KeyNotFound $exception) {

            }
        }
    }

    public function drop()
    {
        if (function_exists("delete_option")) {
            delete_option(self::OPTION_KEY);
        }
    }

    public function truncate()
    {
        if (function_exists("update_option")) {
            update_option(self::OPTION_KEY, []);
        }
    }
}
