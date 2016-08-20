<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use AppBundle\Lib\Server;
use AppBundle\Lib\FS;

class CommanderAddServerCommand extends ContainerAwareCommand
{
    private $rootDir;

    protected function configure()
    {
        $this
            ->setName('commander:add-server')
            ->setDescription('Add server wizard')
            ->setAliases(array('add-server'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->get('kernel')->getRootDir().'/../';

        $helper = $this->getHelper('question');
        //Server name
        $questionServerName = new Question('Please enter the name of the server: ');
        $questionServerName->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException(
                    'The name of the server cannot be empty'
                );
            }

            return $answer;
        });
        $questionServerName->setMaxAttempts(2);
        $serverName = $helper->ask($input, $output, $questionServerName);
        //Server address
        $questionServerAddress = new Question('Please enter the server address: ');
        $questionServerAddress->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException(
                    'The server address cannot be empty'
                );
            }

            return $answer;
        });
        $questionServerAddress->setMaxAttempts(2);
        $serverAddress = $helper->ask($input, $output, $questionServerAddress);
        //SSH Port
        $questionSSHPort = new Question('Specify the SSH Port for the connection: ');
        $questionSSHPort->setValidator(function ($answer) {
            if (!is_numeric($answer)) {
                throw new \RuntimeException(
                    'The SSH port must be a number'
                );
            }

            return $answer;
        });
        $questionSSHPort->setMaxAttempts(2);
        $sshPort = $helper->ask($input, $output, $questionSSHPort);
        //SSH User
        $questionSSHUser = new Question('Specify the SSH User for the connection: ');
        $questionSSHUser->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException(
                    'The SSH user can`t be empty'
                );
            }

            return $answer;
        });
        $questionSSHUser->setMaxAttempts(2);
        $sshUser = $helper->ask($input, $output, $questionSSHUser);
        //SSH Password
        $questionSSHPassword = new Question('Specify the SSH Password for the connection: ');
        $questionSSHPassword->setValidator(function ($answer) {
            if (trim($answer) == '') {
                throw new \Exception('The SSH password can not be empty');
            }

            return $answer;
        });
        $questionSSHPassword->setHidden(true);
        $questionSSHPassword->setMaxAttempts(2);
        $sshPassword = $helper->ask($input, $output, $questionSSHPassword);
        //Secret
        $questionSSHSecret = new Question('Specify the Secret for the connection: ');
        $questionSSHSecret->setValidator(function ($answer) {
            if (trim($answer) == '') {
                throw new \Exception('The secret can not be empty');
            }

            return $answer;
        });
        $questionSSHSecret->setHidden(true);
        $questionSSHSecret->setMaxAttempts(2);
        $secret = $helper->ask($input, $output, $questionSSHSecret);
        //Knocking sequence
        $questionKnockingSequence = new Question('Please enter the knocking sequence, separated with comma. Eg. "6123,4567,1235": ', false);
        $questionKnockingSequence->setMaxAttempts(2);
        $knockingSequence = $helper->ask($input, $output, $questionKnockingSequence);

        $server = new Server();
        $server->setName($serverName)
                ->setAddress($serverAddress)
                ->setPort($sshPort)
                ->setUser($sshUser)
                ->setPassword($sshPassword, $secret)
                ->setKnockingSequence($knockingSequence);

        $currentServers = FS::getServers($this->rootDir);
        if (!$currentServers) {
            $currentServers = array();
        }
        dump($server->toArray());
        $currentServers[] = $server->toArray();
        if (FS::updateServers($this->rootDir, $currentServers)) {
            $io->success('Server Added :)');
        } else {
            $io->warning('Something Failed! :(');
        }
    }
}
