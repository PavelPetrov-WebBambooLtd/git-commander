<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Lib\FS;

class CommanderDeleteServerCommand extends ContainerAwareCommand
{
    private $rootDir;
    
    protected function configure()
    {
        $this
            ->setName('commander:delete-server')
            ->setDescription('Delete server from the list')
            ->addArgument('serverId', InputArgument::REQUIRED, 'The Server ID')
            ->setAliases(array('delete-server'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->get('kernel')->getRootDir().'/../';
        $servers = FS::getServers($this->rootDir);
        
        unset($servers[$input->getArgument('serverId')]);
        //Update repositorie
        
        //
        if(FS::updateServers($this->rootDir, $servers))
        {
            $io->success('Server Removed :)');
        }
        else
        {
            $io->warning('Something Failed! :(');
        }
    }

}
