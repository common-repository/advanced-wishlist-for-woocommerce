<?php

namespace AlgolWishlist\Repositories\ItemsOfWishlist;

class ItemOfWishlistEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $wishlistId;

    /**
     * @var int
     */
    public $productId;

    /**
     * @var float
     */
    public $quantity;

    /**
     * @var string[]
     */
    public $variation;

    /**
     * @var string[]
     */
    public $cartItemData;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var int
     */
    public $priority;

    /**
     * @param int $id
     * @param int $wishlistId
     * @param int $productId
     * @param float $quantity
     * @param string[] $variation
     * @param string[] $cartItemData
     * @param \DateTime $createdAt
     * @param int $priority
     */
    public function __construct(
        int $id,
        int $wishlistId,
        int $productId,
        float $quantity,
        array $variation,
        array $cartItemData,
        \DateTime $createdAt,
        int $priority
    ) {
        $this->id = $id;
        $this->wishlistId = $wishlistId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->variation = $variation;
        $this->cartItemData = $cartItemData;
        $this->createdAt = $createdAt;
        $this->priority = $priority;
    }

    public static function getListOfOrderByColumns(): array
    {
        return ["id", "wishlistId", "productId", "createdAt", "priority"];
    }
}
