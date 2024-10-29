<?php

namespace AlgolWishlist\API\DTO\ItemOfWishlist;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Stock",
 * )
 */
class Stock
{
    /**
     * @OA\Property(
     *     title="Availability",
     *     @OA\Schema(ref="#/components/schemas/StockAvailability")
     * )
     *
     * @var StockAvailability
     */
    public $availability;

    /**
     * @param StockAvailability $availability
     */
    public function __construct(StockAvailability $availability)
    {
        $this->availability = $availability;
    }
}
