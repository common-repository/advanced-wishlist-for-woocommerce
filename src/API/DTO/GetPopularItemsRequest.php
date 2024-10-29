<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="GetPopularItemsRequest",
 * )
 */
class GetPopularItemsRequest
{

    /**
     * @OA\Property(
     *     title="Time range",
     *     type="string"
     * )
     *
     * @var string
     */
    public $timeRange;

    /**
     * @OA\Property(
     *     title="Serach text",
     *     type="string"
     * )
     *
     * @var string
     */
    public $searchByText;

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
     * @param string $timeRange
     * @param string $searchByText
     * @param Pagination $pagination
     * @param Sorting[] $sorting
     */
    public function __construct(string $timeRange, string $searchByText, Pagination $pagination, array $sorting)
    {
        $this->timeRange = $timeRange;
        $this->searchByText = $searchByText;
        $this->pagination = $pagination;
        $this->sorting = $sorting;
    }

    public static function fromBody(array $body): GetPopularItemsRequest
    {
        $pagination = Pagination::fromBody($body['pagination'] ?? []);
        $pagination = $pagination->isValid() ? $pagination : null;

        $sorting = array_filter(
            array_map(function ($data) {
                $obj = Sorting::fromBody($data);
                return $obj->isValid() ? $obj : null;
            }, $body['sorting'] ?? [])
        );

        return new GetPopularItemsRequest(
            $body['timeRange'] ?? null,
            $body['searchByText'] ?? null,
            $pagination,
            $sorting
        );
    }
}
