<?php

namespace AlgolWishlist\VersionFree\LoadStrategies;

use AlgolWishlist\Context;

defined('ABSPATH') or exit;

class PhpUnit implements LoadStrategy
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct()
    {
        $this->context = awlContext();
    }

    public function start()
    {
        // do nothing!
    }
}
