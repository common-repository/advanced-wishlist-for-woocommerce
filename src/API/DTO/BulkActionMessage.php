<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="BulkActionMessage",
 * )
 */
class BulkActionMessage
{
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';

    /**
     * @OA\Property(
     *     title="Message type",
     *     type="string"
     * )
     *
     * @var string
     */
    public $type;

    /**
     * @OA\Property(
     *     title="Entity Id",
     *     type="integer"
     * )
     *
     * @var int
     */
    public $entityId;

    /**
     * @OA\Property(
     *     title="Entity title",
     *     type="string"
     * )
     *
     * @var string
     */
    public $entityTitle;

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
     * @param string $type
     * @param int $entityId
     * @param string $entityTitle
     * @param string $code
     * @param string $message
     */
    public function __construct(string $type, int $entityId, string $entityTitle, string $code, string $message)
    {
        $this->type = $type;
        $this->entityId = $entityId;
        $this->entityTitle = $entityTitle;
        $this->code = $code;
        $this->message = $message;
    }
}
