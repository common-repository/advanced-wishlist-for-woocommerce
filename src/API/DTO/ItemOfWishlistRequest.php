<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="ItemOfWishlistRequest",
 * )
 */
class ItemOfWishlistRequest
{
    /**
     * @OA\Property(
     *     title="Id",
     *     type="integer"
     * )
     *
     * @var int|null
     */
    public $id;

    /**
     * @OA\Property(
     *     title="Wishlist id",
     *     type="integer"
     * )
     *
     * @var int|null
     */
    public $wishlistId;

    /**
     * @OA\Property(
     *     title="Product id",
     *     type="integer"
     * )
     *
     * @var int|null
     */
    public $productId;

    /**
     * @OA\Property(
     *     title="Quantity",
     *     type="integer"
     * )
     *
     * @var int|null
     */
    public $quantity;

    /**
     * @OA\Property(
     *     title="Variation",
     *     type="object"
     * )
     *
     * @var array|null
     */
    public $variation;

    /**
     * @OA\Property(
     *     title="Cart item data",
     *     type="object"
     * )
     *
     * @var array|null
     */
    public $cartItemData;

    /**
     * @OA\Property(
     *     title="Priority",
     *     type="integer"
     * )
     *
     * @var int|null
     */
    public $priority;

    /**
     * @param int|null $id
     * @param int|null $wishlistId
     * @param int|null $productId
     * @param int|null $quantity
     * @param array|null $variation
     * @param array|null $cartItemData
     * @param int|null $priority
     */
    public function __construct(
        ?int $id,
        ?int $wishlistId,
        ?int $productId,
        ?int $quantity,
        ?array $variation,
        ?array $cartItemData,
        ?int $priority
    ) {
        $this->id = $id;
        $this->wishlistId = $wishlistId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->variation = $variation;
        $this->cartItemData = $cartItemData;
        $this->priority = $priority;
    }

    public function isValidForCreation(): bool
    {
        return $this->productId !== null
            && $this->wishlistId !== null
            && $this->quantity !== null
            && $this->variation !== null
            && $this->cartItemData !== null
            && $this->priority !== null;
    }

    public static function fromBody(array $body): ItemOfWishlistRequest
    {
        return new ItemOfWishlistRequest(
            $body['id'] ?? null,
            $body['productId'] ?? null,
            $body['wishlistId'] ?? null,
            $body['quantity'] ?? null,
            $body['variation'] ?? null,
            $body['cartItemData'] ?? null,
            $body['priority'] ?? null
        );
    }
}
