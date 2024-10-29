<?php

namespace AlgolWishlist;

interface IRewriteRule
{
    public function getQueryParam(): string;

    public function register(): string;
}
