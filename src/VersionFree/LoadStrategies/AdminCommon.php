<?php

namespace AlgolWishlist\VersionFree\LoadStrategies;

use AlgolWishlist\Context;

defined('ABSPATH') or exit;

class AdminCommon implements LoadStrategy
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct()
    {
        $this->context = awlContext();
    }

    public function withContext(Context $context)
    {
        $this->context = $context;
    }

    public function start()
    {

    }
}
