<?php

namespace AlgolWishlist\VersionFree;

use AlgolWishlist\CustomizerExtensions\CustomizerExtensions;
use AlgolWishlist\ISubVersionLoader;
use AlgolWishlist\VersionFree\RewriteRules\WishlistRewriteRule;
use AlgolWishlist\VersionFree\SettingsConfig\OptionsInstaller;
use AlgolWishlist\VersionFree\WishlistPageApp\Shortcode as WishlistPageShortcode;
use AlgolWishlist\VersionFree\WishlistPageApp\WishlistPage;
use AlgolWishlist\WordpressRewrite;

class Loader implements ISubVersionLoader
{
    public function initModules()
    {
    }

    public function installRewriteRules(WordpressRewrite $wpRewrite)
    {
        $wpRewrite->addRewriteRule(new WishlistRewriteRule());
    }
}
