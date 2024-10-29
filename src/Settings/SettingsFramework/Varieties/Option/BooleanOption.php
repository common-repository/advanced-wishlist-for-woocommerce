<?php

namespace AlgolWishlist\Settings\SettingsFramework\Varieties\Option;

use AlgolWishlist\Settings\SettingsFramework\Exceptions\OptionValueFilterFailed;
use AlgolWishlist\Settings\SettingsFramework\Varieties\Option\Abstracts\Option;

class BooleanOption extends Option
{
    const TRUE_VALUES = array(
        '1',
        'on',
        'On',
        'ON',
        'true',
        'True',
        'TRUE',
        'y',
        'Y',
        'yes',
        'Yes',
        'YES',
        1,
        true,
    );

    protected $trueValues = array();

    public function __construct($id)
    {
        parent::__construct($id);

        $this->trueValues = array_combine(self::TRUE_VALUES, array_fill(0, count(self::TRUE_VALUES), ""));
    }

    /**
     * @param mixed $value
     *
     * @return string
     * @throws OptionValueFilterFailed
     */
    protected function sanitize($value)
    {
        return $this->isTrueValue($value);
    }

    /**
     * @param mixed $value
     *
     * @return boolean
     */
    protected function isTrueValue($value)
    {
        return isset($this->trueValues[$value]);
    }
}
