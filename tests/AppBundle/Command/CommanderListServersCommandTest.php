<?php
namespace Tests\AppBundle\Command;

use AppBundle\Command\CommanderListServersCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Lib\FS;

class CommanderListServersCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new CommanderListServersCommand());
        
        $command = $application->find('list-servers');
        
        $commandTester = new CommandTester($command);
        
        $commandTester->execute(array(
            'command'  => $command->getName()
        ));
        $output = $commandTester->getDisplay();
        if(FS::getServers("./"))
        {
            $this->assertContains('Server Name', $output);
        }
        else
        {
            $this->assertContains('No Servers Yet :(', $output);
        }
    }
}