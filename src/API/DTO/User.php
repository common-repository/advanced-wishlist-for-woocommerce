<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="User",
 * )
 */
class User
{
    /**
     * @OA\Property(
     *     title="Owner id",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $id;

    /**
     * @OA\Property(
     *     title="Username",
     *     type="string"
     * )
     *
     * @var string
     */
    public $username;

    /**
     * @param int $id
     * @param string $username
     */
    public function __construct(int $id, string $username)
    {
        $this->id = $id;
        $this->username = $username;
    }
}
