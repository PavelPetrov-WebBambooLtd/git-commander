<?php

namespace AppBundle\Lib;

class FS
{
    public static function getServers($root)
    {
        return self::getFile($root.'servers.json');
    }

    public static function updateServers($root, $servers)
    {
        return file_put_contents($root.'servers.json', json_encode($servers));
    }

    public static function getRepositories($root)
    {
        return self::getFile($root.'repositories.json');
    }

    public static function updateRepositories($root, $repositories)
    {
        return file_put_contents($root.'repositories.json', json_encode($repositories));
    }

    private static function getFile($path)
    {
        if (file_exists($path)) {
            $serversJson = file_get_contents($path);
            if (!self::isJson($serversJson)) {
                return false;
            }

            return json_decode($serversJson, true);
        }

        return false;
    }

    private static function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }
}
