<?php declare(strict_types=1);

namespace App\Domain\Model;
use InvalidArgumentException;

/**
 * GridSize represents the size of the Game of Life world.
 *
 * It defines the number of cells per side of the square grid.
 */
final class GridSize
{
    public function __construct(public readonly int $cells)
    {
        if ($cells <= 0) {
            throw new InvalidArgumentException("Value of element 'cells' must be positive number");
        }
    }
}