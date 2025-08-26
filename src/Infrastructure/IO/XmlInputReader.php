<?php declare(strict_types=1);

namespace App\Infrastructure\IO;


use App\Application\Dto\SimulationConfig;
use App\Application\Port\InputReader;
use App\Infrastructure\IO\Exception\InvalidInputException;
use App\Domain\Model\{Cell, Coordinate, GridSize, SpeciesId, World};
use Exception;
use InvalidArgumentException;
use SimpleXMLElement;

/**
 * XmlInputReader reads and validates a Game of Life simulation from an XML file.
 *
 * Responsibilities:
 * 1. Reads an XML file from the given path.
 * 2. Validates that required elements exist (world, cells, species, iterations, organisms).
 * 3. Converts XML organisms into domain objects (Cell, Coordinate, SpeciesId).
 * 4. Constructs the domain World object and SimulationConfig DTO.
 * 5. Throws InvalidInputException for missing elements, invalid coordinates, or other errors.
 */
final class XmlInputReader implements InputReader
{
    /**
     * Reads the XML file and returns the simulation config and world.
     *
     * @throws InvalidInputException if the file is missing, malformed, or contains invalid data
     */
    public function read(string $path): array
    {
        if (!file_exists($path)) {
            throw new InvalidInputException("Unable to read nonexistent file");
        }

        try {
            $xml = new SimpleXMLElement((string)file_get_contents($path));
            $this->validateXmlFile($xml);

            $size = (int)$xml->world->cells;
            $species = (int)$xml->world->species;
            $iterations = (int)$xml->world->iterations;
            if ($iterations < 0) {
                throw new InvalidInputException("Value of element 'iterations' must be zero or positive number");
            }

            // Use readCells to get a 2D array of species or null
            $cellsArray = $this->readCells($xml, $size, $species);

            // Convert 2D array into Cell objects
            $alive = [];
            foreach ($cellsArray as $y => $row) {
                foreach ($row as $x => $speciesId) {
                    if ($speciesId !== null) {
                        $alive[] = Cell::alive(new Coordinate($x, $y), new SpeciesId($speciesId));
                    }
                }
            }

            $config = new SimulationConfig($size, $iterations);
            $world = new World(new GridSize($size), $species, $alive);

            return [
                'config' => $config,
                'world' => $world,
            ];
        } catch (InvalidArgumentException $e) {
            throw new InvalidInputException($e->getMessage());
        } catch (Exception) {
            throw new InvalidInputException("Cannot read XML file");
        }
    }

    /**
     * Validates that all required XML elements exist.
     *
     * @throws InvalidInputException for missing required elements
     */
    private function validateXmlFile(SimpleXMLElement $xml): void
    {
        if (!isset($xml->world)) {
            throw new InvalidInputException("Missing element 'world'");
        }
        if (!isset($xml->world->iterations)) {
            throw new InvalidInputException("Missing element 'iterations'");
        }
        if (!isset($xml->world->cells)) {
            throw new InvalidInputException("Missing element 'cells'");
        }
        if (!isset($xml->world->species)) {
            throw new InvalidInputException("Missing element 'species'");
        }
        if (!isset($xml->organisms)) {
            throw new InvalidInputException("Missing element 'organisms'");
        }
        foreach ($xml->organisms->organism as $organism) {
            if (!isset($organism->x_pos)) {
                throw new InvalidInputException("Missing element 'x_pos' in some of the element 'organism'");
            }
            if (!isset($organism->y_pos)) {
                throw new InvalidInputException("Missing element 'y_pos' in some of the element 'organism'");
            }
            if (!isset($organism->species)) {
                throw new InvalidInputException("Missing element 'species' in some of the element 'organism'");
            }
        }
    }

    /**
     * Converts XML organisms to a 2D array of cells.
     *
     * @throws InvalidInputException for out-of-range coordinates or species
     */
    private function readCells(SimpleXMLElement $xml, int $worldSize, int $speciesCount): array
    {
        $cells = [];
        foreach ($xml->organisms->organism as $organism) {
            $x = (int) $organism->x_pos;
            if ($x < 0 || $x >= $worldSize) {
                throw new InvalidInputException("Value of element 'x_pos' of element 'organism' must be between 0 and number of cells");
            }
            $y = (int) $organism->y_pos;
            if ($y < 0 || $y >= $worldSize) {
                throw new InvalidInputException("Value of element 'y_pos' of element 'organism' must be between 0 and number of cells");
            }
            $species = (int) $organism->species;
            if ($species < 0 || $species >= $speciesCount) {
                throw new InvalidInputException("Value of element 'species' of element 'organism' must be between 0 and maximal number of species");
            }
            $cells[$y] ??= [];
            $finalSpecies = $species;
            if (isset($cells[$y][$x])) {
                $existingCell = $cells[$y][$x]; /** @var int $existingCell */
                $availableSpecies = [$existingCell, $species];
                $finalSpecies = $availableSpecies[array_rand($availableSpecies)];
            }
            $cells[$y][$x] = $finalSpecies;
        }
        for ($y = 0; $y < $worldSize; $y++) {
            $cells[$y] ??= [];
            for ($x = 0; $x < $worldSize; $x++) {
                if (!isset($cells[$y][$x])) {
                    $cells[$y][$x] = null;
                }
            }
        }
        return $cells;
    }
}