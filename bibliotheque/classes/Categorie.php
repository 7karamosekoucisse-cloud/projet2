<?php
require_once __DIR__ . '/../config/database.php';

class Categorie {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY libelle");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $libelle): bool {
        $stmt = $this->pdo->prepare("INSERT INTO categories (libelle) VALUES (?)");
        return $stmt->execute([$libelle]);
    }

    public function update(int $id, string $libelle): bool {
        $stmt = $this->pdo->prepare("UPDATE categories SET libelle = ? WHERE id = ?");
        return $stmt->execute([$libelle, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
