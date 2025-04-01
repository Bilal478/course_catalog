<?php

class Database {
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=db;dbname=course_catalog",
                    "test_user",
                    "test_password",
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die(json_encode(['error' => 'Database connection failed']));
            }
        }
        return self::$connection;
    }
}