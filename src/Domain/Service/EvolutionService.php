<?php declare(strict_types=1);

namespace App\Domain\Service;
use App\Domain\Model\{World, GridSize, Coordinate, Cell, SpeciesId};
use App\Domain\Rule\RuleInterface;

/**
 * EvolutionService is responsible for evolving a World to the next generation.
 *
 * It applies the configured RuleInterface to each cell in the grid
 * to determine which cells will be alive in the next generation.
 */
final class EvolutionService
{
    public function __construct(
        private readonly RuleInterface $rules
    )
    {}

    /**
     * Evolves the given world to the next generation.
     *
     * @param World $world Current world state
     * @return World New world state for the next generation
     */
    public function next(World $world): World
    {
        $size = $world->size->cells;
        $newAlive = [];

        // Iterate over all coordinates in the grid
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $c = new Coordinate($x, $y);
                $species = $this->rules->willBeAlive($world, $c);
                if ($species instanceof SpeciesId) {
                    $newAlive[] = Cell::alive($c, $species);
                }
            }
        }

        // Create a new immutable World object with the updated cells
        return new World(
            new GridSize($size),
            $world->speciesCount,
            $newAlive
        );
    }
}