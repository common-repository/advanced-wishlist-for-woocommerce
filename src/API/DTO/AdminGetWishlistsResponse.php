<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="AdminGetWishlistsResponse",
 * )
 */
class AdminGetWishlistsResponse
{

    /**
     * @OA\Property(
     *     title="Items",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/AdminGetWishlistsElement")
     * )
     *
     * @var array<int, AdminGetWishlistsElement>
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
