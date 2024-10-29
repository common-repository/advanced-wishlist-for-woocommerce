<?php

namespace AlgolWishlist\Repositories\Wishlists;

use AlgolWishlist\Repositories\OrderByModifier;

class WishlistsOrderBy
{
    /**
     * @var array
     */
    private $availableFields;

    /**
     * @var array
     */
    private $list;

    public function __construct()
    {
        $this->availableFields = ["name", "username", "shareType", "dateCreated", "itemsCount"];
        $this->list = [];
    }

    public function add(string $field, OrderByModifier $modifier)
    {
        if (!in_array($field, $this->availableFields, true)) {
            throw new \Exception(
                "[WishlistsOrderBy] Unsupported field: " . $field . " from list [" . join(",", $this->availableFields) . "]"
            );
        }

        $this->list[] = [
            "field" => $field,
            "sort" => $modifier,
        ];
    }

    public function getList(): array
    {
        return $this->list;
    }
}
