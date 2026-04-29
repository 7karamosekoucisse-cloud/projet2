<?php
require_once '../classes/Livre.php';
require_once '../classes/Auteur.php';
require_once '../classes/Categorie.php';

$livreModel  = new Livre();
$auteurModel = new Auteur();
$catModel    = new Categorie();

$message     = '';
$messageType = '';
$editData    = null;
$action      = $_GET['action'] ?? 'list';

// ── Traitement POST ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre       = trim($_POST['titre']       ?? '');
    $isbn        = trim($_POST['isbn']        ?? '');
    $annee       = (int)($_POST['annee']      ?? 0);
    $quantite    = (int)($_POST['quantite']   ?? 1);
    $auteur_id   = (int)($_POST['auteur_id']  ?? 0);
    $categorie_id= (int)($_POST['categorie_id']?? 0);

    if ($titre === '' || $auteur_id === 0) {
        $message     = 'Le titre et l\'auteur sont obligatoires.';
        $messageType = 'danger';
    } else {
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $ok = $livreModel->update((int)$_POST['id'], $titre, $isbn, $annee, $quantite, $auteur_id, $categorie_id);
            $message     = $ok ? 'Livre modifié avec succès.' : 'Erreur de modification.';
            $messageType = $ok ? 'success' : 'danger';
        } else {
            $ok = $livreModel->create($titre, $isbn, $annee, $quantite, $auteur_id, $categorie_id);
            $message     = $ok ? 'Livre ajouté avec succès.' : 'Erreur d\'ajout.';
            $messageType = $ok ? 'success' : 'danger';
        }
        $action = 'list';
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $ok          = $livreModel->delete((int)$_GET['id']);
    $message     = $ok ? 'Livre supprimé.' : 'Erreur de suppression.';
    $messageType = $ok ? 'success' : 'danger';
    $action      = 'list';
}

if ($action === 'edit' && isset($_GET['id'])) {
    $editData = $livreModel->getById((int)$_GET['id']);
    if (!$editData) { $action = 'list'; }
}

// ── Recherche ────────────────────────────────────────────────────────────────
$search  = trim($_GET['q'] ?? '');
$livres  = $search !== '' ? $livreModel->search($search) : $livreModel->getAll();
$auteurs = $auteurModel->getAll();
$categories = $catModel->getAll();

$activePage = 'livres';
$pageTitle  = 'Livres';
require_once '../layout/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Formulaire -->
<div class="card">
    <div class="card-header">
        <h2><?= $editData ? '✏️ Modifier le livre' : '➕ Ajouter un livre' ?></h2>
    </div>
    <form method="POST" action="livres.php">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" required
                       value="<?= htmlspecialchars($editData['titre'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="isbn">ISBN</label>
                <input type="text" id="isbn" name="isbn"
                       value="<?= htmlspecialchars($editData['isbn'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="annee">Année de publication</label>
                <input type="number" id="annee" name="annee" min="1000" max="<?= date('Y') ?>"
                       value="<?= htmlspecialchars($editData['annee'] ?? date('Y')) ?>">
            </div>
            <div class="form-group">
                <label for="quantite">Quantité</label>
                <input type="number" id="quantite" name="quantite" min="0"
                       value="<?= htmlspecialchars($editData['quantite'] ?? 1) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="auteur_id">Auteur *</label>
                <select id="auteur_id" name="auteur_id" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($auteurs as $a): ?>
                        <option value="<?= $a['id'] ?>"
                            <?= (isset($editData['auteur_id']) && $editData['auteur_id'] == $a['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="categorie_id">Catégorie</label>
                <select id="categorie_id" name="categorie_id">
                    <option value="0">— Aucune —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= (isset($editData['categorie_id']) && $editData['categorie_id'] == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= $editData ? '💾 Enregistrer' : '➕ Ajouter' ?>
            </button>
            <?php if ($editData): ?>
                <a href="livres.php" class="btn btn-outline">Annuler</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Recherche + liste -->
<div class="card">
    <div class="card-header">
        <h2>📚 Catalogue (<?= count($livres) ?> livre<?= count($livres) > 1 ? 's' : '' ?>)</h2>
    </div>
    <form method="GET" action="livres.php" class="search-bar">
        <input type="text" name="q" placeholder="Rechercher par titre, ISBN, auteur…"
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary btn-sm">🔍 Rechercher</button>
        <?php if ($search): ?>
            <a href="livres.php" class="btn btn-outline btn-sm">✖ Effacer</a>
        <?php endif; ?>
    </form>

    <?php if (empty($livres)): ?>
        <p style="color:var(--muted);text-align:center;padding:1.5rem 0;">Aucun livre trouvé.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Titre</th><th>ISBN</th><th>Année</th>
                    <th>Qté</th><th>Auteur</th><th>Catégorie</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $l): ?>
                <tr>
                    <td><?= $l['id'] ?></td>
                    <td><?= htmlspecialchars($l['titre']) ?></td>
                    <td><?= htmlspecialchars($l['isbn'] ?? '—') ?></td>
                    <td><?= $l['annee'] ?: '—' ?></td>
                    <td>
                        <span class="badge <?= $l['quantite'] > 0 ? 'badge-success' : 'badge-danger' ?>">
                            <?= $l['quantite'] ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($l['auteur_nom'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($l['categorie_libelle'] ?? '—') ?></td>
                    <td>
                        <div class="actions">
                            <a href="livres.php?action=edit&id=<?= $l['id'] ?>"
                               class="btn btn-warning btn-sm">✏️</a>
                            <a href="livres.php?action=delete&id=<?= $l['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer ce livre ?')">🗑️</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once '../layout/footer.php'; ?>
