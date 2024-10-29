<?php

namespace AlgolWishlist\VersionFree\LoadStrategies;

use AlgolWishlist\Context;

defined('ABSPATH') or exit;

class CustomizePreview implements LoadStrategy
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
        (new ClientCommon())->start();
        (new AdminAjax())->start();
    }
}
