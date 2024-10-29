<?php

namespace AlgolWishlist\Repositories\Wishlists;

class WishlistTokenGenerator
{
    public function generate()
    {
        return strtoupper(bin2hex(random_bytes(7)));
    }
}
