<?php

namespace AppBundle\Lib;

interface RepositoryInterface
{
    public function setName($name);
    public function getName();
    public function setLocalFolder($localFolder);
    public function getLocalFolder();
    public function addRemote(ServerInterface $server, $folder);
    public function removeRemote($key);
    public function getRemotes();
    public function toArray();
    public function fromArray($array);
}
