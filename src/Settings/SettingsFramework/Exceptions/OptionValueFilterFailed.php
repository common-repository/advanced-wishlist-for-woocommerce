<?php

namespace AlgolWishlist\Settings\SettingsFramework\Exceptions;

class OptionValueFilterFailed extends \Exception
{
    public function errorMessage()
    {
        return 'Option value filter failed'; // TODO localize
    }
}
