<?php

namespace tests\AppBundle\Command;

use AppBundle\Command\CommanderAddServerCommand;
use AppBundle\Command\CommanderDeleteServerCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Lib\Server;
use AppBundle\Lib\FS;

class CommanderAddServerCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new CommanderAddServerCommand());

        $command = $application->find('add-server');

        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("Lorem Ipsum\n127.0.0.1\n22\nroot\nlorem\nlorem\n\n"));

        $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Server Added :)', $output);

        $servers = FS::getServers('./');
        end($servers);
        $lastKey = key($servers);

        $serverArray = $servers[$lastKey];
        $serverObject = (new Server())->fromArray($serverArray);
        if ($serverObject->getName() == 'Lorem Ipsum') {
            $application = new Application($kernel);
            $application->add(new CommanderDeleteServerCommand());

            $command = $application->find('delete-server');

            $commandTester = new CommandTester($command);

            $commandTester->execute(array(
                'command' => $command->getName(),
                'serverId' => $lastKey,
            ));

            $output = $commandTester->getDisplay();
            $this->assertContains('Server Removed :)', $output);
        }
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
