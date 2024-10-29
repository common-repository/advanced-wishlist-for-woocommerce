<?php

namespace AlgolWishlist\User;

use AlgolWishlist\Context;
use AlgolWishlist\Repositories\Wishlists\ShareTypeEnum;
use AlgolWishlist\Repositories\Wishlists\WishlistEntity;
use AlgolWishlist\Repositories\Wishlists\WishlistsRepositoryWordpress;
use AlgolWishlist\Repositories\Wishlists\WishlistTokenGenerator;

class GuestMetaData
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var string
     */
    private $sessionKey;

    /**
     * @var WishlistsRepositoryWordpress
     */
    private $wishlistRepository;

    public function __construct(string $sessionKey)
    {
        $this->sessionKey = $sessionKey;
        $this->context = awlContext();

        global $wpdb;
        $this->wishlistRepository = new WishlistsRepositoryWordpress($wpdb);
    }

    public function getDefaultWishlist(): ?WishlistEntity
    {
        if ( $this->sessionKey === "" ) {
            return null;
        }

        try {
            $wishlist = $this->wishlistRepository->getFirstBySessionKey($this->sessionKey);
        } catch (\Exception $e) {
            $this->context->getLogger()->critical("Cannot get default wishlist for guest! Reason: {$e->getMessage()}");
            $wishlist = null;
        }

        if ($wishlist === null) {
            $wishlist = $this->createDefaultWishlist();
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
                    null,
                    $this->sessionKey,
                    ShareTypeEnum::PUBLIC(),
                    new \DateTime('now', new \DateTimeZone('UTC'))
                )
            );
        } catch (\Exception $e) {
            $this->context->getLogger()->critical("Cannot create wishlist for guest! Reason: {$e->getMessage()}");
            $wishlist = null;
        }

        return $wishlist;
    }
}
