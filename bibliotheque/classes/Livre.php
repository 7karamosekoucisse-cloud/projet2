<?php
require_once __DIR__ . '/../config/database.php';

class Livre {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT l.*, 
                   CONCAT(a.prenom, ' ', a.nom) AS auteur_nom,
                   c.libelle AS categorie_libelle
            FROM livres l
            LEFT JOIN auteurs a    ON l.auteur_id    = a.id
            LEFT JOIN categories c ON l.categorie_id = c.id
            ORDER BY l.titre
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("
            SELECT l.*, 
                   CONCAT(a.prenom, ' ', a.nom) AS auteur_nom,
                   c.libelle AS categorie_libelle
            FROM livres l
            LEFT JOIN auteurs a    ON l.auteur_id    = a.id
            LEFT JOIN categories c ON l.categorie_id = c.id
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $titre, string $isbn, int $annee, int $quantite, int $auteur_id, int $categorie_id): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO livres (titre, isbn, annee, quantite, auteur_id, categorie_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$titre, $isbn, $annee, $quantite, $auteur_id, $categorie_id]);
    }

    public function update(int $id, string $titre, string $isbn, int $annee, int $quantite, int $auteur_id, int $categorie_id): bool {
        $stmt = $this->pdo->prepare("
            UPDATE livres 
            SET titre = ?, isbn = ?, annee = ?, quantite = ?, auteur_id = ?, categorie_id = ?
            WHERE id = ?
        ");
        return $stmt->execute([$titre, $isbn, $annee, $quantite, $auteur_id, $categorie_id, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM livres WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function search(string $keyword): array {
        $kw = "%$keyword%";
        $stmt = $this->pdo->prepare("
            SELECT l.*, 
                   CONCAT(a.prenom, ' ', a.nom) AS auteur_nom,
                   c.libelle AS categorie_libelle
            FROM livres l
            LEFT JOIN auteurs a    ON l.auteur_id    = a.id
            LEFT JOIN categories c ON l.categorie_id = c.id
            WHERE l.titre LIKE ? OR l.isbn LIKE ? OR a.nom LIKE ? OR a.prenom LIKE ?
            ORDER BY l.titre
        ");
        $stmt->execute([$kw, $kw, $kw, $kw]);
        return $stmt->fetchAll();
    }
}
