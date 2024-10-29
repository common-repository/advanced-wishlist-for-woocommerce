<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="ExecuteActionsOnItemsOfWishlistRequest",
 * )
 */
class ExecuteActionsOnItemsOfWishlistRequest
{
    /**
     * @OA\Property(
     *     title="Action",
     *     type="string"
     * )
     *
     * @var string
     */
    public $action;

    /**
     * @OA\Property(
     *     title="Message",
     *     type="array",
     *     @OA\Items(type="integer")
     * )
     *
     * @var array
     */
    public $itemIds;

    /**
     * @OA\Property(
     *     title="Options for action",
     *     type="object",
     * )
     *
     * @var array
     */
    public $options;

    /**
     * @param string $action
     * @param array $itemIds
     * @param array $options
     */
    public function __construct(string $action, array $itemIds, array $options)
    {
        $this->action = $action;
        $this->itemIds = $itemIds;
        $this->options = $options;
    }

    public function isValid(): bool
    {
        return is_string($this->action) && is_array($this->itemIds);
    }

    public static function fromBody(array $body): ExecuteActionsOnItemsOfWishlistRequest
    {
        return new ExecuteActionsOnItemsOfWishlistRequest(
            $body['action'] ?? null,
            $body['itemIds'] ?? null,
                $body['options'] ?? []
        );
    }
}
