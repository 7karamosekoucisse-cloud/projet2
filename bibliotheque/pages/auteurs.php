<?php
require_once '../classes/Auteur.php';

$auteurModel = new Auteur();
$message     = '';
$messageType = '';
$editData    = null;
$action      = $_GET['action'] ?? 'list';

// ── Traitement des formulaires ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom']         ?? '');
    $prenom      = trim($_POST['prenom']      ?? '');
    $nationalite = trim($_POST['nationalite'] ?? '');

    if ($nom === '' || $prenom === '') {
        $message     = 'Le nom et le prénom sont obligatoires.';
        $messageType = 'danger';
    } else {
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $ok = $auteurModel->update((int)$_POST['id'], $nom, $prenom, $nationalite);
            $message     = $ok ? 'Auteur modifié avec succès.' : 'Erreur lors de la modification.';
            $messageType = $ok ? 'success' : 'danger';
        } else {
            $ok = $auteurModel->create($nom, $prenom, $nationalite);
            $message     = $ok ? 'Auteur ajouté avec succès.' : 'Erreur lors de l\'ajout.';
            $messageType = $ok ? 'success' : 'danger';
        }
        $action = 'list';
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $ok = $auteurModel->delete((int)$_GET['id']);
    $message     = $ok ? 'Auteur supprimé.' : 'Impossible de supprimer (livres liés ?).';
    $messageType = $ok ? 'success' : 'danger';
    $action      = 'list';
}

if ($action === 'edit' && isset($_GET['id'])) {
    $editData = $auteurModel->getById((int)$_GET['id']);
    if (!$editData) { $action = 'list'; }
}

$auteurs    = $auteurModel->getAll();
$activePage = 'auteurs';
$pageTitle  = 'Auteurs';
require_once '../layout/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Formulaire ajout / édition -->
<div class="card">
    <div class="card-header">
        <h2><?= $editData ? '✏️ Modifier l\'auteur' : '➕ Ajouter un auteur' ?></h2>
    </div>
    <form method="POST" action="auteurs.php">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required
                       value="<?= htmlspecialchars($editData['nom'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" required
                       value="<?= htmlspecialchars($editData['prenom'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="nationalite">Nationalité</label>
            <input type="text" id="nationalite" name="nationalite"
                   value="<?= htmlspecialchars($editData['nationalite'] ?? '') ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= $editData ? '💾 Enregistrer' : '➕ Ajouter' ?>
            </button>
            <?php if ($editData): ?>
                <a href="auteurs.php" class="btn btn-outline">Annuler</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Liste des auteurs -->
<div class="card">
    <div class="card-header">
        <h2>📋 Liste des auteurs (<?= count($auteurs) ?>)</h2>
    </div>
    <?php if (empty($auteurs)): ?>
        <p style="color:var(--muted);text-align:center;padding:1.5rem 0;">Aucun auteur enregistré.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Nom</th><th>Prénom</th><th>Nationalité</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($auteurs as $a): ?>
                <tr>
                    <td><?= $a['id'] ?></td>
                    <td><?= htmlspecialchars($a['nom']) ?></td>
                    <td><?= htmlspecialchars($a['prenom']) ?></td>
                    <td><?= htmlspecialchars($a['nationalite'] ?? '—') ?></td>
                    <td>
                        <div class="actions">
                            <a href="auteurs.php?action=edit&id=<?= $a['id'] ?>"
                               class="btn btn-warning btn-sm">✏️ Modifier</a>
                            <a href="auteurs.php?action=delete&id=<?= $a['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer cet auteur ?')">🗑️ Supprimer</a>
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
