<?php

namespace AlgolWishlist\API\DTO\ItemOfWishlist;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="StockAvailability",
 * )
 */
class StockAvailability
{
    /**
     * @OA\Property(
     *     title="Availability text based on stock status",
     *     type="string"
     * )
     *
     * @var string
     */
    public $text;

    /**
     * @OA\Property(
     *     title="Availability classname based on stock status",
     *     type="string"
     * )
     *
     * @var string
     */
    public $stockClass;

    /**
     * @param string $text
     * @param string $stockClass
     */
    public function __construct(string $text, string $stockClass)
    {
        $this->text = $text;
        $this->stockClass = $stockClass;
    }
}
