<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use AppBundle\Lib\Repository;
use AppBundle\Lib\Server;
use AppBundle\Lib\FS;

class CommanderAddRepositoryCommand extends ContainerAwareCommand
{
    private $rootDir;

    protected function configure()
    {
        $this
            ->setName('commander:add-repository')
            ->setDescription('Add repository wizard')
            ->setAliases(array('add-repository'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->get('kernel')->getRootDir().'/../';

        $helper = $this->getHelper('question');
        //Set Repository Folder
        $questionRepositoryFolder = new Question('Please enter the path to the repository: ');
        $questionRepositoryFolder->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException(
                    'The path to the repository cannot be empty'
                );
            }

            return $answer;
        });
        $questionRepositoryFolder->setMaxAttempts(2);
        $localFolder = $helper->ask($input, $output, $questionRepositoryFolder);
        //Add remote directories
        //Server Names
        $serverNames = array();
        $servers = FS::getServers($this->rootDir);
        foreach ($servers as $key => $server) {
            $serverObject = (new Server())->fromArray($server);
            $serverNames[$serverObject->getName()] = $key;
        }

        $remotes = array();
        $loop = true;
        $id = 1;

        while ($loop) {
            if (empty($serverNames)) {
                $loop = false;
            } else {
                //Select Servere
                $questionServer = new ChoiceQuestion(
                    'Please select remote server',
                    array_keys($serverNames),
                    false
                );
                $questionServer->setErrorMessage('Server %s is invalid.');
                $serverName = $helper->ask($input, $output, $questionServer);

                //Set Remote Folder
                $questionRemoteFolder = new Question('Please enter the remote folder path: ', false);

                $questionRemoteFolder->setMaxAttempts(2);
                $remoteFolder = $helper->ask($input, $output, $questionRemoteFolder);

                //Set Branch
                $questionBranch = new Question('Please enter the branch name: ', 'master');

                $questionBranch->setMaxAttempts(2);
                $remoteBranch = $helper->ask($input, $output, $questionBranch);

                if (!$serverName || !$remoteFolder) {
                    $loop = false;
                } else {
                    $sId = $serverNames[$serverName];
                    $remotes[] = array($servers[$sId], $remoteFolder, $remoteBranch);
                }
                ++$id;
            }
        }
        $name = shell_exec("cd $localFolder;basename `git rev-parse --show-toplevel`");
        $repository = new Repository();
        $repository->setLocalFolder($localFolder);
        $repository->setName($name);

        foreach ($remotes as $remote) {
            list($serverArray, $remoteFolder, $remoteBranch) = $remote;
            $serverObject = (new Server())->fromArray($serverArray);
            $repository->addRemote($serverObject, $remoteFolder, $remoteBranch);
        }

        $currentRepositories = FS::getRepositories($this->rootDir);
        if (!$currentRepositories) {
            $currentRepositories = array();
        }

        $currentRepositories[] = $repository->toArray();
        if (FS::updateRepositories($this->rootDir, $currentRepositories)) {
            $io->success('Repository Added :)');
        } else {
            $io->warning('Something Failed! :(');
        }
    }
}
