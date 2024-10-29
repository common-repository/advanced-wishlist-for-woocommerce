<?php

namespace AlgolWishlist\Repositories\ItemsOfWishlist;

use AlgolWishlist\Repositories\OrderByModifier;

class ProductsOfWishlistOrderBy
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
        $this->availableFields = ["title", "unitPrice", "stockStatus", "createdAt", "priority"];
        $this->list = [];
    }

    public function add(string $field, OrderByModifier $modifier)
    {
        if (!in_array($field, $this->availableFields, true)) {
            throw new \Exception(
                "[ProductsOfWishlistOrderBy] Unsupported field: " . $field . " from list [" . join(",", $this->availableFields) . "]"
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
