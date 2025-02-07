<?php


namespace App;

use \PDO;

class Connection
{

    const ADMINER_PASSWORD = "root";

    public static function getPDO(): PDO
    {

        $user = 'root';
        $password = self::ADMINER_PASSWORD;

        return new PDO('mysql:host=localhost;dbname=portail_massoutre', $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public static function getPDO_2(): PDO
    {
        $user = 'root';
        $password = self::ADMINER_PASSWORD;

        return new PDO('mysql:host=localhost;dbname=massoutre', $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }



    public static function getPDO_starterre(): PDO
    {
        $user = 'root';
        $password = self::ADMINER_PASSWORD;

        return new PDO('mysql:host=localhost;dbname=starterre', $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public static function getPDO_starterre_prod(): PDO
    {
        $user = 'root';
        $password = self::ADMINER_PASSWORD;

        return new PDO('mysql:host=localhost;dbname=starterre_prod', $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }


}