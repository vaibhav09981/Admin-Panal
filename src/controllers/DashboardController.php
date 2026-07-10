<?php

namespace App\Controllers;

use App\Database;
use PDO;

class DashboardController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function stats(): array
    {
        $users = $this->pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'];
        $items = $this->pdo->query("SELECT COUNT(*) AS c FROM items")->fetch()['c'];
        $stock = $this->pdo->query("SELECT COALESCE(SUM(stock),0) AS t FROM items")->fetch()['t'];
        $value = $this->pdo->query("SELECT COALESCE(SUM(price * stock),0) AS t FROM items")->fetch()['t'];

        return [
            'total_users' => (int) $users,
            'total_items' => (int) $items,
            'total_stock' => (int) $stock,
            'inventory_value' => (float) $value,
        ];
    }
}
