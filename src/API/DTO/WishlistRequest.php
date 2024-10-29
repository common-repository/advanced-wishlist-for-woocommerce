<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="WishlistRequest",
 * )
 */
class WishlistRequest
{
    /**
     * @OA\Property(
     *     title="Id",
     *     type="integer"
     * )
     *
     * @var int|null
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
     *     title="Share type Id",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $shareTypeId;

    /**
     * @param int|null $id
     * @param string $title
     * @param int $shareTypeId
     */
    public function __construct(?int $id, string $title, int $shareTypeId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->shareTypeId = $shareTypeId;
    }

    public function isValidForCreation(): bool
    {
        return $this->title !== null && $this->shareTypeId !== null;
    }

    public static function fromBody(array $body): WishlistRequest
    {
        return new WishlistRequest(
            $body['id'] ?? null,
            $body['title'] ?? null,
            $body['shareTypeId'] ?? null
        );
    }
}
