<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Lib\FS;

class CommanderDeleteRepositoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('commander:delete-repository')
            ->setDescription('Delete repository from the list')
            ->addArgument('repositoryId', InputArgument::REQUIRED, 'The Repository ID')
            ->setAliases(array('delete-repository'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->get('kernel')->getRootDir().'/../';
        $repositories = FS::getRepositories($this->rootDir);

        unset($repositories[$input->getArgument('repositoryId')]);
        //Update repositorie

        //
        if (FS::updateRepositories($this->rootDir, $repositories)) {
            $io->success('Repository Removed :)');
        } else {
            $io->warning('Something Failed! :(');
        }
    }
}
