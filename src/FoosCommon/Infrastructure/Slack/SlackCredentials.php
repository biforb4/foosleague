<?php

declare(strict_types=1);

namespace FoosCommon\Infrastructure\Slack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

class SlackCredentials
{
    public const REQUEST_TIMESTAMP = 'X-Slack-Request-Timestamp';
    public const REQUEST_SIGNATURE = 'X-Slack-Signature';
    public const USERNAME = 'slackbot';

    private string $signature;
    private int $timestamp;
    private string $payload;

    private function __construct(string $signature, int $timestamp, string $payload)
    {
        $this->signature = $signature;
        $this->timestamp = $timestamp;
        $this->payload = $payload;
    }

    public static function fromRequest(Request $request): SlackCredentials
    {
        $signature = $request->headers->get(self::REQUEST_SIGNATURE);
        $timestamp = (int)$request->headers->get(self::REQUEST_TIMESTAMP);
        $payload = (string)$request->getContent();

        Assert::notNull($signature);

        return new self(
            $signature,
            $timestamp,
            $payload
        );
    }

    public function areValidFor(UserInterface $user): bool
    {
        $baseString = 'v0:' . $this->timestamp . ':' . $this->payload;
        $password = $user->getPassword();
        Assert::notNull($password);
        $hashed = 'v0=' . hash_hmac('sha256', $baseString, $password);
        return hash_equals($this->signature, $hashed);
    }
}
