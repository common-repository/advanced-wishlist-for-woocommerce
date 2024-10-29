<?php

namespace AlgolWishlist\User;

use AlgolWishlist\Context;
use AlgolWishlist\Repositories\Wishlists\ShareTypeEnum;
use AlgolWishlist\Repositories\Wishlists\WishlistEntity;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use AlgolWishlist\Repositories\Wishlists\WishlistTokenGenerator;

class UserMetaData
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $defaultWishlistIdOptionName;

    /**
     * @var WishlistsRepositoryWordpress
     */
    private $wishlistRepository;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->context = awlContext();

        $this->defaultWishlistIdOptionName = "awl_default_wishlist";

        global $wpdb;
        $this->wishlistRepository = new WishlistsRepositoryWordpress($wpdb);
    }

    public function setDefaultWishlistId(int $wishlistId)
    {
        if (!$this->userId) {
            return null;
        }

        update_user_meta($this->userId, $this->defaultWishlistIdOptionName, $wishlistId);
    }

    public function getDefaultWishlist(): ?WishlistEntity
    {
        if (!$this->userId) {
            return null;
        }

        global $wpdb;
        $wishlistRepository = new WishlistsRepositoryWordpress($wpdb);

        $wishlistId = (int)get_user_meta($this->userId, $this->defaultWishlistIdOptionName, true);

        try {
            $wishlist = $wishlistRepository->getById($wishlistId);
        } catch (\Exception $e) {
            $this->context->getLogger()->critical(
                "Cannot get default wishlist for userId {$this->userId}! Reason: {$e->getMessage()}"
            );
            $wishlist = null;
        }

        if ($wishlist === null) {
            try {
                if ($wishlist = $wishlistRepository->getFirstByOwnerId($this->userId)) {
                    $this->setDefaultWishlistId($wishlist->id);
                }
            } catch (\Exception $e) {
                $this->context->getLogger()->critical(
                    "Cannot get first wishlist for userId {$this->userId}! Reason: {$e->getMessage()}"
                );
                $wishlist = null;
            }
        }

        if ($wishlist === null) {
            if ($wishlist = $this->createDefaultWishlist()) {
                $this->setDefaultWishlistId($wishlist->id);
            }
        }

        return $wishlist;
    }

    private function createDefaultWishlist(): ?WishlistEntity
    {
        try {
            $wishlist = $this->wishlistRepository->create(
                new WishlistEntity(
                    0,
                    "Default",
                    (new WishlistTokenGenerator())->generate(),
                    $this->userId,
                    null,
                    ShareTypeEnum::PUBLIC(),
                    new \DateTime('now', new \DateTimeZone('UTC'))
                )
            );
        } catch (\Exception $e) {
            $this->context->getLogger()->critical(
                "Cannot create wishlist for userId {$this->userId}! Reason: {$e->getMessage()}"
            );
            $wishlist = null;
        }

        return $wishlist;
    }

}
