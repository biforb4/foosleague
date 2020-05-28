<?php

declare(strict_types=1);

namespace FoosLeague\Domain\Game;

final class GameException extends \Exception
{
    public static function gameNotEnded(): GameException
    {
        return new self('Game not finished');
    }
}
