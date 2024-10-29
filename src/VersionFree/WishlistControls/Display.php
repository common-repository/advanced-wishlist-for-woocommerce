<?php

namespace AlgolWishlist\VersionFree\WishlistControls;

use AlgolWishlist\Context;
use AlgolWishlist\CustomizerExtensions\CustomizerExtensions;
use AlgolWishlist\VersionFree\TemplateLoader;

class Display
{
    /**
     * @var CustomizerExtensions
     */
    protected $customizer;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Shortcode
     */
    protected $shortCode;

    public function __construct()
    {
        $this->customizer = new CustomizerExtensions();
        $this->context = awlContext();

        $this->shortCode = new Shortcode();
    }

    public function register()
    {
        $settings = awlContext()->getSettings();
        add_action('wp_loaded', function () use ($settings) {
            $themeOptions = $this->customizer->getThemeOptions();
            $shopLoopBtnAction = $themeOptions->shopLoopButton->buttonPositionAction;
            if (strrpos($shopLoopBtnAction, ':') !== false) {
                $shopLoopBtnHook = substr($shopLoopBtnAction, 0, strrpos($shopLoopBtnAction, ':'));
                $shopLoopBtnPriority = substr($shopLoopBtnAction, strrpos($shopLoopBtnAction, ':') + 1);
            } else {
                $shopLoopBtnHook = $shopLoopBtnAction;
                $shopLoopBtnPriority = 10;
            }
            $productPageBtnAction = $themeOptions->productPageButton->buttonPositionAction;

            if ($settings->getOption('show_at_shop_pages')) {
                add_action($shopLoopBtnHook, array($this, 'installWishlistButton'), $shopLoopBtnPriority);
            }
            if ($settings->getOption('show_at_product_page')) {
                add_action($productPageBtnAction, array($this, 'installWishlistButton'));
            }
        });

        if ($settings->getOption('show_wishlist_notices')) {
            add_action('wp_body_open', function () {
                echo TemplateLoader::getTemplate('popupMessage.php');
            });
        }

        add_action('wp_print_styles', [$this->shortCode, 'enqueueScriptsAndStyles']);
    }

    public function installWishlistButton()
    {
        global $product;

        if ( ! $product ) {
            return;
        }

        (new Shortcode())->printControls($product);
    }
}
