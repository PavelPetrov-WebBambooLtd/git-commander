<?php
namespace Tests\AppBundle\Command;

use AppBundle\Command\CommanderAddRepositoryCommand;
use AppBundle\Command\CommanderDeleteRepositoryCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Lib\Server;
use AppBundle\Lib\Repository;
use AppBundle\Lib\FS;

class CommanderAddRepositoryCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new CommanderAddRepositoryCommand());
        
        $command = $application->find('add-repository');
        
        $commandTester = new CommandTester($command);
        
        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("./\n\n\n\n"));
        
        $commandTester->execute(array(
            'command'  => $command->getName()
        ));
        
        $output = $commandTester->getDisplay();
        $this->assertContains('Repository Added :)', $output);
        
        $repositories = FS::getRepositories("./");
        end($repositories);
        $lastKey = key($repositories);
        
        $repositoryArray = $repositories[$lastKey];
        $repositoryObject = (new Repository())->fromArray($repositoryArray);
        
        //var_dump($repositoryObject->getName());
        if($repositoryObject->getName() == "commander\n")
        {
            $application = new Application($kernel);
            $application->add(new CommanderDeleteRepositoryCommand());

            $command = $application->find('delete-repository');

            $commandTester = new CommandTester($command);

            $commandTester->execute(array(
                'command'  => $command->getName(),
                'repositoryId' => $lastKey
            ));

            $output = $commandTester->getDisplay();
            $this->assertContains('Repository Removed :)', $output);
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