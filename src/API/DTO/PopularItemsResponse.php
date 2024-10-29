<?php

namespace AlgolWishlist\API\DTO;

use AlgolWishlist\API\DTO\PopularItemsResponse\PopularItem;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="PopularItemsResponse",
 * )
 */
class PopularItemsResponse
{

    /**
     * @OA\Property(
     *     title="Items",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/PopularItem")
     * )
     *
     * @var array<int, PopularItem>
     */
    public $items;

    /**
     * @OA\Property(
     *     title="Total items count",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $total;

    /**
     * @param array $items
     * @param int $total
     */
    public function __construct(array $items, int $total)
    {
        $this->items = $items;
        $this->total = $total;
    }

}
