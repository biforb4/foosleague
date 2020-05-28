<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\Slack;

use FoosLeague\Application\Dto\League;
use Jeremeamia\Slack\BlockKit\Slack;

final class SlackResponseBuilder
{
    private const NO_LEAGUES_CREATED = 'No leagues created.';

    public function confirmationMessage(string $text): array
    {
        $message = Slack::newMessage();
        $message->text($text);

        return $message->toArray();
    }

    public function leaguesList(League ...$leagues): array
    {
        if (empty($leagues)) {
            return $this->confirmationMessage(self::NO_LEAGUES_CREATED);
        }

        $text = '';
        foreach ($leagues as $league) {
            $text .= sprintf('• %s\n', $league->getName());
        }
        $message = Slack::newMessage();
        $message->text($text);

        return $message->toArray();
    }
}
