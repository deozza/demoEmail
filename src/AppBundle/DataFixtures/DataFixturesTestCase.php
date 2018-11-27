<?php

namespace AppBundle\DataFixtures;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class DataFixturesTestCase extends WebTestCase
{

    protected $client;
    protected $application;

    public function getClientOrCreateOne()
    {
        if (null === $this->client)
        {
            $this->client = static::createClient();
        }
        return $this->client;
    }

    public function getApplicationOrCreateOne()
    {
        if (null === $this->application)
        {
            $this->application = new Application($this->getClientOrCreateOne()->getKernel());
            $this->application->setAutoExit(false);
        }
        return $this->application;
    }

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->getClientOrCreateOne();

        $this->runCommand('d:d:d --force --env=test');
        $this->runCommand('d:d:c --env=test');
        $this->runCommand('d:s:c --env=test');

        $this->client = static::createClient();

        parent::setUp();
    }

    protected function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return $this->getApplicationOrCreateOne()->run(new StringInput($command));
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}