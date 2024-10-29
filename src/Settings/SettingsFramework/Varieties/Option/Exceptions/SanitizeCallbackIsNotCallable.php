<?php

namespace AlgolWishlist\Settings\SettingsFramework\Varieties\Option\Exceptions;

class SanitizeCallbackIsNotCallable extends \Exception
{
    public function errorMessage()
    {
        return 'Sanitize callback is not callable'; // TODO localize
    }
}
