<?php
require_once __DIR__ . '/../config/database.php';

class Emprunt {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT e.*,
                   l.titre AS livre_titre,
                   l.isbn  AS livre_isbn
            FROM emprunts e
            LEFT JOIN livres l ON e.livre_id = l.id
            ORDER BY e.date_emprunt DESC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("
            SELECT e.*,
                   l.titre AS livre_titre
            FROM emprunts e
            LEFT JOIN livres l ON e.livre_id = l.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(int $livre_id, string $emprunteur, string $date_emprunt, string $date_retour_prevue): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO emprunts (livre_id, emprunteur, date_emprunt, date_retour_prevue, retourne)
            VALUES (?, ?, ?, ?, 0)
        ");
        return $stmt->execute([$livre_id, $emprunteur, $date_emprunt, $date_retour_prevue]);
    }

    public function retourner(int $id): bool {
        $stmt = $this->pdo->prepare("
            UPDATE emprunts SET retourne = 1, date_retour_effective = CURDATE() WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM emprunts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
