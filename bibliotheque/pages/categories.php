<?php
require_once '../classes/Categorie.php';

$catModel    = new Categorie();
$message     = '';
$messageType = '';
$editData    = null;
$action      = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = trim($_POST['libelle'] ?? '');
    if ($libelle === '') {
        $message     = 'Le libellé est obligatoire.';
        $messageType = 'danger';
    } else {
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $ok = $catModel->update((int)$_POST['id'], $libelle);
            $message     = $ok ? 'Catégorie modifiée.' : 'Erreur de modification.';
            $messageType = $ok ? 'success' : 'danger';
        } else {
            $ok = $catModel->create($libelle);
            $message     = $ok ? 'Catégorie ajoutée.' : 'Erreur d\'ajout.';
            $messageType = $ok ? 'success' : 'danger';
        }
        $action = 'list';
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $ok          = $catModel->delete((int)$_GET['id']);
    $message     = $ok ? 'Catégorie supprimée.' : 'Impossible de supprimer (livres liés ?).';
    $messageType = $ok ? 'success' : 'danger';
    $action      = 'list';
}

if ($action === 'edit' && isset($_GET['id'])) {
    $editData = $catModel->getById((int)$_GET['id']);
    if (!$editData) { $action = 'list'; }
}

$categories = $catModel->getAll();
$activePage = 'categories';
$pageTitle  = 'Catégories';
require_once '../layout/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2><?= $editData ? '✏️ Modifier la catégorie' : '➕ Ajouter une catégorie' ?></h2>
    </div>
    <form method="POST" action="categories.php">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>
        <div class="form-group" style="max-width:400px">
            <label for="libelle">Libellé *</label>
            <input type="text" id="libelle" name="libelle" required
                   value="<?= htmlspecialchars($editData['libelle'] ?? '') ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= $editData ? '💾 Enregistrer' : '➕ Ajouter' ?>
            </button>
            <?php if ($editData): ?>
                <a href="categories.php" class="btn btn-outline">Annuler</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>📋 Liste des catégories (<?= count($categories) ?>)</h2>
    </div>
    <?php if (empty($categories)): ?>
        <p style="color:var(--muted);text-align:center;padding:1.5rem 0;">Aucune catégorie enregistrée.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Libellé</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['libelle']) ?></td>
                    <td>
                        <div class="actions">
                            <a href="categories.php?action=edit&id=<?= $c['id'] ?>"
                               class="btn btn-warning btn-sm">✏️ Modifier</a>
                            <a href="categories.php?action=delete&id=<?= $c['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer cette catégorie ?')">🗑️ Supprimer</a>
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
