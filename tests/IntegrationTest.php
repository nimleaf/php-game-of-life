<?php declare(strict_types = 1);

namespace Tests\App;

use App\Infrastructure\Cli\RunSimulationCommand;
use DOMDocument;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use function array_map;
use function file_exists;
use function range;
use function unlink;

final class IntegrationTest extends TestCase
{
    private const OUTPUT_FILE = 'output.xml';

    /**
     * @dataProvider getInputAndExpectedOutputFiles
     */
    public function testGame(string $inputFile, string $expectedOutputFile): void
    {
        $application = new Application();
        $application->add(new RunSimulationCommand());

        $command = $application->find('game:run');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                '--input' => $inputFile,
                '--output' => self::OUTPUT_FILE,
            ]
        );

        $output = $this->loadXmlForComparison();

        Assert::assertXmlStringEqualsXmlFile(
            $expectedOutputFile,
            $output,
            'Expected XML and output XML should be same'
        );
    }


    public function getInputAndExpectedOutputFiles(): array
    {
        $scenarios = range(1, 9);

        return array_map(
            static fn(int $scenario): array => [
                __DIR__ . '/__fixtures__/scenario-' . $scenario . '/input.xml',
                __DIR__ . '/__fixtures__/scenario-' . $scenario . '/output.xml',
            ],
            $scenarios
        );
    }


    private function loadXmlForComparison(): string
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load(self::OUTPUT_FILE);

        return $dom->saveXML();
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $outputFilePath = self::OUTPUT_FILE;
        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }
    }
}
