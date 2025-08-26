<?php declare(strict_types=1);

namespace App\Domain\Model;

use InvalidArgumentException;

/**
 * Coordinate represents a 2D position on the grid in the Game of Life.
 *
 * Each coordinate has a non-negative X and Y value.
 * It is typically used as the position of a Cell.
 */
final class Coordinate
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {
        if ($x < 0 || $y < 0) {
            throw new InvalidArgumentException('Coordinates must be non-negative');
        }
    }

    /** Returns a unique string key representation, useful for hashing or array indexing. */
    public function key(): string
    {
        return $this->x . ':' . $this->y;
    }
}