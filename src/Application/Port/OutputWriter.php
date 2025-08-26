<?php declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\Model\World;

/**
 * OutputWriter defines a contract for writing simulation output data to a destination.
 *
 * Implementations should take a list of world generations and persist them
 * in a specific format (e.g., XML, JSON, database, etc.).
 */
interface OutputWriter
{
    /**
     * Writes the simulation output to the specified path.
     *
     * @param string $path Path to save the output file
     * @param list<World> $generations List of world generations
     */
    public function write(string $path, array $generations): void;
}