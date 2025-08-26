<?php declare(strict_types=1);

namespace App\Application\Dto;

/**
 * SimulationConfig represents the configuration of a Game of Life simulation.
 */
final class SimulationConfig
{
    public function __construct(
        public readonly int $size,
        public readonly int $iterations
    ) {}
}