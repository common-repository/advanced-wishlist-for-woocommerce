<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="WcCoupon",
 * )
 */
class WcCoupon
{
    /**
     * @OA\Property(
     *     title="Code",
     *     type="string"
     * )
     *
     * @var string
     */
    public $code;

    /**
     * @OA\Property(
     *     title="Title",
     *     type="string"
     * )
     *
     * @var string
     */
    public $title;

    /**
     * @param string $code
     * @param string $title
     */
    public function __construct(string $code, string $title)
    {
        $this->code = $code;
        $this->title = $title;
    }
}
