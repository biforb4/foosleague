<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

final class LeagueException extends \Exception
{
    public static function pendingGameNotFound(): LeagueException
    {
        return new self('Pending game was not found');
    }
}
