<?php declare(strict_types=1);

namespace App\Infrastructure\IO;

use App\Application\Port\OutputWriter;
use App\Domain\Model\World;
use App\Infrastructure\IO\Exception\OutputWritingException;
use DOMDocument;
use SimpleXMLElement;

/**
 * XmlOutputWriter writes Game of Life simulation results to an XML file.
 *
 * Responsibilities:
 * 1. Takes a list of World objects (generations) and writes the last generation to XML.
 * 2. Loads a predefined XML template (output-template.xml) as a starting structure.
 * 3. Fills in world metadata (size, species count) and all alive cells.
 * 4. Cells are sorted by Y, then X coordinates for consistency.
 * 5. Outputs the final XML file with pretty formatting.
 * 6. Throws OutputWritingException if the file cannot be written.
 */
final class XmlOutputWriter implements OutputWriter
{
    private const OUTPUT_TEMPLATE = __DIR__ . '/Template/output-template.xml';

    /**
     * Writes the last World generation to an XML file.
     *
     * @param string $path Path to output an XML file
     * @param list<World> $generations Array of World objects (all generations)
     * @throws OutputWritingException if writing to a file fails
     */
    public function write(string $path, array $generations): void
    {
        $lastWorld = end($generations);

        /** @var SimpleXMLElement $xml */
        $xml = simplexml_load_file(self::OUTPUT_TEMPLATE);

        $xml->world->cells = $lastWorld->size->cells;
        $xml->world->species = $lastWorld->speciesCount;

        $cells = iterator_to_array($lastWorld->aliveCells());
        usort($cells, fn($a, $b) => [$a->coordinate->y, $a->coordinate->x] <=> [$b->coordinate->y, $b->coordinate->x]);

        foreach ($cells as $cell) {
            $o = $xml->organisms->addChild('organism');
            $o->addChild('x_pos', (string)$cell->coordinate->x);
            $o->addChild('y_pos', (string)$cell->coordinate->y);
            $o->addChild('species', (string)$cell->species?->value);
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        if (file_put_contents($path, $dom->saveXML()) === false) {
            throw new OutputWritingException("Writing XML file failed");
        }
    }
}