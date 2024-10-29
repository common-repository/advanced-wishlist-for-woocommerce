<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="ExecuteActionsOnItemsOfWishlistResponse",
 * )
 */
class ExecuteActionsOnItemsOfWishlistResponse
{
    /**
     * @OA\Property(
     *     title="List of errors",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/BulkActionMessage")
     * )
     *
     * @var array<int, BulkActionMessage|Error>
     */
    public $messages;

    /**
     * @OA\Property(
     *     title="Items after executing action",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/ExtendedItemOfWishlist")
     * )
     *
     * @var ExtendedItemOfWishlist[]
     */
    public $newItems;

    /**
     * @param array<int, BulkActionMessage|Error> $messages
     * @param ExtendedItemOfWishlist[] $newItems
     */
    public function __construct(array $messages, array $newItems)
    {
        $this->messages = $messages;
        $this->newItems = $newItems;
    }
}
