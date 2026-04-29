<?php
class Database {
    private static ?PDO $instance = null;

    private string $host     = 'localhost';
    private string $dbname   = 'bibliotheque';
    private string $user     = 'root';
    private string $password = '';
    private string $charset  = 'utf8mb4';

    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $db = new self();
            $dsn = "mysql:host={$db->host};dbname={$db->dbname};charset={$db->charset}";
            try {
                self::$instance = new PDO($dsn, $db->user, $db->password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
