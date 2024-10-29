<?php

namespace AlgolWishlist\API\DTO\UserOfPopularItemResponse;

use AlgolWishlist\API\DTO\Date;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="UserOfPopularItem",
 * )
 */
class UserOfPopularItem
{

    /**
     * @OA\Property(
     *     title="Id",
     *     type="integer",
     * )
     *
     * @var int
     */
    public $id;

    /**
     * @OA\Property(
     *     title="Thumbnail url",
     *     type="string"
     * )
     *
     * @var string
     */
    public $thumbnailUrl;

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
     * @OA\Property(
     *     title="Date of creation",
     *     @OA\Schema(ref="#/components/schemas/Date")
     * )
     *
     * @var Date
     */
    public $addedOn;

    /**
     * @param int $id
     * @param string $thumbnailUrl
     * @param string $name
     * @param Date $addedOn
     */
    public function __construct(int $id, string $thumbnailUrl, string $name, Date $addedOn)
    {
        $this->id = $id;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->name = $name;
        $this->addedOn = $addedOn;
    }
}
