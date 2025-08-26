<?php declare(strict_types=1);

namespace App\Domain\Model\Enum;

/**
 * CellState represents the state of a cell in the Game of Life.
 *
 * It can be either ALIVE or DEAD.
 */
enum CellState: string
{
    case ALIVE = 'alive';
    case DEAD = 'dead';
}