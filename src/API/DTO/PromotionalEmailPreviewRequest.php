<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="PromotionalEmailPreviewRequest",
 * )
 */
class PromotionalEmailPreviewRequest
{
    /**
     * @OA\Property(
     *     title="Type ('html' or 'plain')",
     *     type="string"
     * )
     *
     * @var string|null
     */
    public $type;

    /**
     * @OA\Property(
     *     title="Content",
     *     type="string"
     * )
     *
     * @var string|null
     */
    public $htmlContent;

    /**
     * @OA\Property(
     *     title="Plain content",
     *     type="string"
     * )
     *
     * @var string|null
     */
    public $plainContent;

    /**
     * @OA\Property(
     *     title="Product Id",
     *     @OA\Schema(ref="#/components/schemas/Product")
     * )
     *
     * @var Product
     */
    public $product;

    /**
     * @OA\Property(
     *     title="User Id",
     *     type="number"
     * )
     *
     * @var int|null
     */
    public $userId;

    /**
     * @OA\Property(
     *     title="Coupon",
     *     type="string"
     * )
     *
     * @var string|null
     */
    public $coupon;

    /**
     * @param string|null $type
     * @param string|null $htmlContent
     * @param string|null $plainContent
     * @param Product $product
     * @param int|null $userId
     * @param string|null $coupon
     */
    public function __construct(
        ?string $type,
        ?string $htmlContent,
        ?string $plainContent,
        Product $product,
        ?int $userId,
        ?string $coupon
    ) {
        $this->type = $type;
        $this->htmlContent = $htmlContent;
        $this->plainContent = $plainContent;
        $this->product = $product;
        $this->userId = $userId;
        $this->coupon = $coupon;
    }

    public static function fromBody(array $body): PromotionalEmailPreviewRequest
    {
        return new PromotionalEmailPreviewRequest(
            $body['type'] ?? null,
            $body['htmlContent'] ?? null,
            $body['plainContent'] ?? null,
            Product::fromBody($body['product'] ?? []),
            $body['userId'] ?? null,
            $body['coupon'] ?? null
        );
    }
}
