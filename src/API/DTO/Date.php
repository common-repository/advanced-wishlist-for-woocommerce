<?php

namespace AlgolWishlist\API\DTO;

use DateTimeInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Date",
 * )
 */
class Date
{
    /**
     * @OA\Property(
     *     title="Date",
     *     type="string"
     * )
     *
     * @var string
     */
    public $date;

    /**
     * @OA\Property(
     *     title="Timezone code",
     *     type="string"
     * )
     *
     * @var string
     */
    public $timezone;

    /**
     * @param string $date
     * @param string $timezone
     */
    public function __construct(string $date, string $timezone)
    {
        $this->date = $date;
        $this->timezone = $timezone;
    }

    public static function fromDateTime(\DateTime $dateTime): Date
    {
        return new self(
            $dateTime->format(DateTimeInterface::RFC3339),
            $dateTime->getTimezone()->getName()
        );
    }
}
