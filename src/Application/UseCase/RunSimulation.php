<?php declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\{InputReader, OutputWriter};
use App\Domain\Rule\RuleInterface;
use App\Domain\Service\EvolutionService;

/**
 * RunSimulation is the application use case that orchestrates
 * the execution of a Game of Life simulation.
 *
 * It handles reading the input, evolving the world for the configured
 * number of iterations, and writing the output generations.
 */
final class RunSimulation
{
    public function __construct(
        private readonly InputReader  $reader,
        private readonly OutputWriter $writer,
        private readonly RuleInterface $rules
    ) {}


    /**
     * Executes the simulation.
     *
     * @param string $in  Path to the input file
     * @param string $out Path to the output file
     */
    public function __invoke(string $in, string $out): void
    {
        $data = $this->reader->read($in);
        $config = $data['config'];
        $world = $data['world'];


        $evolver = new EvolutionService($this->rules);
        $generations = [$world];
        for ($i = 0; $i < $config->iterations; $i++) {
            $world = $evolver->next($world);
            $generations[] = $world;
        }
        $this->writer->write($out, $generations);
    }
}