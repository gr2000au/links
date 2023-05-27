<?php
class Database {

    private static $db;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new mysqli(LINKS_DB_HOST, LINKS_DB_USERNAME, LINKS_DB_PASSWORD, LINKS_DB_NAME);
            $this->connection->set_charset("utf8");
        }
        catch(Exception $e) {
            exit('Could not connect to database');
        }
    }

    function __destruct() {
        $this->connection->close();
    }

    public static function getConnection() {
        if (self::$db == null) {
            self::$db = new Database();
        }
        return self::$db->connection;
    }
}
