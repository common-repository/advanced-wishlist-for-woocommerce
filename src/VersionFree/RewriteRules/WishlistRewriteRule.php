<?php

namespace AlgolWishlist\VersionFree\RewriteRules;

use AlgolWishlist\IRewriteRule;
use AlgolWishlist\VersionFree\WishlistPageApp\WishlistPage;

class WishlistRewriteRule implements IRewriteRule
{
    /**
     * @var string
     */
    protected $queryParam;

    public function __construct()
    {
        $this->queryParam = "wishlist";
    }

    public function getQueryParam(): string
    {
        return $this->queryParam;
    }

    public function register(): string
    {
        $wishlistPageSlug = (new WishlistPage())->getPageSlug();
        if ( $wishlistPageSlug === "" ) {
            return "";
        }

        $pattern = '(([^/]+/)*' . urldecode($wishlistPageSlug) . ')(/(.*))?/?$';

        add_rewrite_rule(
            $pattern,
            'index.php?pagename=$matches[1]&' . $this->queryParam . '=$matches[4]',
            'top'
        );

        return $pattern;
    }
}
