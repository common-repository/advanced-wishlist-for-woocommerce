<?php

namespace AlgolWishlist\Repositories;

use AlgolWishlist\Enum\BaseEnum;

/**
 * @method static self DESC ()
 * @method static self ASC ()
 */
class OrderByModifier extends BaseEnum
{
    const __default = self::DESC;

    const DESC = "DESC";
    const ASC = "ASC";

    /**
     * @param self $variable
     *
     * @return bool
     */
    public function equals($variable)
    {
        return parent::equals($variable);
    }
}