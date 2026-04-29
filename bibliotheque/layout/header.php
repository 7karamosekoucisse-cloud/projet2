<?php
// layout/header.php  — inclure en passant $activePage et $pageTitle
$activePage = $activePage ?? '';
$pageTitle  = isset($pageTitle) ? "Bibliothèque · $pageTitle" : 'Bibliothèque';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?? '../' ?>assets/css/style.css">
</head>
<body>
<nav>
    <span class="brand">📚 Bibliothèque</span>
    <a href="<?= $baseUrl ?? '../' ?>index.php"             class="<?= $activePage === 'accueil'    ? 'active' : '' ?>">Accueil</a>
    <a href="<?= $baseUrl ?? '../' ?>pages/auteurs.php"     class="<?= $activePage === 'auteurs'    ? 'active' : '' ?>">Auteurs</a>
    <a href="<?= $baseUrl ?? '../' ?>pages/categories.php"  class="<?= $activePage === 'categories' ? 'active' : '' ?>">Catégories</a>
    <a href="<?= $baseUrl ?? '../' ?>pages/livres.php"      class="<?= $activePage === 'livres'     ? 'active' : '' ?>">Livres</a>
    <a href="<?= $baseUrl ?? '../' ?>pages/emprunts.php"    class="<?= $activePage === 'emprunts'   ? 'active' : '' ?>">Emprunts</a>
</nav>
<div class="container">
