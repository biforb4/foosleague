<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\Slack;

use FoosLeague\Application\Dto\League;
use FoosLeague\Infrastructure\Slack\ACL\SlackCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ShowLeaguesController
{
    use HandleTrait;

    private SlackResponseBuilder $responseBuilder;

    public function __construct(MessageBusInterface $queryBus, SlackResponseBuilder $responseBuilder)
    {
        $this->messageBus = $queryBus;
        $this->responseBuilder = $responseBuilder;
    }

    /** @Route("/slack/show-leagues", methods={"POST"}) */
    public function show(SlackCommand $slackCommand): JsonResponse
    {
        /** @var array<int, League> $result */
        $result = $this->handle($slackCommand->translateToShowLeaguesQuery());

        return new JsonResponse($this->responseBuilder->leaguesList(...$result));
    }
}
