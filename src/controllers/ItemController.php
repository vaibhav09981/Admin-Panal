<?php

namespace App\Controllers;

use App\Database;
use PDO;

class ItemController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function index(): array
    {
        return $this->pdo
            ->query("SELECT id, name, description, price, stock, created_at FROM items ORDER BY id ASC")
            ->fetchAll();
    }

    public function show(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, name, description, price, stock, created_at FROM items WHERE id = ?"
        );
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        return $item ?: null;
    }

    public function store(array $data): array
    {
        if (empty($data['name'])) {
            throw new \Exception("name is required", 400);
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO items (name, description, price, stock) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?? '',
            $data['price'] ?? 0,
            $data['stock'] ?? 0,
        ]);

        return $this->show((int) $this->pdo->lastInsertId());
    }

    public function update(int $id, array $data): array
    {
        $current = $this->show($id);
        if (!$current) {
            throw new \Exception("Item not found", 404);
        }

        $name = $data['name'] ?? $current['name'];
        $description = $data['description'] ?? $current['description'];
        $price = $data['price'] ?? $current['price'];
        $stock = $data['stock'] ?? $current['stock'];

        $stmt = $this->pdo->prepare(
            "UPDATE items SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?"
        );
        $stmt->execute([$name, $description, $price, $stock, $id]);

        return $this->show($id);
    }

    public function destroy(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = ?");
        $stmt->execute([$id]);
    }
}
