<?php

namespace AlgolWishlist\API\DTO\ItemOfWishlist;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Price",
 * )
 */
class Price
{
    /**
     * @OA\Property(
     *     title="Unit price",
     *     type="number"
     * )
     *
     * @var float
     */
    public $unitPrice;

    /**
     * @OA\Property(
     *     title="Min price",
     *     type="string"
     * )
     *
     * @var string
     */
    public $minPrice;

    /**
     * @OA\Property(
     *     title="Min price value",
     *     type="number"
     * )
     *
     * @var float
     */
    public $minPriceValue;

    /**
     * @OA\Property(
     *     title="Max price",
     *     type="string"
     * )
     *
     * @var string
     */
    public $maxPrice;

    /**
     * @OA\Property(
     *     title="Max price value",
     *     type="number"
     * )
     *
     * @var float
     */
    public $maxPriceValue;

    /**
     * @OA\Property(
     *     title="Price html",
     *     type="string"
     * )
     *
     * @var string
     */
    public $priceHtml;

    /**
     * @param float $unitPrice
     * @param string $minPrice
     * @param float $minPriceValue
     * @param string $maxPrice
     * @param float $maxPriceValue
     * @param string $priceHtml
     */
    public function __construct(
        float $unitPrice,
        string $minPrice,
        float $minPriceValue,
        string $maxPrice,
        float $maxPriceValue,
        string $priceHtml
    ) {
        $this->unitPrice = $unitPrice;
        $this->minPrice = $minPrice;
        $this->minPriceValue = $minPriceValue;
        $this->maxPrice = $maxPrice;
        $this->maxPriceValue = $maxPriceValue;
        $this->priceHtml = $priceHtml;
    }
}
