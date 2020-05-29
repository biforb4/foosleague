<?php

declare(strict_types=1);

namespace tests\Spec\FoosCommon\Infrastructure\Slack;

use FoosCommon\Infrastructure\Slack\SlackCredentials;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class SlackCredentialsSpec extends ObjectBehavior
{
    public function let(Request $request, ParameterBag $headers)
    {
        $headers->get('X-Slack-Request-Timestamp')->willReturn('1531420618');
        $headers->get('X-Slack-Signature')->willReturn('v0=a2114d57b48eac39b9ad189dd8316235a7b4a8d21a10bd27519666489c69b503');
        $request->headers = $headers;
        $content = 'token=xyzz0WbapA4vBCDEFasx0q6G&team_id=T1DC2JH3J&team_domain=testteamnow&channel_id=G8PSS9T3V&channel_name=foobar&user_id=U2CERLKJA&user_name=roadrunner&command=%2Fwebhook-collect&text=&response_url=https%3A%2F%2Fhooks.slack.com%2Fcommands%2FT1DC2JH3J%2F397700885554%2F96rGlfmibIGlgcZRskXaIFfN&trigger_id=398738663015.47445629121.803a0bc887a14d10d2c447fce8b6703c';
        $request->getContent()->willReturn($content);
        $this->beConstructedThrough('fromRequest', [$request]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SlackCredentials::class);
    }

    public function it_can_validate_user_with_valid_secret(UserInterface $user)
    {
        $user->getPassword()->willReturn('8f742231b10e8888abcd99yyyzzz85a5');
        $this->areValidFor($user)->shouldBe(true);
    }

    public function it_can_validate_user_with_invalid_secret(UserInterface $user)
    {
        $user->getPassword()->willReturn('invalid_secret');
        $this->areValidFor($user)->shouldBe(false);
    }
}
