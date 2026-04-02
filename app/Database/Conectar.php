<?php
require_once __DIR__ . '/../../config.php';

class Database {
    private static $pdo;

    public static function getInstance() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO('sqlite:' . DB_PATH);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

function getDB() {
    return Database::getInstance();
}
