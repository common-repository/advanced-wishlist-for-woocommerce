<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="WishlistAdminRequest",
 * )
 */
class WishlistAdminRequest
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
     *     title="Owner Id",
     *     type="integer"
     * )
     *
     * @var integer
     */
    public $ownerId;

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
     * @param int $ownerId
     * @param int $shareTypeId
     */
    public function __construct(?int $id, string $title, int $ownerId, int $shareTypeId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->ownerId = $ownerId;
        $this->shareTypeId = $shareTypeId;
    }

    public function isValidForCreation(): bool
    {
        return $this->title !== null && $this->ownerId !== null && $this->shareTypeId !== null;
    }

    public static function fromBody(array $body): WishlistAdminRequest
    {
        return new WishlistAdminRequest(
            $body['id'] ?? null,
            $body['title'] ?? null,
            $body['ownerId'] ?? null,
            $body['shareTypeId'] ?? null
        );
    }
}
