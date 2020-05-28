<?php

declare(strict_types=1);

namespace FoosTeam\Model\Player;

final class Name
{
    private string $slackHandle;

    private function __construct(string $slackHandle)
    {
        $this->slackHandle = $slackHandle;
    }

    public static function fromSlackHandle(string $slackHandle): self
    {
        return new Name($slackHandle);
    }

    public function asString(): string
    {
        return $this->slackHandle;
    }
}
