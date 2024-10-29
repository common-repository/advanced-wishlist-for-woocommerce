<?php

namespace AlgolWishlist\VersionFree\WishlistControls;

use AlgolWishlist\Context;
use AlgolWishlist\CustomizerExtensions\CustomizerExtensions;
use AlgolWishlist\Repositories\ItemsOfWishlist\ItemsOfWishlistRepositoryWordpress;
use AlgolWishlist\UrlBuilder;
use AlgolWishlist\VersionFree\TemplateLoader;

class Shortcode
{
    protected $name = "awl_add_button";

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var CustomizerExtensions
     */
    protected $customizer;

    public function __construct()
    {
        $this->context = awlContext();
        $this->customizer = new CustomizerExtensions();
    }

    public function register()
    {
        add_shortcode($this->name, [$this, 'getContent']);
        add_action("wp_enqueue_scripts", [$this, "registerScriptsAndStyles"]);
    }

    public function registerScriptsAndStyles()
    {
        wp_register_script(
            'algol_wishlist_common',
            ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/js/common.js',
            ['jquery'],
            ALGOL_WISHLIST_VERSION,
            true
        );
        wp_localize_script(
            'algol_wishlist_common',
            'algolWishlistAppData',
            [
                'host' => trailingslashit(get_site_url()),
                'nonce' => wp_create_nonce('wp_rest'),
                'labels' => array(
                    'productAddedToWishlist' => __('Product added to wishlist', 'wc-wishlist'),
                    'productRemovedFromWishlist' => __('Product removed from wishlist', 'wc-wishlist'),
                )
            ]
        );
        wp_register_style(
            'algol_wishlist_style',
            ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/css/style.css',
            [],
            ALGOL_WISHLIST_VERSION
        );

        wp_register_script(
            'awl_api_lib',
            ALGOL_WISHLIST_PLUGIN_URL . '/src/assets/api-client-lib/api-client-lib-build-app.js',
            [],
            ALGOL_WISHLIST_VERSION,
            true
        );
        wp_localize_script(
            'awl_api_lib',
            'awlApiLibConfig',
            [
                'host' => trailingslashit(get_site_url()),
                'nonce' => wp_create_nonce('wp_rest'),
            ]
        );
    }

    public function enqueueScriptsAndStyles()
    {
        wp_enqueue_script('algol_wishlist_common');
        wp_enqueue_script('awl_api_lib');
        wp_enqueue_style('algol_wishlist_style');
    }

    public function getContent($args)
    {
        $this->enqueueScriptsAndStyles();
        $product = $this->getProductFromArgsOtherwiseFromGlobals($args);

        if ($product === null) {
            return;
        }

        $this->printControls($product);
    }

    protected function getProductFromArgsOtherwiseFromGlobals($args)
    {
        global $product;

        $argsProduct = wc_get_product($args['product'] ?? 0);
        $argsProduct = $argsProduct instanceof \WC_Product ? $argsProduct : null;

        return $argsProduct === null ? ($product instanceof \WC_Product ? $product : null) : $argsProduct;
    }

    /**
     * @param \WC_Product $product
     */
    public function printControls($product)
    {
        $productIds = [$product->get_id()];
        if ($product->is_type('variable')) {
            foreach ($product->get_children() as $childId) {
                $productIds[] = $childId;
            }
        }

        global $wpdb;
        $itemsOfWishlistRepositoryWordpress = new ItemsOfWishlistRepositoryWordpress($wpdb);

        $wishlist = $this->context->getCurrentUser()->getDefaultWishlist();

        $wishlistsAndProducts = [];
        $wishlistUrl = (new UrlBuilder())->getUrlToDefaultWishlistForCurrentUser();
        if ($wishlist) {
            try {
                $wishlistsAndProducts = $itemsOfWishlistRepositoryWordpress->getAllByWishlistId($wishlist->id);
            } catch (\Exception $e) {
            }
        }

        $wishlistProductIds = array();
        $isInWishlist = false;
        foreach ($wishlistsAndProducts as $rel) {
            if (in_array($rel->productId, $productIds)) {
                $isInWishlist = true;
                $wishlistProductIds[] = array(
                    'productId' => $rel->productId,
                    'variation' => $rel->variation,
                    'relationshipId' => $rel->id,
                );
            }
        }

        $settings = awlContext()->getSettings();
        $ifProductIsInWishlistSetting = $settings->getOption('product_in_wishlist');

        $themeOptions = $this->customizer->getThemeOptions();

        $shopLoopBtnType = $themeOptions->shopLoopButton->controlType;
        $shopLoopBtnAddText = $themeOptions->shopLoopButton->addToWishlistText;
        $shopLoopBtnViewText = $themeOptions->shopLoopButton->viewWishlistText;
        $shopLoopBtnRemoveText = $themeOptions->shopLoopButton->removeFromWishlistText;
        $shopLoopBtnAddIcon = $themeOptions->shopLoopButton->addToWishlistIcon;
        $shopLoopBtnAddIconCustom = wp_get_attachment_image_url($themeOptions->shopLoopButton->addToWishlistIconCustom);
        $shopLoopBtnViewIcon = $themeOptions->shopLoopButton->viewWishlistIcon;
        $shopLoopBtnViewIconCustom = wp_get_attachment_image_url($themeOptions->shopLoopButton->viewWishlistIconCustom);
        $shopLoopBtnRemoveIcon = $themeOptions->shopLoopButton->removeFromWishlistIcon;
        $shopLoopBtnRemoveIconCustom = wp_get_attachment_image_url($themeOptions->shopLoopButton->removeFromWishlistIconCustom);

        $productPageBtnType = $themeOptions->productPageButton->controlType;
        $productPageBtnAddText = $themeOptions->productPageButton->addToWishlistText;
        $productPageBtnViewText = $themeOptions->productPageButton->viewWishlistText;
        $productPageBtnRemoveText = $themeOptions->productPageButton->removeFromWishlistText;
        $productPageBtnAddIcon = $themeOptions->productPageButton->addToWishlistIcon;
        $productPageBtnAddIconCustom = wp_get_attachment_image_url($themeOptions->productPageButton->addToWishlistIconCustom);
        $productPageBtnViewIcon = $themeOptions->productPageButton->viewWishlistIcon;
        $productPageBtnViewIconCustom = wp_get_attachment_image_url($themeOptions->productPageButton->viewWishlistIconCustom);
        $productPageBtnRemoveIcon = $themeOptions->productPageButton->removeFromWishlistIcon;
        $productPageBtnRemoveIconCustom = wp_get_attachment_image_url($themeOptions->productPageButton->removeFromWishlistIconCustom);

        $isProduct = $this->context->getQueryContext()->isProductPage();

        if ($ifProductIsInWishlistSetting === 'show_remove_from_wishlist') {
            echo TemplateLoader::getTemplate('removeFromWishlistBtn.php', array(
                'display' => $isInWishlist,
                'type' => $isProduct ? $productPageBtnType : $shopLoopBtnType,
                'text' => $isProduct ? $productPageBtnRemoveText : $shopLoopBtnRemoveText,
                'icon' => $isProduct ? $productPageBtnRemoveIcon : $shopLoopBtnRemoveIcon,
                'iconCustom' => $isProduct ? $productPageBtnRemoveIconCustom : $shopLoopBtnRemoveIconCustom,
                'isProduct' => $isProduct,
            ));
        } elseif ($ifProductIsInWishlistSetting === 'show_view_wishlist') {
            echo TemplateLoader::getTemplate('viewWishlistBtn.php', array(
                'wishlistUrl' => $wishlistUrl,
                'display' => $isInWishlist,
                'type' => $isProduct ? $productPageBtnType : $shopLoopBtnType,
                'text' => $isProduct ? $productPageBtnViewText : $shopLoopBtnViewText,
                'icon' => $isProduct ? $productPageBtnViewIcon : $shopLoopBtnViewIcon,
                'iconCustom' => $isProduct ? $productPageBtnViewIconCustom : $shopLoopBtnViewIconCustom,
                'isProduct' => $isProduct,
            ));
        }

        echo TemplateLoader::getTemplate('addToWishlistBtn.php', array(
            'productId' => $product->get_id(),
            'wishlistProductIds' => $wishlistProductIds,
            'display' => !$isInWishlist,
            'type' => $isProduct ? $productPageBtnType : $shopLoopBtnType,
            'text' => $isProduct ? $productPageBtnAddText : $shopLoopBtnAddText,
            'icon' => $isProduct ? $productPageBtnAddIcon : $shopLoopBtnAddIcon,
            'iconCustom' => $isProduct ? $productPageBtnAddIconCustom : $shopLoopBtnAddIconCustom,
            'isProduct' => $isProduct,
        ));
    }

}
