<?php

namespace AlgolWishlist\CustomizerExtensions;

defined('ABSPATH') or exit;

class ThemeProperties
{
    /**
     * @var ShopLoopButtonProperties
     */
    public $shopLoopButton;

    /**
     * @var ProductPageButtonProperties
     */
    public $productPageButton;

    /**
     * @var ExactWishlistProperties
     */
    public $exactWishlist;

    public function __construct() {
        $this->shopLoopButton = new ShopLoopButtonProperties();
        $this->productPageButton = new ProductPageButtonProperties();
        $this->exactWishlist = new ExactWishlistProperties();
    }

    /**
     * @param array $properties
     *
     * @return self
     */
    public static function create(array $properties)
    {
        $obj = new self();

        return $obj;
    }
}
