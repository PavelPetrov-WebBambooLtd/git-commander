<?php

namespace AppBundle\Lib;

interface ServerInterface
{
    public function setName($name);
    public function getName();
    public function setAddress($address);
    public function getAddress();
    public function setPort($port);
    public function getPort();
    public function setUser($user);
    public function getUser();
    public function setPassword($password, $secret, $method = 'AES-256-CBC');
    public function getPassword($secret, $method = 'AES-256-CBC');
    public function setKnockingSequence($knockingString);
    public function getKnockingSequence();
    public function toArray();
    public function fromArray($array);
}
