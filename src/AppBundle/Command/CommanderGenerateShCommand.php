<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use AppBundle\Lib\ShCommandGenerator;

class CommanderGenerateShCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('commander:generate-sh')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = $this->createGenerator();
        $generator->generateScript($this->getSkeletonDirs(), $this->getContainer()->getParameter('kernel.root_dir'));

        $io = new SymfonyStyle($input, $output);
        $io->title('Commander bash script generated');

        $root = $this->getContainer()->getParameter('kernel.root_dir').'/../';
        shell_exec("chmod +x {$root}commander.sh");

        $io->success('Making commander.sh executable ...');
        shell_exec("mv {$root}commander.sh /usr/bin/commander");
        $io->success('Trying to copy commander.sh to /usr/bin ...');
    }

    protected function createGenerator()
    {
        return new ShCommandGenerator($this->getContainer()->get('filesystem'));
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        $skeletonDirs[] = __DIR__.'/../../../app/Resources/views';

        return $skeletonDirs;
    }
}
