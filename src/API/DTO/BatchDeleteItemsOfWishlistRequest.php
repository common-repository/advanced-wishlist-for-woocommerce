<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="BatchDeleteItemsOfWishlistRequest",
 * )
 */
class BatchDeleteItemsOfWishlistRequest
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

    public static function fromBody(array $body): BatchDeleteItemsOfWishlistRequest
    {
        return new BatchDeleteItemsOfWishlistRequest($body['itemIds'] ?? []);
    }
}
