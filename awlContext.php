<?php

use AlgolWishlist\Context;
use AlgolWishlist\Factory;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!function_exists('awlContext')) {
    function awlContext(): Context {
        static $context;

        if (!$context) {
            $context = apply_filters(
                "algol_wishlist_context_created",
                new Context(
                    Factory::callStaticMethod(
                        "SettingsConfig_OptionsInstaller",
                        "install"
                    )
                )
            );
        }

        return $context;
    }
}
