<?php declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\Model\World;
use App\Application\Dto\SimulationConfig;

/**
 * InputReader defines a contract for reading simulation input data from a source.
 *
 * Implementations should parse the input (e.g., XML, JSON, or other formats)
 * and return both the simulation configuration and the initial world state.
 *
 * Example usage:
 *   $reader = new XmlInputReader();
 *   ['config' => $config, 'world' => $world] = $reader->read('input.xml');
 */
interface InputReader
{
    /**
     * Reads input from the specified path and returns the simulation configuration
     * and the initial world state.
     *
     * @param string $path Path to the input file
     * @return array{config: SimulationConfig, world: World}
     */
    public function read(string $path): array;
}