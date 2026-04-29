<?php
require_once '../classes/Emprunt.php';
require_once '../classes/Livre.php';

$empruntModel = new Emprunt();
$livreModel   = new Livre();

$message     = '';
$messageType = '';
$action      = $_GET['action'] ?? 'list';

// ── Traitement POST ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livre_id          = (int)($_POST['livre_id']          ?? 0);
    $emprunteur        = trim($_POST['emprunteur']         ?? '');
    $date_emprunt      = $_POST['date_emprunt']            ?? date('Y-m-d');
    $date_retour_prevue= $_POST['date_retour_prevue']      ?? '';

    if ($livre_id === 0 || $emprunteur === '' || $date_retour_prevue === '') {
        $message     = 'Tous les champs sont obligatoires.';
        $messageType = 'danger';
    } else {
        $ok = $empruntModel->create($livre_id, $emprunteur, $date_emprunt, $date_retour_prevue);
        $message     = $ok ? 'Emprunt enregistré.' : 'Erreur lors de l\'enregistrement.';
        $messageType = $ok ? 'success' : 'danger';
        $action      = 'list';
    }
}

if ($action === 'retour' && isset($_GET['id'])) {
    $ok          = $empruntModel->retourner((int)$_GET['id']);
    $message     = $ok ? 'Livre marqué comme retourné.' : 'Erreur.';
    $messageType = $ok ? 'success' : 'danger';
    $action      = 'list';
}

if ($action === 'delete' && isset($_GET['id'])) {
    $ok          = $empruntModel->delete((int)$_GET['id']);
    $message     = $ok ? 'Emprunt supprimé.' : 'Erreur de suppression.';
    $messageType = $ok ? 'success' : 'danger';
    $action      = 'list';
}

$emprunts   = $empruntModel->getAll();
$livres     = $livreModel->getAll();
$today      = date('Y-m-d');
$retourMax  = date('Y-m-d', strtotime('+30 days'));

$activePage = 'emprunts';
$pageTitle  = 'Emprunts';
require_once '../layout/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Formulaire nouvel emprunt -->
<div class="card">
    <div class="card-header"><h2>➕ Nouvel emprunt</h2></div>
    <form method="POST" action="emprunts.php">
        <div class="form-row">
            <div class="form-group">
                <label for="livre_id">Livre *</label>
                <select id="livre_id" name="livre_id" required>
                    <option value="">— Sélectionner un livre —</option>
                    <?php foreach ($livres as $l): ?>
                        <option value="<?= $l['id'] ?>">
                            <?= htmlspecialchars($l['titre']) ?>
                            (dispo : <?= $l['quantite'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="emprunteur">Nom de l'emprunteur *</label>
                <input type="text" id="emprunteur" name="emprunteur" required
                       placeholder="Prénom Nom">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="date_emprunt">Date d'emprunt *</label>
                <input type="date" id="date_emprunt" name="date_emprunt"
                       value="<?= $today ?>" required>
            </div>
            <div class="form-group">
                <label for="date_retour_prevue">Date de retour prévue *</label>
                <input type="date" id="date_retour_prevue" name="date_retour_prevue"
                       value="<?= $retourMax ?>" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">📋 Enregistrer l'emprunt</button>
        </div>
    </form>
</div>

<!-- Liste des emprunts -->
<div class="card">
    <div class="card-header">
        <h2>📋 Historique des emprunts (<?= count($emprunts) ?>)</h2>
    </div>
    <?php if (empty($emprunts)): ?>
        <p style="color:var(--muted);text-align:center;padding:1.5rem 0;">Aucun emprunt enregistré.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Livre</th><th>Emprunteur</th>
                    <th>Date emprunt</th><th>Retour prévu</th><th>Retour effectif</th>
                    <th>Statut</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emprunts as $e):
                    $enRetard = !$e['retourne'] && $e['date_retour_prevue'] < $today;
                ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['livre_titre'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($e['emprunteur']) ?></td>
                    <td><?= $e['date_emprunt'] ?></td>
                    <td><?= $e['date_retour_prevue'] ?></td>
                    <td><?= $e['date_retour_effective'] ?? '—' ?></td>
                    <td>
                        <?php if ($e['retourne']): ?>
                            <span class="badge badge-success">✅ Retourné</span>
                        <?php elseif ($enRetard): ?>
                            <span class="badge badge-danger">⚠️ En retard</span>
                        <?php else: ?>
                            <span class="badge badge-info">🔖 En cours</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="actions">
                            <?php if (!$e['retourne']): ?>
                                <a href="emprunts.php?action=retour&id=<?= $e['id'] ?>"
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Marquer comme retourné ?')">↩️ Retour</a>
                            <?php endif; ?>
                            <a href="emprunts.php?action=delete&id=<?= $e['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer cet emprunt ?')">🗑️</a>
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
