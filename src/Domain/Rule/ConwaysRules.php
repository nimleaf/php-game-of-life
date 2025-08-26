<?php declare(strict_types=1);

namespace App\Domain\Rule;

use App\Domain\Model\{Coordinate, SpeciesId, World};


/**
 * ConwaysRules implements the standard Conway's Game of Life rules
 * with support for multiple species.
 *
 * Rules:
 *   - A live cell survives if it has 2 or 3 live neighbors.
 *   - A dead cell becomes alive if it has exactly 3 live neighbors.
 *   - For multi-species cells, the majority species among neighbors is assigned.
 */
final class ConwaysRules implements RuleInterface
{
    /**
     * Returns all valid neighbor coordinates of a given cell within the grid size.
     */
    public function neighbors(Coordinate $c, int $size): iterable
    {
        for ($dy = -1; $dy <= 1; $dy++) {
            for ($dx = -1; $dx <= 1; $dx++) {
                if ($dx === 0 && $dy === 0) {
                    continue;
                }
                $x = $c->x + $dx;
                $y = $c->y + $dy;
                if ($x < 0 || $y < 0 || $x >= $size || $y >= $size) {
                    continue;
                }
                yield new Coordinate($x, $y);
            }
        }
    }

    /**
     * Determines if a cell will be alive in the next generation.
     *
     * Returns a SpeciesId if the cell will be alive, or null if dead.
     */
    public function willBeAlive(World $world, Coordinate $c): ?SpeciesId
    {
        $alive = 0;
        $speciesCounts = [];

        foreach ($this->neighbors($c, $world->size->cells) as $n) {
            if ($world->isAliveAt($n)) {
                $alive++;
                $sid = $world->speciesAt($n)->value;
                $speciesCounts[$sid] = ($speciesCounts[$sid] ?? 0) + 1;
            }
        }

        $currentlyAlive = $world->isAliveAt($c);

        if ($currentlyAlive) {
            // a live cell survives with 2 or 3 neighbors
            if ($alive === 2 || $alive === 3) {
                // keep majority species (for a multi-species edge case)
                $max = array_keys($speciesCounts, max($speciesCounts))[0] ?? $world->speciesAt($c)->value;
                return new SpeciesId((int)$max);
            }
            return null;
        }

        if ($alive === 3) {
            // newborn takes the majority species of its neighbors
            $max = array_keys($speciesCounts, max($speciesCounts))[0] ?? 0;
            return new SpeciesId((int)$max);
        }
        return null;
    }
}