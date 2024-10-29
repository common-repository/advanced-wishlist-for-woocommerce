<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Product",
 * )
 */
class Product
{
    /**
     * @OA\Property(
     *     title="Product Id",
     *     type="number"
     * )
     *
     * @var int
     */
    public $productId;

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
     * @param int $productId
     * @param array $variation
     */
    public function __construct(int $productId, array $variation)
    {
        $this->productId = $productId;
        $this->variation = $variation;
    }

    public static function fromBody(array $body): Product
    {
        return new Product(
            $body['productId'] ?? null,
            $body['variation'] ?? []
        );
    }
}
