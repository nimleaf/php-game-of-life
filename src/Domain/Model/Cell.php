<?php declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Model\Enum\CellState;
use InvalidArgumentException;

/**
 * Cell represents a single cell in the Game of Life world.
 *
 * Each cell has a coordinate, a state (alive or dead), and optionally a species.
 * Live cells must have a species assigned.
 */
final class Cell
{
    public function __construct(
        public readonly Coordinate $coordinate,
        public readonly CellState $state,
        public readonly ?SpeciesId $species = null,
    ) {
        if ($this->state === CellState::ALIVE && $this->species === null) {
            throw new InvalidArgumentException('Alive cell must have species');
        }
    }

    /**
     * Creates a dead cell at the given coordinate.
     */
    public static function dead(Coordinate $c): self
    {
        return new self($c, CellState::DEAD, null);
    }

    /**
     * Creates a live cell at the given coordinate with the specified species.
     */
    public static function alive(Coordinate $c, SpeciesId $s): self
    {
        return new self($c, CellState::ALIVE, $s);
    }

    /**
     * Checks if the cell is alive.
     */
    public function isAlive(): bool
    {
        return $this->state === CellState::ALIVE;
    }
}