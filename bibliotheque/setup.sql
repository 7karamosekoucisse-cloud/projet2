-- ============================================================
--  TP Bibliothèque — Script SQL complet
--  Base de données : bibliotheque
-- ============================================================

CREATE DATABASE IF NOT EXISTS bibliotheque
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bibliotheque;

-- ── Tables ──────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS auteurs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    prenom      VARCHAR(100) NOT NULL,
    nationalite VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS livres (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    titre        VARCHAR(150) NOT NULL,
    isbn         VARCHAR(50),
    annee        INT,
    quantite     INT DEFAULT 1,
    auteur_id    INT,
    categorie_id INT,
    CONSTRAINT fk_livre_auteur    FOREIGN KEY (auteur_id)    REFERENCES auteurs(id)    ON DELETE SET NULL,
    CONSTRAINT fk_livre_categorie FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS emprunts (
    id                   INT AUTO_INCREMENT PRIMARY KEY,
    livre_id             INT,
    emprunteur           VARCHAR(150) NOT NULL,
    date_emprunt         DATE NOT NULL,
    date_retour_prevue   DATE NOT NULL,
    date_retour_effective DATE,
    retourne             TINYINT(1) DEFAULT 0,
    CONSTRAINT fk_emprunt_livre FOREIGN KEY (livre_id) REFERENCES livres(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Données de démonstration ─────────────────────────────────

INSERT INTO auteurs (nom, prenom, nationalite) VALUES
    ('Hugo',      'Victor',    'Française'),
    ('Camus',     'Albert',    'Française'),
    ('Tolkien',   'J.R.R.',    'Britannique'),
    ('Orwell',    'George',    'Britannique'),
    ('Dostoïevski','Fiodor',   'Russe');

INSERT INTO categories (libelle) VALUES
    ('Roman'),
    ('Science-fiction'),
    ('Fantasy'),
    ('Philosophie'),
    ('Classique');

INSERT INTO livres (titre, isbn, annee, quantite, auteur_id, categorie_id) VALUES
    ('Les Misérables',           '978-2-07-040850-4', 1862, 3, 1, 1),
    ('Notre-Dame de Paris',      '978-2-07-036835-0', 1831, 2, 1, 5),
    ('L''Étranger',              '978-2-07-036024-8', 1942, 4, 2, 1),
    ('La Peste',                 '978-2-07-036024-9', 1947, 2, 2, 1),
    ('Le Seigneur des Anneaux',  '978-2-07-061271-1', 1954, 5, 3, 3),
    ('1984',                     '978-0-45-228423-4', 1949, 3, 4, 2),
    ('La Ferme des animaux',     '978-0-14-028381-4', 1945, 2, 4, 1),
    ('Crime et Châtiment',       '978-2-07-036022-4', 1866, 1, 5, 5);

INSERT INTO emprunts (livre_id, emprunteur, date_emprunt, date_retour_prevue, retourne) VALUES
    (1, 'Aminata Diallo',  CURDATE(),                  DATE_ADD(CURDATE(), INTERVAL 14 DAY), 0),
    (5, 'Moussa Sow',      DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY),  0),
    (3, 'Fatou Ndiaye',    DATE_SUB(CURDATE(), INTERVAL 30 DAY), DATE_SUB(CURDATE(), INTERVAL 16 DAY), 1);

-- Marquer le retour du dernier emprunt
UPDATE emprunts
SET retourne = 1, date_retour_effective = DATE_SUB(CURDATE(), INTERVAL 14 DAY)
WHERE id = 3;
