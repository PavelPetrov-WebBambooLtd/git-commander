<?php

namespace AppBundle\Lib;

class Repository implements RepositoryInterface
{
    private $name;
    private $localFolder;
    private $remotes = array();

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setLocalFolder($localFolder)
    {
        $this->localFolder = $localFolder;

        return $this;
    }

    public function getLocalFolder()
    {
        return $this->localFolder;
    }

    public function addRemote(ServerInterface $server, $folder, $branch = 'master')
    {
        $this->remotes[] = array($server->toArray(), $folder, $branch);
    }

    public function removeRemote($key)
    {
        unset($this->remotes[$key]);
    }

    public function getRemotes()
    {
        return $this->remotes;
    }

    public function toArray()
    {
        return array(
            $this->name,
            $this->localFolder,
            $this->remotes,
        );
    }

    public function fromArray($array)
    {
        list($name, $localFolder, $remotes) = $array;
        $this->name = $name;
        $this->localFolder = $localFolder;
        $this->remotes = $remotes;

        return $this;
    }
}
