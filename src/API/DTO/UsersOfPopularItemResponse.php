<?php

namespace AlgolWishlist\API\DTO;

use AlgolWishlist\API\DTO\UserOfPopularItemResponse\UserOfPopularItem;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="UsersOfPopularItemResponse",
 * )
 */
class UsersOfPopularItemResponse
{

    /**
     * @OA\Property(
     *     title="Items",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/UserOfPopularItem")
     * )
     *
     * @var array<int, UserOfPopularItem>
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
