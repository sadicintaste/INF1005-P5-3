<?php

class DBConnect{
    private static $conn = null;

    public static function connect() {
        if (self::$conn === null) {
            $config = parse_ini_file('/var/www/private/db-config.ini');
            
            if (!$config) {
                throw new Exception('Failed to read database config file.');
            }
            
            self::$conn = new mysqli(
                $config['servername'],
                $config['username'],
                $config['password'],
                $config['dbname']
            );
            
            if (self::$conn->connect_error) {
                throw new Exception('Connection failed: ' . self::$conn->connect_error);
            }
        }
        
        return self::$conn;
    }

    public static function close() {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }
}

?>