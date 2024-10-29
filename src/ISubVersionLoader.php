<?php

namespace AlgolWishlist;

interface ISubVersionLoader
{
    public function initModules();

    public function installRewriteRules(WordpressRewrite $wpRewrite);
}
