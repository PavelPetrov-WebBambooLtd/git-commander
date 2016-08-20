<?php

namespace tests\AppBundle\Command;

use AppBundle\Command\CommanderListRepositoriesCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Lib\FS;

class CommanderListRepositoriesCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new CommanderListRepositoriesCommand());

        $command = $application->find('list-repositories');

        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command' => $command->getName(),
        ));
        $output = $commandTester->getDisplay();
        if (FS::getRepositories('./')) {
            $this->assertContains('Repository Name', $output);
        } else {
            $this->assertContains('No Repositories Yet :(', $output);
        }
    }
}
