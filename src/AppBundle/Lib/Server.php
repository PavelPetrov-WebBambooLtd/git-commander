<?php

namespace AppBundle\Lib;

class Server implements ServerInterface
{
    private $name;
    private $address;
    private $port;
    private $user;
    private $password;
    private $knockingSequence = array();

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setPassword($password, $secret, $method = 'AES-256-CBC')
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $this->password = $iv.openssl_encrypt($password, $method, $secret, 0, $iv);

        return $this;
    }

    public function getPassword($secret, $method = 'AES-256-CBC')
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = substr($this->password, 0, $iv_size);

        return openssl_decrypt(substr($this->password, $iv_size), $method, $secret, 0, $iv);
    }

    public function setKnockingSequence($knockingString)
    {
        $ks = explode(',', $knockingString);
        if (count($ks) > 1) {
            $this->knockingSequence = array_map('trim', $ks);
        } else {
            $this->knockingSequence = false;
        }

        return $this;
    }

    public function getKnockingSequence()
    {
        return $this->knockingSequence;
    }

    public function toArray()
    {
        return array(
            $this->name,
            $this->address,
            $this->port,
            $this->user,
            base64_encode($this->password),
            $this->knockingSequence,
        );
    }

    public function fromArray($array)
    {
        list($name, $address, $port, $user, $bpass, $knockingSequence) = $array;
        $this->name = $name;
        $this->address = $address;
        $this->port = $port;
        $this->user = $user;
        $this->password = base64_decode($bpass);
        $this->knockingSequence = $knockingSequence;

        return $this;
    }
}
