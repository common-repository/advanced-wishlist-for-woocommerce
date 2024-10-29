<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="PromotionalEmailRequest",
 * )
 */
class PromotionalEmailRequest
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
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/Product")
     * )
     *
     * @var array<int, Product>
     */
    public $products;

    /**
     * @OA\Property(
     *     title="User Ids",
     *     type="array",
     *     @OA\Items(type="integer")
     * )
     *
     * @var array<int, int>
     */
    public $userIds;

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
     * @param array $products
     * @param array $userIds
     * @param string|null $coupon
     */
    public function __construct(
        ?string $type,
        ?string $htmlContent,
        ?string $plainContent,
        array $products,
        array $userIds,
        ?string $coupon
    ) {
        $this->type = $type;
        $this->htmlContent = $htmlContent;
        $this->plainContent = $plainContent;
        $this->products = $products;
        $this->userIds = $userIds;
        $this->coupon = $coupon;
    }

    public static function fromBody(array $body): PromotionalEmailRequest
    {
        return new PromotionalEmailRequest(
            $body['type'] ?? null,
            $body['htmlContent'] ?? null,
            $body['plainContent'] ?? null,
            array_map([Product::class, 'fromBody'], $body['products'] ?? []),
            $body['userIds'] ?? [],
            $body['coupon'] ?? null
        );
    }
}
