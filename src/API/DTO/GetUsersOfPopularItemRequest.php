<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="GetUsersOfPopularItemRequest",
 * )
 */
class GetUsersOfPopularItemRequest
{

    /**
     * @OA\Property(
     *     title="Product id",
     *     type="integer"
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
     * @OA\Property(
     *     title="Pagination",
     *     @OA\Schema(ref="#/components/schemas/Pagination")
     * )
     *
     * @var Pagination
     */
    public $pagination;

    /**
     * @OA\Property(
     *     title="Items",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/Sorting")
     * )
     *
     * @var array<int, Sorting>
     */
    public $sorting;

    /**
     * @param int $productId
     * @param array $variation
     * @param Pagination $pagination
     * @param Sorting[] $sorting
     */
    public function __construct(int $productId, array $variation, Pagination $pagination, array $sorting)
    {
        $this->productId = $productId;
        $this->variation = $variation;
        $this->pagination = $pagination;
        $this->sorting = $sorting;
    }

    public static function fromBody(array $body): GetUsersOfPopularItemRequest
    {
        $pagination = Pagination::fromBody($body['pagination'] ?? []);
        $pagination = $pagination->isValid() ? $pagination : null;

        $sorting = array_filter(
            array_map(function ($data) {
                $obj = Sorting::fromBody($data);
                return $obj->isValid() ? $obj : null;
            }, $body['sorting'] ?? [])
        );

        return new GetUsersOfPopularItemRequest(
            $body['productId'] ?? null,
            $body['variation'] ?? null,
            $pagination,
            $sorting
        );
    }
}
