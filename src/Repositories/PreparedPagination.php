<?php

namespace AlgolWishlist\Repositories;

class PreparedPagination
{
    /**
     * @var int
     */
    protected $itemsPerPage;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @param int $itemsPerPage
     * @param int $currentPage
     */
    public function __construct(int $itemsPerPage, int $currentPage)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = $currentPage;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
}