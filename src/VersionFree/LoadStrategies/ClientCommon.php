<?php

namespace AlgolWishlist\VersionFree\LoadStrategies;

use AlgolWishlist\Context;
use AlgolWishlist\CustomizerExtensions\CustomizerExtensions;
use AlgolWishlist\VersionFree\WishlistControls\Display as WishlistControlsDisplay;
use AlgolWishlist\VersionFree\WishlistControls\Shortcode as AddToWishlistControlShortcode;
use AlgolWishlist\VersionFree\WishlistPageApp\Shortcode as WishlistPageShortcode;

defined('ABSPATH') or exit;

class ClientCommon implements LoadStrategy
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct()
    {
        $this->context = awlContext();
    }

    public function start()
    {
        (new WishlistControlsDisplay())->register();

        (new WishlistPageShortcode())->register();
        (new AddToWishlistControlShortcode())->register();

        (new CustomizerExtensions())->register();
    }
}
