<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="ItemOfWishlist",
 * )
 */
class ItemOfWishlist
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
     *     title="Wishlist id",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $wishlistId;

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
     *     title="Quantity",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $quantity;

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
     *     title="Priority",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $priority;

    /**
     * @OA\Property(
     *     title="Wishlist URL",
     *     type="string"
     * )
     *
     * @var string
     */
    public $wishlistUrl;

    /**
     * @param int $id
     * @param int $wishlistId
     * @param int $productId
     * @param int $quantity
     * @param array $variation
     * @param array $cartItemData
     * @param Date $createdAt
     * @param int $priority
     * @param string $wishlistUrl
     */
    public function __construct(
        int $id,
        int $wishlistId,
        int $productId,
        int $quantity,
        array $variation,
        array $cartItemData,
        Date $createdAt,
        int $priority,
        string $wishlistUrl
    ) {
        $this->id = $id;
        $this->wishlistId = $wishlistId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->variation = $variation;
        $this->cartItemData = $cartItemData;
        $this->createdAt = $createdAt;
        $this->priority = $priority;
        $this->wishlistUrl = $wishlistUrl;
    }
}
