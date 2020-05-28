<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\Slack;

use FoosLeague\Infrastructure\Slack\ACL\SlackCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CreateLeagueController
{
    private SlackResponseBuilder $responseBuilder;
    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus, SlackResponseBuilder $responseBuilder)
    {
        $this->responseBuilder = $responseBuilder;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route ("/slack/league", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function create(SlackCommand $slackCommand): JsonResponse
    {
        $command = $slackCommand->translateToCreateLeagueCommand();
        $this->commandBus->dispatch($command);
        return JsonResponse::create(
            $this->responseBuilder->confirmationMessage(
                sprintf('League *%s* created', $command->getName()->asString())
            )
        );
    }
}
