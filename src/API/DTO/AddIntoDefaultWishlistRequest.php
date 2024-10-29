<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="AddIntoDefaultWishlistRequest",
 * )
 */
class AddIntoDefaultWishlistRequest
{
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
    public $quantity = 1;

    /**
     * @OA\Property(
     *     title="Variation",
     *     type="object"
     * )
     *
     * @var array
     */
    public $variation = [];

    /**
     * @OA\Property(
     *     title="Cart item data",
     *     type="object"
     * )
     *
     * @var array
     */
    public $cartItemData = [];

    /**
     * @OA\Property(
     *     title="Priority",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $priority = 0;

    /**
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    public static function fromBody(array $body): AddIntoDefaultWishlistRequest
    {
        $obj = new AddIntoDefaultWishlistRequest(
            $body['productId'] ?? 0
        );

        $obj->quantity = $body['quantity'] ?? 1;
        $obj->variation = $body['variation'] ?? [];
        $obj->cartItemData = $body['cartItemData'] ?? [];
        $obj->priority = $body['priority'] ?? 0;

        return $obj;
    }
}
