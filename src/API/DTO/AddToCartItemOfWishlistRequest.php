<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="AddToCartItemOfWishlistRequest",
 * )
 */
class AddToCartItemOfWishlistRequest
{
    /**
     * @OA\Property(
     *     title="Delete item if it has been added to the cart",
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
     * @param bool $deleteAddedToCart
     * @param bool $withWcNotices
     */
    public function __construct(bool $deleteAddedToCart, bool $withWcNotices)
    {
        $this->deleteAddedToCart = $deleteAddedToCart;
        $this->withWcNotices = $withWcNotices;
    }

    public static function fromBody(array $body): AddToCartItemOfWishlistRequest
    {
        return new AddToCartItemOfWishlistRequest(
            $body['deleteAddedToCart'] ?? false,
            $body['withWcNotices'] ?? false
        );
    }
}