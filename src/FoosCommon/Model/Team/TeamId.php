<?php

declare(strict_types=1);

namespace FoosCommon\Model\Team;

use Webmozart\Assert\Assert;

final class TeamId
{
    private string $id;

    private function __construct(string $id)
    {
        Assert::uuid($id);
        $this->id = $id;
    }

    public static function fromString(string $id): self
    {
        return new TeamId($id);
    }

    public function asString(): string
    {
        return $this->id;
    }

    public function equals(self $teamId): bool
    {
        return $this->asString() === $teamId->asString();
    }
}
