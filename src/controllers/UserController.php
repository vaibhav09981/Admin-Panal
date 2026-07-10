<?php

namespace App\Controllers;

use App\Database;
use PDO;

class UserController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function index(): array
    {
        return $this->pdo
            ->query("SELECT id, name, email, role, created_at FROM users ORDER BY id ASC")
            ->fetchAll();
    }

    public function show(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function store(array $data): array
    {
        $this->validate($data, true);
        $exists = $this->pdo->prepare("SELECT id FROM users WHERE email = ?")->fetch(PDO::FETCH_ASSOC);
        if ($exists) {
            throw new \Exception("Email already in use", 409);
        }

        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$data['name'], $data['email'], $hash, $data['role'] ?? 'user']);

        return $this->show((int) $this->pdo->lastInsertId());
    }

    public function update(int $id, array $data): array
    {
        $current = $this->show($id);
        if (!$current) {
            throw new \Exception("User not found", 404);
        }

        $name = $data['name'] ?? $current['name'];
        $email = $data['email'] ?? $current['email'];
        $role = $data['role'] ?? $current['role'];
        $hash = isset($data['password']) && $data['password'] !== ''
            ? password_hash($data['password'], PASSWORD_DEFAULT)
            : $current['password'];

        $stmt = $this->pdo->prepare(
            "UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?"
        );
        $stmt->execute([$name, $email, $hash, $role, $id]);

        return $this->show($id);
    }

    public function destroy(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function validate(array $data, bool $requirePassword): void
    {
        if (empty($data['name']) || empty($data['email'])) {
            throw new \Exception("name and email are required", 400);
        }
        if ($requirePassword && empty($data['password'])) {
            throw new \Exception("password is required", 400);
        }
    }
}
