<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\Slack\ACL;

use FoosLeague\Application\Command\CreateLeague;
use FoosLeague\Application\Query\ShowLeagues;
use FoosLeague\Domain\League\LeagueId;
use FoosLeague\Domain\League\Name;
use FoosCommon\Model\Owner\OwnerId;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class SlackCommand
{
    private string $token;
    /** @SerializedName("team_id") */
    private string $teamId;
    /** @SerializedName("team_domain") */
    private string $teamDomain;
    /** @SerializedName("channel_id") */
    private string $channelId;
    /** @SerializedName("channel_name") */
    private string $channelName;
    /** @SerializedName("user_id") */
    private string $userId;
    /** @SerializedName("user_name") */
    private string $userName;
    private string $command;
    private string $text;
    /** @SerializedName("response_url") */
    private string $responseUrl;
    /** @SerializedName("trigger_id") */
    private string $triggerId;

    public function __construct(
        string $token,
        string $teamId,
        string $teamDomain,
        string $channelId,
        string $channelName,
        string $userId,
        string $userName,
        string $command,
        string $text,
        string $responseUrl,
        string $triggerId
    ) {
        $this->token = $token;
        $this->teamId = $teamId;
        $this->teamDomain = $teamDomain;
        $this->channelId = $channelId;
        $this->channelName = $channelName;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->command = $command;
        $this->text = $text;
        $this->responseUrl = $responseUrl;
        $this->triggerId = $triggerId;
    }

    public function translateToShowLeaguesQuery(): ShowLeagues
    {
        return new ShowLeagues(OwnerId::fromString($this->teamId));
    }

    public function translateToCreateLeagueCommand(): CreateLeague
    {
        return new CreateLeague(
            OwnerId::fromString($this->teamId),
            LeagueId::fromString((string)Uuid::uuid4()),
            Name::fromString($this->text)
        );
    }
}
