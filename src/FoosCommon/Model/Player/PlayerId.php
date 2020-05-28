<?php

declare(strict_types=1);

namespace FoosCommon\Model\Player;

use Webmozart\Assert\Assert;

final class PlayerId implements \Stringable
{
    private string $id;

    private function __construct(string $uuid)
    {
        Assert::uuid($uuid);
        $this->id = $uuid;
    }

    public static function fromString(string $id): self
    {
        return new PlayerId($id);
    }

    public function asString(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->asString();
    }
}
