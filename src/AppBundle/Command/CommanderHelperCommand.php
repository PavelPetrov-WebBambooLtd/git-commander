<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CommanderHelperCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('commander:helper')
            ->setDescription('All commander commands')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Commander version 0.0.1');
        $io->section('Commands');
        $io->listing(array(
            'list-servers',
            'add-server',
            'delete-server',
            'list-repositories',
            'add-repository',
            'delete-repository',
            'check-repositories'
        ));
    }

}
