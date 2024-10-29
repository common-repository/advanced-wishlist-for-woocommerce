<?php

namespace AlgolWishlist;

use AlgolWishlist\VersionFree\RewriteRules\WishlistRewriteRule;

class WordpressCurrentRequest
{
    public function getWishlistTokenIfPresent(): ?string
    {
        return get_query_var((new WishlistRewriteRule())->getQueryParam(), null);
    }
}
