<?php declare(strict_types=1);

namespace App\Infrastructure\Cli;

use App\Application\UseCase\RunSimulation;
use App\Domain\Rule\ConwaysRules;
use Symfony\Component\Console\Input\InputOption;
use App\Infrastructure\IO\{XmlInputReader, XmlOutputWriter};
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'game:run',
    description: 'use input file [-i] and produce output file [-o]'
)]
final class RunSimulationCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('input', 'i', InputOption::VALUE_OPTIONAL, 'Input file', 'input.xml');
        $this->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output file', 'output.xml');
    }

    /**
     * Executes the Game of Life simulation.
     *
     * Steps:
     * 1. Reads the initial world state from the input XML using XmlInputReader.
     * 2. Evolves the world through the configured number of iterations using RunSimulation use case.
     * 3. Writes all generations to the output XML file using XmlOutputWriter.
     *
     * @param InputInterface $input  Symfony input object
     * @param OutputInterface $output Symfony output object
     * @return int Exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFile = $input->getOption('input');
        assert(is_string($inputFile));
        $outputFile = $input->getOption('output');
        assert(is_string($outputFile));

        $useCase = new RunSimulation(
            new XmlInputReader(),
            new XmlOutputWriter(),
            new ConwaysRules()
        );
        $useCase($inputFile, $outputFile);
        $output->writeln('<info>Done.</info>');
        return Command::SUCCESS;
    }
}