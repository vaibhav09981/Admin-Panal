<?php

namespace App;

use PDO;

class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dbFile = __DIR__ . '/../data/app.db';
        self::$pdo = new PDO('sqlite:' . $dbFile);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        self::migrate();
        self::seed();

        return self::$pdo;
    }

    private static function migrate(): void
    {
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                price REAL DEFAULT 0,
                stock INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    private static function seed(): void
    {
        $count = self::$pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'];
        if ($count > 0) {
            return;
        }

        $stmt = self::$pdo->prepare(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute(['Admin', 'admin@example.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin']);

        $item = self::$pdo->prepare(
            "INSERT INTO items (name, description, price, stock) VALUES (?, ?, ?, ?)"
        );
        $item->execute(['Sample Widget', 'A demo product', 19.99, 42]);
        $item->execute(['Sample Gadget', 'Another demo product', 49.50, 10]);
    }
}
