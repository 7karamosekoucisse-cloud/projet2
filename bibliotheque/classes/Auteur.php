<?php
require_once __DIR__ . '/../config/database.php';

class Auteur {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM auteurs ORDER BY nom, prenom");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM auteurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $nom, string $prenom, string $nationalite): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO auteurs (nom, prenom, nationalite) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$nom, $prenom, $nationalite]);
    }

    public function update(int $id, string $nom, string $prenom, string $nationalite): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE auteurs SET nom = ?, prenom = ?, nationalite = ? WHERE id = ?"
        );
        return $stmt->execute([$nom, $prenom, $nationalite, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM auteurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
