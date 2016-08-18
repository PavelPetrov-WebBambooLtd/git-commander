<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use AppBundle\Lib\FS;
use AppBundle\Lib\Repository;
use AppBundle\Lib\Server;
use AppBundle\Lib\ServerInterface;

class CommanderCheckRepositoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('commander:check-repositories')
            ->setDescription('Checking if the repositories on the remote locations need updating')
            ->addArgument('repositoryId', InputArgument::REQUIRED, 'The Repository ID')
            ->addOption('commonSecret', 'cs', InputOption::VALUE_OPTIONAL, "If the script should use a common secret", false)
            ->setAliases(array('check-repositories'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->get('kernel')->getRootDir().'/../';
        
        $helper = $this->getHelper('question');
        
        
        if($input->getOption('commonSecret'))
        {
            $questionCommonSecret = new Question("Specify the Common Secret: ");
            $questionCommonSecret->setValidator(function ($answer) {
                if (trim($answer) == '') {
                    throw new \Exception('The secret can not be empty');
                }

                return $answer;
            });
            $questionCommonSecret->setHidden(true);
            $questionCommonSecret->setMaxAttempts(2);
            $secret = $helper->ask($input, $output, $questionCommonSecret);
        }
        
        $repositories = FS::getRepositories($this->rootDir);
        
        $repository = (new Repository())->fromArray($repositories[$input->getArgument('repositoryId')]);
        
        $currentGitRevision = shell_exec("cd {$repository->getLocalFolder()}; git rev-parse --verify HEAD;");
        $currentBranch = str_replace(array("* ", "\n"), array("", ""), shell_exec("cd {$repository->getLocalFolder()}; git branch | grep \*;"));
        
        $servers = array();
        foreach($repository->getRemotes() as $remote)
        {
            list($serverArray, $remoteFolder) = $remote;
            $serverObject = (new Server())->fromArray($serverArray);
            if(!$input->getOption('commonSecret'))
            {
                $questionSSHSecret = new Question("Specify the Secret for the connection for {$serverObject->getName()}: ");
                $questionSSHSecret->setValidator(function ($answer) {
                    if (trim($answer) == '') {
                        throw new \Exception('The secret can not be empty');
                    }

                    return $answer;
                });
                $questionSSHSecret->setHidden(true);
                $questionSSHSecret->setMaxAttempts(2);
                $secret = $helper->ask($input, $output, $questionSSHSecret);
            }
            $servers[] = array($serverObject, $remoteFolder, $secret);
            if($serverObject->getKnockingSequence())
            {
                foreach($serverObject->getKnockingSequence() as $knock)
                {
                    shell_exec(sprintf("knock -v {$serverObject->getAddress()} %s:tcp", $knock));
                }
            }
            
            $remoteGitRevision = shell_exec($this->getCommand($serverObject, $secret, "cd {$remoteFolder}; git rev-parse --verify HEAD;"));
            $this->checkResponse($remoteGitRevision);
            
            $remoteGitBranch = str_replace(array("* ", "\n"), array("", ""), shell_exec($this->getCommand($serverObject, $secret, "cd {$remoteFolder}; git branch | grep \*;")));
            $this->checkResponse($remoteGitBranch);
            
            if($remoteGitBranch === $currentBranch)
            {
                if($currentGitRevision === $remoteGitRevision)
                {
                    $io->success('No changes!');
                }
                else
                {
                    //pull
                    $io->warning('Not Implemented: Pull');
                }
            }
            else
            {
                //Change current branch
                shell_exec("cd {$repository->getLocalFolder()}; git checkout {$remoteGitBranch};");
                //Get Revision
                $branchGitRevision = shell_exec("cd {$repository->getLocalFolder()}; git rev-parse --verify HEAD;");
                //Restore branch
                shell_exec("cd {$repository->getLocalFolder()}; git checkout {$currentBranch};");
                //Compare Revisions
                if($branchGitRevision === $remoteGitRevision)
                {
                    $io->success('No changes!');
                }
                else
                {
                    //pull
                    $io->warning('Not Implemented: Pull');
                }
            }
        }
    }
    
    private function getCommand(ServerInterface $serverObject, $secret, $command)
    {
        return sprintf("sshpass -p '%s' ssh %s@%s -p%s '%s'", $serverObject->getPassword($secret), $serverObject->getUser(), $serverObject->getAddress(), $serverObject->getPort(), $command);
    }

    private function checkResponse($response)
    {
        if(is_null($response) || empty($response))
        {
            throw new \Exception('Probably wrong secret/password');
        }
    }
}
