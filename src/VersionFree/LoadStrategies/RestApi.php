<?php

namespace AlgolWishlist\VersionFree\LoadStrategies;

use AlgolWishlist\Context;
use AlgolWishlist\API\Controllers\AdminPopularItems as RestApiAdminPopularItems;
use AlgolWishlist\API\Controllers\AdminPromotionalEmailController as RestApiAdminPromotionalEmailController;
use AlgolWishlist\API\Controllers\AdminWcCoupons as RestApiAdminWcCoupons;
use AlgolWishlist\API\Controllers\AdminWishlists as RestApiAdminWishlistsController;
use AlgolWishlist\API\Controllers\ItemsOfWishlist as RestApiWishlistsAndProductsController;
use AlgolWishlist\API\Controllers\AdminSettingsController as RestApiAdminSettingsController;
use AlgolWishlist\API\Controllers\Wishlists as RestApiWishlistsController;

defined('ABSPATH') or exit;

class RestApi implements LoadStrategy
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
        add_action('rest_api_init', [new RestApiWishlistsController(), 'registerRoutes']);
        add_action('rest_api_init', [new RestApiWishlistsAndProductsController(), 'registerRoutes']);

        add_action('rest_api_init', [new RestApiAdminWishlistsController(), 'registerRoutes']);
        add_action('rest_api_init', [new RestApiAdminPopularItems(), 'registerRoutes']);
        add_action('rest_api_init', [new RestApiAdminPromotionalEmailController(), 'registerRoutes']);

        add_action('rest_api_init', [new RestApiAdminSettingsController(), 'registerRoutes']);
        add_action('rest_api_init', [new RestApiAdminWcCoupons(), 'registerRoutes']);
    }
}
