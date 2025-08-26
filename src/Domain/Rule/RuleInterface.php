<?php declare(strict_types=1);

namespace App\Domain\Rule;

use App\Domain\Model\{Coordinate, SpeciesId, World};

/**
 * RuleInterface defines the contract for evolution rules in the Game of Life.
 *
 * Implementations determine how the world evolves from one generation to the next.
 */
interface RuleInterface
{
    /**
     * Determines the next state for a given coordinate based on the current world.
     *
     * @param World $world Current world state
     * @param Coordinate $c Coordinate to evaluate
     * @return SpeciesId|null SpeciesId if the cell will be alive, null if dead
     */
    public function willBeAlive(World $world, Coordinate $c): ?SpeciesId; // return species if alive, null if dead


    /**
     * Provides neighbor coordinates for a given cell.
     *
     * @param Coordinate $c Coordinate to get neighbors for
     * @param int $size Size of the grid (for boundary checking)
     * @return iterable<Coordinate> Neighbor coordinates
     */
    public function neighbors(Coordinate $c, int $size): iterable; // of Coordinate
}