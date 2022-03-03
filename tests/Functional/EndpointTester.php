<?php

namespace Hippy\Api\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class EndpointTester extends WebTestCase
{
    /** @var KernelBrowser */
    protected KernelBrowser $client;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
}
