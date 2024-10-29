<?php

namespace AlgolWishlist;

use AlgolWishlist\Repositories\Wishlists\WishlistEntity;
use AlgolWishlist\VersionFree\WishlistPageApp\WishlistPage;

class UrlBuilder
{
    public function getUrlToWishlist(WishlistEntity $wishlist): string
    {
        $url = get_permalink((new WishlistPage())->getPageId());
        $url = untrailingslashit($url);

        return $url . "/" . $wishlist->token;
    }

    public function getUrlToDefaultWishlistForCurrentUser()
    {
        return get_permalink((new WishlistPage())->getPageId());
    }
}
