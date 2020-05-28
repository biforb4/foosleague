<?php

declare(strict_types=1);

namespace FoosCommon\Model\Owner;

final class OwnerId
{
    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromString(string $id): OwnerId
    {
        return new self($id);
    }

    public function asString(): string
    {
        return $this->id;
    }
}
