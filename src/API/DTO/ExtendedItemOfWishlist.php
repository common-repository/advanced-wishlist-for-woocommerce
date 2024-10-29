<?php

namespace AlgolWishlist\API\DTO;

use AlgolWishlist\API\DTO\ItemOfWishlist\AddToCart;
use AlgolWishlist\API\DTO\ItemOfWishlist\Price;
use AlgolWishlist\API\DTO\ItemOfWishlist\Stock;
use AlgolWishlist\API\DTO\ItemOfWishlist\Thumbnail;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="ExtendedItemOfWishlist",
 * )
 */
class ExtendedItemOfWishlist
{
    /**
     * @OA\Property(
     *     title="Id",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $id;

    /**
     * @OA\Property(
     *     title="Product id",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $productId;

    /**
     * @OA\Property(
     *     title="Parent id",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $parentId;

    /**
     * @OA\Property(
     *     title="Title",
     *     type="string"
     * )
     *
     * @var string
     */
    public $title;

    /**
     * @OA\Property(
     *     title="Type of product",
     *     type="string"
     * )
     *
     * @var string
     */
    public $type;

    /**
     * @OA\Property(
     *     title="Is a virtual product",
     *     type="boolean"
     * )
     *
     * @var bool
     */
    public $virtualProduct;

    /**
     * @OA\Property(
     *     title="Is downloadable",
     *     type="boolean"
     * )
     *
     * @var bool
     */
    public $downloadable;

    /**
     * @OA\Property(
     *     title="SKU",
     *     type="string"
     * )
     *
     * @var string
     */
    public $sku;

    /**
     * @OA\Property(
     *     title="Quantity",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $quantity;

    /**
     * @OA\Property(
     *     title="Priority",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $priority;

    /**
     * @OA\Property(
     *     title="Variation",
     *     type="object"
     * )
     *
     * @var array
     */
    public $variation;

    /**
     * @OA\Property(
     *     title="Cart item data",
     *     type="object"
     * )
     *
     * @var array
     */
    public $cartItemData;

    /**
     * @OA\Property(
     *     title="Date of creation",
     *     @OA\Schema(ref="#/components/schemas/Date")
     * )
     *
     * @var Date
     */
    public $createdAt;

    /**
     * @OA\Property(
     *     title="Permalink",
     *     type="string"
     * )
     *
     * @var string
     */
    public $permalink;

    /**
     * @OA\Property(
     *     title="Thumbnail info",
     *     @OA\Schema(ref="#/components/schemas/Thumbnail")
     * )
     *
     * @var Thumbnail
     */
    public $thumbnail;

    /**
     * @OA\Property(
     *     title="Stock",
     *     @OA\Schema(ref="#/components/schemas/Stock")
     * )
     *
     * @var Stock
     */
    public $stock;

    /**
     * @OA\Property(
     *     title="Price",
     *     @OA\Schema(ref="#/components/schemas/Price")
     * )
     *
     * @var Price
     */
    public $price;

    /**
     * @OA\Property(
     *     title="Price",
     *     @OA\Schema(ref="#/components/schemas/AddToCart")
     * )
     *
     * @var AddToCart
     */
    public $addToCart;

    /**
     * @param int $id
     * @param int $productId
     * @param int $parentId
     * @param string $title
     * @param string $type
     * @param bool $virtualProduct
     * @param bool $downloadable
     * @param string $sku
     * @param int $quantity
     * @param int $priority
     * @param array $variation
     * @param array $cartItemData
     * @param Date $createdAt
     * @param string $permalink
     * @param Thumbnail $thumbnail
     * @param Stock $stock
     * @param Price $price
     * @param AddToCart $addToCart
     */
    public function __construct(
        int $id,
        int $productId,
        int $parentId,
        string $title,
        string $type,
        bool $virtualProduct,
        bool $downloadable,
        string $sku,
        int $quantity,
        int $priority,
        array $variation,
        array $cartItemData,
        Date $createdAt,
        string $permalink,
        Thumbnail $thumbnail,
        Stock $stock,
        Price $price,
        AddToCart $addToCart
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->parentId = $parentId;
        $this->title = $title;
        $this->type = $type;
        $this->virtualProduct = $virtualProduct;
        $this->downloadable = $downloadable;
        $this->sku = $sku;
        $this->quantity = $quantity;
        $this->priority = $priority;
        $this->variation = $variation;
        $this->cartItemData = $cartItemData;
        $this->createdAt = $createdAt;
        $this->permalink = $permalink;
        $this->thumbnail = $thumbnail;
        $this->stock = $stock;
        $this->price = $price;
        $this->addToCart = $addToCart;
    }
}
