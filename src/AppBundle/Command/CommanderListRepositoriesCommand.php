<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Lib\FS;
use AppBundle\Lib\Repository;
use AppBundle\Lib\Server;

class CommanderListRepositoriesCommand extends ContainerAwareCommand
{
    private $rootDir;
    
    protected function configure()
    {
        $this
            ->setName('commander:list-repositories')
            ->setDescription('List all added repositories')
            ->addArgument('rId', InputArgument::OPTIONAL, 'Repository ID for detailed view')
            ->setAliases(array('list-repositories'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->get('kernel')->getRootDir().'/../';
        $repositories = FS::getRepositories($this->rootDir);
        if($repositories)
        {
            if(is_null($input->getArgument('rId')))
            {
                $tableResult = array();
                foreach($repositories as $key => $repository)
                {
                    $repositoryObject = (new Repository())->fromArray($repository);
                    $tableResult[] = array($key, $repositoryObject->getName());
                }
                $io->table(
                    array('ID', 'Repository Name'),
                    $tableResult
                );
            }
            else {
                $repositoryArray = $repositories[(int)$input->getArgument('rId')];
                $repositoryObject = (new Repository())->fromArray($repositoryArray);
                $tableResult = array(
                    array('Name', $repositoryObject->getName()),
                    array('Local Folder', $repositoryObject->getLocalFolder())
                );
                $id = 1;
                foreach($repositoryObject->getRemotes() as $remote)
                {
                    list($serverArray, $remoteFolder) = $remote;
                    $serverObject = (new Server())->fromArray($serverArray);
                    $tableResult[] = array("$id. Server Name", $serverObject->getName());
                    $tableResult[] = array("$id. Remote Folder", $remoteFolder);
                    $id++;
                }
                $io->table(
                    array('Key', 'Value'),
                    $tableResult
                );
            }
        }
        else
        {
            $output->writeln([
                'No Repositories Yet :(',
                '======================'
            ]);
        }
    }

}
