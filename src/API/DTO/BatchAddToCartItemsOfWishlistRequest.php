<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="BatchAddToCartItemsOfWishlistRequest",
 * )
 */
class BatchAddToCartItemsOfWishlistRequest
{
    /**
     * @OA\Property(
     *     title="Message",
     *     type="array",
     *     @OA\Items(type="integer")
     * )
     *
     * @var array
     */
    public $itemIds;

    /**
     * @OA\Property(
     *     title="Delete items that have been added to the cart",
     *     type="boolean"
     * )
     *
     * @var bool
     */
    public $deleteAddedToCart;

    /**
     * @OA\Property(
     *     title="Show WC notices",
     *     type="boolean"
     * )
     *
     * @var bool
     */
    public $withWcNotices;

    /**
     * @param array $itemIds
     * @param bool $deleteAddedToCart
     * @param bool $withWcNotices
     */
    public function __construct(array $itemIds, bool $deleteAddedToCart, bool $withWcNotices)
    {
        $this->itemIds = $itemIds;
        $this->deleteAddedToCart = $deleteAddedToCart;
        $this->withWcNotices = $withWcNotices;
    }

    public static function fromBody(array $body): BatchAddToCartItemsOfWishlistRequest
    {
        return new BatchAddToCartItemsOfWishlistRequest(
            $body['itemIds'] ?? [],
            $body['deleteAddedToCart'] ?? false,
            $body['withWcNotices'] ?? false
        );
    }
}
