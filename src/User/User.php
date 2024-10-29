<?php

namespace AlgolWishlist\User;

use AlgolWishlist\Context;
use AlgolWishlist\Repositories\Wishlists\WishlistEntity;

class User
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $sessionKey;

    private function __construct()
    {
    }

    public static function initFromGlobals(Context $context): User
    {
        $user = new User();
        $user->context = $context;

        if (get_current_user_id()) {
            $user->userId = (int)get_current_user_id();
            $user->sessionKey = "";
        } else {
            $user->userId = 0;

            add_action("woocommerce_init", function() use ($user) {
                if (WC()->session && WC()->session->has_session()) {
                    $user->sessionKey = (string)WC()->session->get_customer_unique_id();
                }
            });

            $user->sessionKey = "";
        }

        return $user;
    }

    private static function generateAndSaveAndReturnUserSessionKey(): string
    {
        if ( headers_sent() ) {
            return "";
        }

        if (WC()->session === null) {
            wc_load_cart();
        }

        if (!WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);

            $reflectionClass    = new \ReflectionClass(WC()->session);
            $reflectionProperty = $reflectionClass->getProperty('_dirty');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue(WC()->session, true);

            WC()->session->save_data();
        }

        return (string)WC()->session->get_customer_unique_id();
    }

    public function isGuest(): bool
    {
        return $this->userId === 0;
    }

    /**
     * @return string
     */
    public function getSessionKey(): string
    {
        if ($this->sessionKey === "") {
            $this->sessionKey = self::generateAndSaveAndReturnUserSessionKey();
        }

        return $this->sessionKey;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getDefaultWishlist(): ?WishlistEntity
    {
        if ($this->isGuest()) {
            $wishlist = (new GuestMetaData($this->getSessionKey()))->getDefaultWishlist();
        } else {
            $wishlist = (new UserMetaData($this->getUserId()))->getDefaultWishlist();
        }

        return $wishlist;
    }
}
