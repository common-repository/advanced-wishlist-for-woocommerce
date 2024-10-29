<?php

namespace AlgolWishlist\CustomizerExtensions;

defined('ABSPATH') or exit;

class ProductPageButtonProperties
{
    const KEY = "awl_product_page_button";

    /**
     * @var string
     */
    public $buttonPositionAction;

    /**
     * @var string
     */
    public $controlType;

    /**
     * @var string
     */
    public $addToWishlistText;

    /**
     * @var string
     */
    public $viewWishlistText;

    /**
     * @var string
     */
    public $removeFromWishlistText;

    /**
     * @var string
     */
    public $textColor;

    /**
     * @var string
     */
    public $hoverColor;

    /**
     * @var string
     */
    public $addToWishlistIcon;

    /**
     * @var string
     */
    public $addToWishlistIconCustom;

    /**
     * @var string
     */
    public $viewWishlistIcon;

    /**
     * @var string
     */
    public $viewWishlistIconCustom;

    /**
     * @var string
     */
    public $removeFromWishlistIcon;

    /**
     * @var string
     */
    public $removeFromWishlistIconCustom;
}