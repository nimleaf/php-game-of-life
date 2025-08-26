<?php declare(strict_types=1);

namespace App\Domain\Model;

use InvalidArgumentException;

/**
 * World represents the immutable state of the Game of Life at a single generation.
 *
 * It keeps track of all live cells in a sparse map keyed by their coordinates ("x:y").
 */
final class World
{
    /** @var array<string, Cell> sparse map keyed by "x:y" */
    private array $alive;

    public function __construct(
        public readonly GridSize $size,
        public readonly int      $speciesCount,
        array                    $aliveCells
    )
    {
        if ($this->speciesCount <= 0) {
            throw new InvalidArgumentException("Value of element 'species' must be positive number");
        }
        $this->alive = [];
        foreach ($aliveCells as $cell) {
            if (!$cell instanceof Cell || !$cell->isAlive()) {
                throw new InvalidArgumentException('World expects alive cells');
            }
            $this->alive[$cell->coordinate->key()] = $cell;
        }
    }

    /**
     * Checks if a cell is alive at the given coordinate.
     */
    public function isAliveAt(Coordinate $c): bool
    {
        return isset($this->alive[$c->key()]);
    }

    /**
     * Returns the species of the cell at the given coordinate, or null if dead.
     */
    public function speciesAt(Coordinate $c): ?SpeciesId
    {
        return $this->alive[$c->key()]->species ?? null;
    }


    /**
     * Returns all live cells as an iterable.
     *
     * @return iterable<Cell>
     */
    public function aliveCells(): iterable
    {
        return $this->alive;
    }
}