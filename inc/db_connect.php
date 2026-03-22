<?php

class DBConnect
{
    private static $conn = null;

    public static function connect()
    {
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

    public static function close()
    {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }

    public static function getUserDetails(int $user_id): ?array
    {
        // Create database connection.
        $conn = self::connect();
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT username, email, points FROM User
                                WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: ({$conn->errno}) {$conn->error}");
        }
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: ({$stmt->errno}) {$stmt->error}");
        }
        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            return null;
        }
        return $result->fetch_assoc();
    }
}
