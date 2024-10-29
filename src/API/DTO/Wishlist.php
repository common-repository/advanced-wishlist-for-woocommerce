<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Wishlist",
 * )
 */
class Wishlist
{
    /**
     * @OA\Property(
     *     title="Id",
     *     type="integer"
     * )
     *
     * @var int
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
     *     title="Token",
     *     type="string"
     * )
     *
     * @var string
     */
    public $token;

    /**
     * @OA\Property(
     *     title="Owner",
     *     @OA\Schema(ref="#/components/schemas/User")
     * )
     *
     * @var User
     */
    public $owner;

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
     * @OA\Property(
     *     title="Date of creation",
     *     @OA\Schema(ref="#/components/schemas/Date")
     * )
     *
     * @var Date
     */
    public $dateCreated;

    /**
     * @param int $id
     * @param string $title
     * @param string $token
     * @param User $owner
     * @param int $shareTypeId
     * @param Date $dateCreated
     */
    public function __construct(int $id, string $title, string $token, User $owner, int $shareTypeId, Date $dateCreated)
    {
        $this->id = $id;
        $this->title = $title;
        $this->token = $token;
        $this->owner = $owner;
        $this->shareTypeId = $shareTypeId;
        $this->dateCreated = $dateCreated;
    }
}
