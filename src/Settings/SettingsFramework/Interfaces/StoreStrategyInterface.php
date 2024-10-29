<?php

namespace AlgolWishlist\Settings\SettingsFramework\Interfaces;

use AlgolWishlist\Settings\SettingsFramework\OptionsList;

interface StoreStrategyInterface
{
    /**
     * @param OptionsList $optionsList
     */
    public function save($optionsList);

    /**
     * @param OptionsList $optionsList
     */
    public function load($optionsList);

    public function drop();

    public function truncate();
}
