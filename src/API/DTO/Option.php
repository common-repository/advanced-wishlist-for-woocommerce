<?php

namespace AlgolWishlist\API\DTO;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Option",
 * )
 */
class Option
{
    /**
     * @OA\Property(
     *     title="Type",
     *     type="string"
     * )
     *
     * @var string
     */
    public $type;

    /**
     * @OA\Property(
     *     title="Id",
     *     type="string"
     * )
     *
     * @var string
     */
    public $id;

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
     * @OA\Property(
     *     title="Default value"
     * )
     *
     * @var mixed
     */
    public $defaultValue;

    /**
     * @OA\Property(
     *     title="Value"
     * )
     *
     * @var mixed
     */
    public $value;

    /**
     * @OA\Property(
     *     title="Selections",
     *     type="array",
     *     @OA\Items(
     *          type="object",
     *          @OA\Property(property="title", type="string"),
     *          @OA\Property(property="value", type="string")
     *     )
     * )
     *
     * @var array
     */
    public $selections = [];

    /**
     * @OA\Property(
     *     title="Customizer url",
     *     type="array",
     *     @OA\Items(type="object")
     * )
     *
     * @var array
     */
    public $customizeUrls = [];

    /**
     * @param string $type
     * @param string $id
     * @param string $title
     * @param mixed $defaultValue
     * @param mixed $value
     */
    public function __construct(string $type, string $id, string $title, $defaultValue, $value)
    {
        $this->type = $type;
        $this->id = $id;
        $this->title = $title;
        $this->defaultValue = $defaultValue;
        $this->value = $value;
    }
}
