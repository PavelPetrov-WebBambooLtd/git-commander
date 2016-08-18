<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Lib\Server;
use AppBundle\Lib\FS;

class CommanderListServersCommand extends ContainerAwareCommand
{
    private $rootDir;
    
    protected function configure()
    {
        $this
            ->setName('commander:list-servers')
            ->setDescription('List added servers')
            ->addArgument('sId', InputArgument::OPTIONAL, 'Server ID for detailed view')
            ->setAliases(array('list-servers'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->get('kernel')->getRootDir().'/../';
        $servers = FS::getServers($this->rootDir);
        
        if($servers)
        {
            if(is_null($input->getArgument('sId')))
            {
                $tableResult = array();
                foreach($servers as $key => $server)
                {
                    $serverObject = (new Server())->fromArray($server);
                    $tableResult[] = array($key, $serverObject->getName());
                }
                $io->table(
                    array('ID', 'Server Name'),
                    $tableResult
                );
            }
            else
            {
                $serverArray = $servers[(int)$input->getArgument('sId')];
                $serverObject = (new Server())->fromArray($serverArray);
                $io->table(
                    array('Key', 'Value'),
                    array(
                        array('Name', $serverObject->getName()),
                        array('Port', $serverObject->getPort()),
                        array('User', $serverObject->getUser()),
                        array('Knocking Sequence', is_array($serverObject->getKnockingSequence()) ? implode(",", $serverObject->getKnockingSequence()) : false)
                    )
                );
            }
        }
        else
        {
            $output->writeln([
                'No Servers Yet :(',
                '================'
            ]);
        }
    }

}
