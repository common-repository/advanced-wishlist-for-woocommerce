<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Pagination",
 * )
 */
class Pagination
{
    /**
     * @OA\Property(
     *     title="Count of items per page",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $perPage;

    /**
     * @OA\Property(
     *     title="Current page",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $currentPage;

    /**
     * @param int $perPage
     * @param int $currentPage
     */
    public function __construct(int $perPage, int $currentPage)
    {
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    public function isValid(): bool
    {
        return $this->perPage !== null && $this->currentPage !== null;
    }

    public static function fromBody(array $body): Pagination
    {
        return new Pagination(
            $body['perPage'] ?? null,
            $body['currentPage'] ?? null,
        );
    }
}
