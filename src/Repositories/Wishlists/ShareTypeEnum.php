<?php

namespace AlgolWishlist\Repositories\Wishlists;

use AlgolWishlist\Enum\BaseEnum;

/**
 * @method static self PRIVATE ()
 * @method static self PUBLIC ()
 */
class ShareTypeEnum extends BaseEnum
{
    const __default = self::PRIVATE;

    const PUBLIC = 0;
    const PRIVATE = 1;

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
