<?php

namespace AlgolWishlist\Settings\SettingsFramework\Exceptions;

class EnvironmentKeyNotFound extends \Exception
{
    public function errorMessage()
    {
        return 'Environment key not found'; // TODO localize
    }
}
