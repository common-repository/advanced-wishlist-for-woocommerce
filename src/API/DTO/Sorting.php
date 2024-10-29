<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Sorting",
 * )
 */
class Sorting
{
    /**
     * @OA\Property(
     *     title="Field",
     *     type="string"
     * )
     *
     * @var string
     */
    public $field;

    /**
     * @OA\Property(
     *     title="Modifier: DESC or ASC",
     *     type="string"
     * )
     *
     * @var string
     */
    public $sort;

    /**
     * @param string $field
     * @param string $sort
     */
    public function __construct(string $field, string $sort)
    {
        $this->field = $field;
        $this->sort = $sort;
    }

    public function isValid(): bool
    {
        return $this->field !== null && $this->sort !== null;
    }

    public static function fromBody(array $body): Sorting
    {
        return new Sorting(
            $body['field'] ?? null,
            $body['sort'] ?? null
        );
    }
}
