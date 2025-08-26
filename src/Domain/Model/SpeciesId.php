<?php declare(strict_types=1);

namespace App\Domain\Model;
use InvalidArgumentException;

/**
 * SpeciesId represents the identifier of a species in the Game of Life.
 *
 * It is a value object that ensures the species ID is non-negative.
 */
final class SpeciesId
{
    public function __construct(public readonly int $value)
    {
        if ($this->value < 0) {
            throw new InvalidArgumentException('SpeciesId must be >= 0');
        }
    }
}