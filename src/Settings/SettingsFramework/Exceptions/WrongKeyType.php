<?php

namespace AlgolWishlist\Settings\SettingsFramework\Exceptions;

class WrongKeyType extends \Exception
{
    public function errorMessage()
    {
        return 'Wrong key type'; // TODO localize
    }
}
