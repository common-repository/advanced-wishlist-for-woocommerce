<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="ReorderItemsOfWishlistRequest",
 * )
 */
class ReorderItemsOfWishlistRequest
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
     * @param array $itemIds
     */
    public function __construct(array $itemIds)
    {
        $this->itemIds = $itemIds;
    }

    public static function fromBody(array $body): ReorderItemsOfWishlistRequest
    {
        return new ReorderItemsOfWishlistRequest($body['itemIds'] ?? []);
    }
}
