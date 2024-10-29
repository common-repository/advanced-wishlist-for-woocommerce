<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Error",
 * )
 */
class Error
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
     *     title="Message",
     *     type="string"
     * )
     *
     * @var string
     */
    public $message;

    /**
     * @param string $code
     * @param string $message
     */
    public function __construct(string $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }
}
