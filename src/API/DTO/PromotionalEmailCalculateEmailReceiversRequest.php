<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="PromotionalEmailCalculateEmailReceiversRequest",
 * )
 */
class PromotionalEmailCalculateEmailReceiversRequest
{
    /**
     * @OA\Property(
     *     title="Product Id",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/Product")
     * )
     *
     * @var array<int, Product>
     */
    public $products;

    /**
     * @OA\Property(
     *     title="User Ids",
     *     type="array",
     *     @OA\Items(type="integer")
     * )
     *
     * @var array<int, int>
     */
    public $userIds;

    /**
     * @param Product[] $products
     * @param int[] $userIds
     */
    public function __construct(array $products, array $userIds)
    {
        $this->products = $products;
        $this->userIds = $userIds;
    }

    public static function fromBody(array $body): PromotionalEmailCalculateEmailReceiversRequest
    {
        return new PromotionalEmailCalculateEmailReceiversRequest(
            $body['product'] ?? [],
            $body['userIds'] ?? []
        );
    }
}
