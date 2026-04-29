<?php
require_once 'config/database.php';

$pdo = Database::getInstance();

$nbAuteurs    = $pdo->query("SELECT COUNT(*) FROM auteurs")->fetchColumn();
$nbCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$nbLivres     = $pdo->query("SELECT COUNT(*) FROM livres")->fetchColumn();
$nbEmprunts   = $pdo->query("SELECT COUNT(*) FROM emprunts WHERE retourne = 0")->fetchColumn();

$recentLivres = $pdo->query("
    SELECT l.titre, CONCAT(a.prenom,' ',a.nom) AS auteur, c.libelle AS categorie
    FROM livres l
    LEFT JOIN auteurs a    ON l.auteur_id    = a.id
    LEFT JOIN categories c ON l.categorie_id = c.id
    ORDER BY l.id DESC LIMIT 5
")->fetchAll();

$activePage = 'accueil';
$pageTitle  = 'Accueil';
$baseUrl    = '';
require_once 'layout/header.php';
?>

<div class="stats">
    <div class="stat-card">
        <div class="value"><?= $nbAuteurs ?></div>
        <div class="label">📝 Auteurs</div>
    </div>
    <div class="stat-card green">
        <div class="value"><?= $nbCategories ?></div>
        <div class="label">🏷️ Catégories</div>
    </div>
    <div class="stat-card orange">
        <div class="value"><?= $nbLivres ?></div>
        <div class="label">📖 Livres</div>
    </div>
    <div class="stat-card red">
        <div class="value"><?= $nbEmprunts ?></div>
        <div class="label">🔖 Emprunts en cours</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Derniers livres ajoutés</h2>
        <a href="pages/livres.php" class="btn btn-outline btn-sm">Voir tous</a>
    </div>
    <?php if (empty($recentLivres)): ?>
        <p style="color:var(--muted);text-align:center;padding:1.5rem 0;">Aucun livre enregistré pour l'instant.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Titre</th><th>Auteur</th><th>Catégorie</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentLivres as $l): ?>
                <tr>
                    <td><?= htmlspecialchars($l['titre']) ?></td>
                    <td><?= htmlspecialchars($l['auteur']) ?></td>
                    <td><?= htmlspecialchars($l['categorie'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'layout/footer.php'; ?>
