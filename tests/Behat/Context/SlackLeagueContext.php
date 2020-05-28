<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Webmozart\Assert\Assert;

final class SlackLeagueContext implements Context
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @BeforeScenario
     */
    public function beginTransaction()
    {
        /** @var KernelBrowser $client */
        $client = $this->session->getDriver()->getClient();
        $client->disableReboot();
        $connection = $client->getContainer()->get('database_connection');
        $connection->beginTransaction();
    }

    /**
     * @AfterScenario
     */
    public function rollback()
    {
        /** @var KernelBrowser $client */
        $client = $this->session->getDriver()->getClient();
        $connection = $client->getContainer()->get('database_connection');
        $connection->rollBack();
    }


    /**
     * @When I create a league :arg1
     */
    public function iCreateALeague($arg1)
    {
        /** @var HttpKernelBrowser $client */
        $client = $this->session->getDriver()->getClient();
        parse_str(
            'token=u1tHaqUZwWEJbVbzN5JzkNQL&team_id=T013CPLKDQF&team_domain=testingground-yx38670&channel_id=C01464XF68H&channel_name=test&user_id=U013LPW06DU&user_name=bniewinski&command=%2Fleague&text=test&response_url=https%3A%2F%2Fhooks.slack.com%2Fcommands%2FT013CPLKDQF%2F1131058717443%2FYBoW0AgeaMq0weiTceWCmLuo&trigger_id=1129470485045.1114802659831.55ddee5763d0b8c7c2c6c53c594840d6',
            $parameters
        );

        $client->request(
            'POST',
            '/slack/league',
            $parameters, [],
            [
                'PHP_AUTH_USER' => 'slackbot',
                'PHP_AUTH_PW'   => 'password',
            ]
        );
        Assert::same($client->getResponse()->getStatusCode(), 200);
        $expectedResponse = [
            'response_type' => 'ephemeral',
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => 'League *test* created',
                    ],
                ],
            ],
        ];

        Assert::same($client->getResponse()->getContent(), json_encode($expectedResponse, JSON_THROW_ON_ERROR));
    }

    /**
     * @Then :arg1 league is created
     */
    public function leagueIsCreated($arg1)
    {
        /** @var HttpKernelBrowser $client */
        $client = $this->session->getDriver()->getClient();

        parse_str(
            'token=u1tHaqUZwWEJbVbzN5JzkNQL&team_id=T013CPLKDQF&team_domain=testingground-yx38670&channel_id=C01464XF68H&channel_name=test&user_id=U013LPW06DU&user_name=bniewinski&command=%2Fleagues&text=&response_url=https%3A%2F%2Fhooks.slack.com%2Fcommands%2FT013CPLKDQF%2F1146604112961%2FMVDXWAtJ0lwKJU112nrr4DVL&trigger_id=1140160320740.1114802659831.9f0021cbd131dc63fe7018c715469184',
            $parameters
        );

        $client->request(
            'POST',
            '/slack/show-leagues',
            $parameters, [],
            [
                'PHP_AUTH_USER' => 'slackbot',
                'PHP_AUTH_PW'   => 'password',
            ]
        );
        Assert::same($client->getResponse()->getStatusCode(), 200);

        $expectedResponse = [
            'response_type' => 'ephemeral',
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => '• test\n',
                    ],
                ],
            ],
        ];

        Assert::same($client->getResponse()->getContent(), json_encode($expectedResponse, JSON_THROW_ON_ERROR));
    }
}
