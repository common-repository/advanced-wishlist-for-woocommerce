<?php

namespace AlgolWishlist\Repositories\Wishlists;

class WishlistEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $token;

    /**
     * @var int
     */
    public $ownerId;

    /**
     * @var ShareTypeEnum
     */
    public $shareType;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var string
     */
    public $sessionKey;

    public function __construct(
        int $id,
        string $title,
        string $token,
        ?int $ownerId,
        ?string $sessionKey,
        ShareTypeEnum $shareType,
        \DateTime $createdAt
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->token = $token;
        $this->ownerId = $ownerId;
        $this->sessionKey = $sessionKey;
        $this->shareType = $shareType;
        $this->createdAt = $createdAt;
    }

    public static function getListOfOrderByColumns(): array
    {
        return ["id", "title", "ownerId", "shareType", "createdAt"];
    }
}
