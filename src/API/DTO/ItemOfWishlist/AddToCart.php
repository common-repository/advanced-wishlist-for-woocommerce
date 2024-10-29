<?php

namespace AlgolWishlist\API\DTO\ItemOfWishlist;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="AddToCart",
 * )
 */
class AddToCart
{
    /**
     * @OA\Property(
     *     title="Text",
     *     type="string"
     * )
     *
     * @var string
     */
    public $text;

    /**
     * @OA\Property(
     *     title="Url",
     *     type="string"
     * )
     *
     * @var string
     */
    public $url;

    /**
     * @OA\Property(
     *     title="Requires selection before adding to cart",
     *     type="bool"
     * )
     *
     * @var bool
     */
    public $requiresSelection;

    /**
     * @param string $text
     * @param string $url
     * @param bool $requiresSelection
     */
    public function __construct(string $text, string $url, bool $requiresSelection)
    {
        $this->text = $text;
        $this->url = $url;
        $this->requiresSelection = $requiresSelection;
    }
}
