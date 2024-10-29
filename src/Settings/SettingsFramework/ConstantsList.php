<?php

namespace AlgolWishlist\Settings\SettingsFramework;

use AlgolWishlist\Settings\SettingsFramework\Constants\Constant;
use AlgolWishlist\Settings\SettingsFramework\Exceptions\KeyNotFound;

class ConstantsList
{
    /**
     * @var Constant[]
     */
    protected $list;

    /**
     * @param Constant[] $constants
     */
    public function register(...$constants)
    {
        foreach ($constants as $constant) {
            if ($constant instanceof Constant) {
                $this->list[$constant->getId()] = $constant;
            }
        }
    }

    /**
     * @param string $key
     *
     * @return Constant
     * @throws KeyNotFound
     */
    public function getByKey(string $key)
    {
        if ( ! isset($this->list[$key])) {
            throw new KeyNotFound($key);
        }

        return $this->list[$key];
    }
}
