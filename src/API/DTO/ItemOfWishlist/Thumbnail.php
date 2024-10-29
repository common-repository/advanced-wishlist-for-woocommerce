<?php

namespace AlgolWishlist\API\DTO\ItemOfWishlist;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Thumbnail",
 * )
 */
class Thumbnail
{
    /**
     * @OA\Property(
     *     title="Preview url",
     *     type="string"
     * )
     *
     * @var string
     */
    public $previewUrl;

    /**
     * @param string $previewUrl
     */
    public function __construct(string $previewUrl)
    {
        $this->previewUrl = $previewUrl;
    }
}
