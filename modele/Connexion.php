<?php

class Connexion {
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getConnexion(): PDO {
        if (self::$instance === null) {
            $login   = "martinr";
            $mdp     = "03052006";
            $bd      = "martinr_dbBat";
            $serveur = "192.168.20.15";

            self::$instance = new PDO(
                "mysql:host=$serveur;dbname=$bd;charset=utf8",
                $login,
                $mdp,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        }
        return self::$instance;
    }
}
