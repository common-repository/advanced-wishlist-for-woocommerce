<?php

namespace AlgolWishlist\Settings\SettingsFramework\Varieties\Option\Exceptions;

class ValidateCallbackIsNotCallable extends \Exception
{
    public function errorMessage()
    {
        return 'Validate callback is not callable'; // TODO localize
    }
}
