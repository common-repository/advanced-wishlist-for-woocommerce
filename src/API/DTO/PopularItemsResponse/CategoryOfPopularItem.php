<?php

namespace AlgolWishlist\API\DTO\PopularItemsResponse;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="CategoryOfPopularItem",
 * )
 */
class CategoryOfPopularItem
{
    /**
     * @OA\Property(
     *     title="Product id",
     *     type="integer"
     * )
     *
     * @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *     title="Name",
     *     type="string"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
