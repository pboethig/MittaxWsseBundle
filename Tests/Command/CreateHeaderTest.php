<?php

namespace Mittax\MessageBundle\Tests\Command;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Mittax\WsseBundle\Command\GenerateHeaderCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;


require_once __DIR__. '/../../../../../app/autoload.php';

class GenerateHeaderCommandTest extends KernelTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testCommand()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $application->add(new GenerateHeaderCommand());

        $command = $application->find('mittax:wsse:generate-header');

        $commandTester = new CommandTester($command);

    }


}
