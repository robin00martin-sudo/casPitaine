<?php
/**
 * Connexion PDO en singleton + lecture de la configuration.
 * Les paramètres viennent de config.local.php (non versionné) ou, à défaut,
 * des variables d'environnement DB_HOST, DB_NAME, DB_USER, DB_PASS, ADMIN_HASH.
 */
class Connexion {

    private static ?PDO $instance = null;

    private function __construct() {}

    /** Configuration complète (base de données + hash du mot de passe administrateur). */
    public static function config(): array {
        $fichier = __DIR__ . '/../config.local.php';
        if (is_file($fichier)) {
            return require $fichier;
        }
        return [
            'host'       => getenv('DB_HOST') ?: 'localhost',
            'dbname'     => getenv('DB_NAME') ?: 'dbBat',
            'user'       => getenv('DB_USER') ?: 'root',
            'pass'       => getenv('DB_PASS') ?: '',
            'admin_hash' => getenv('ADMIN_HASH') ?: '',
        ];
    }

    public static function getConnexion(): PDO {
        if (self::$instance === null) {
            $cfg = self::config();
            self::$instance = new PDO(
                "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8mb4",
                $cfg['user'],
                $cfg['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        }
        return self::$instance;
    }
}
