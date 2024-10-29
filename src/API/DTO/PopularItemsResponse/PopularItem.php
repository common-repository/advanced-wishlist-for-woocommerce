<?php

namespace AlgolWishlist\API\DTO\PopularItemsResponse;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="PopularItem",
 * )
 */
class PopularItem
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
     *     title="Title",
     *     type="string"
     * )
     *
     * @var string
     */
    public $title;

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
     * @OA\Property(
     *     title="Cart item data",
     *     type="object"
     * )
     *
     * @var array
     */
    public $cartItemData;

    /**
     * @OA\Property(
     *     title="Categories",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/CategoryOfPopularItem")
     * )
     *
     * @var array
     */
    public $categories;

    /**
     * @OA\Property(
     *     title="Count of items",
     *     type="integer",
     * )
     *
     * @var int
     */
    public $totalCount;

    /**
     * @OA\Property(
     *     title="Link to product",
     *     type="string"
     * )
     *
     * @var string
     */
    public $link;

    /**
     * @param int $id
     * @param string $title
     * @param array $variation
     * @param array $cartItemData
     * @param array $categories
     * @param int $totalCount
     * @param string $link
     */
    public function __construct(
        int $id,
        string $title,
        array $variation,
        array $cartItemData,
        array $categories,
        int $totalCount,
        string $link
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->variation = $variation;
        $this->cartItemData = $cartItemData;
        $this->categories = $categories;
        $this->totalCount = $totalCount;
        $this->link = $link;
    }


}
