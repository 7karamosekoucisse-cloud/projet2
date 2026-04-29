# TP PHP POO & PDO — Gestion de Bibliothèque

## 📁 Structure du projet

```
bibliotheque/
├── config/
│   └── database.php          ← Connexion PDO (Singleton)
├── classes/
│   ├── Auteur.php             ← CRUD auteurs
│   ├── Categorie.php          ← CRUD catégories
│   ├── Livre.php              ← CRUD livres (avec JOIN)
│   └── Emprunt.php            ← CRUD emprunts
├── pages/
│   ├── auteurs.php            ← Interface auteurs
│   ├── categories.php         ← Interface catégories
│   ├── livres.php             ← Interface livres + recherche
│   └── emprunts.php           ← Interface emprunts
├── layout/
│   ├── header.php             ← Navigation HTML
│   └── footer.php             ← Pied de page
├── assets/
│   └── css/
│       └── style.css          ← Feuille de style
├── index.php                  ← Tableau de bord
├── setup.sql                  ← Script SQL (schéma + données de test)
└── README.md
```

## 🚀 Installation

### 1. Base de données
```bash
mysql -u root -p < setup.sql
```
Ou importer `setup.sql` via phpMyAdmin.

### 2. Configuration
Modifier `config/database.php` si besoin :
```php
private string $host     = 'localhost';
private string $dbname   = 'bibliotheque';
private string $user     = 'root';
private string $password = '';
```

### 3. Serveur PHP
```bash
# Depuis le dossier du projet :
php -S localhost:8000
```
Puis ouvrir : http://localhost:8000

Ou placer le dossier dans `htdocs/` (XAMPP) ou `www/` (WAMP).

## ✅ Fonctionnalités implémentées

| Module       | Lister | Ajouter | Modifier | Supprimer | Extra                        |
|--------------|--------|---------|----------|-----------|------------------------------|
| Auteurs      | ✅     | ✅      | ✅       | ✅        | —                            |
| Catégories   | ✅     | ✅      | ✅       | ✅        | —                            |
| Livres       | ✅     | ✅      | ✅       | ✅        | Recherche plein texte, JOIN  |
| Emprunts     | ✅     | ✅      | —        | ✅        | Retour livre, détection retard|

## 🔒 Contraintes respectées

- ✅ **PDO** avec DSN MySQL
- ✅ **Requêtes préparées** (`prepare` / `execute`) — aucune concaténation SQL
- ✅ **POO** : classes séparées par entité, méthodes CRUD, Singleton pour la connexion
- ✅ **Interface HTML/CSS** responsive avec navigation, formulaires et tableaux
