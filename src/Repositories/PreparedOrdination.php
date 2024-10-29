<?php

namespace AlgolWishlist\Repositories;

class PreparedOrdination
{
    /**
     * @var string[]
     */
    protected $columnNames;

    /**
     * @var OrderByModifier
     */
    protected $order;

    /**
     * @param string[] $columnNames
     * @param OrderByModifier $order
     */
    public function __construct(array $columnNames, OrderByModifier $order)
    {
        $this->columnNames = $columnNames;
        $this->order = $order;
    }

    /**
     * @return string[]
     */
    public function getColumnNames(): array
    {
        return $this->columnNames;
    }

    public function getOrder(): OrderByModifier
    {
        return $this->order;
    }
}