<?php
require_once __DIR__ . '/../config/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$chambre = $id ? getChambreById($id) : null;

if (!$chambre) {
    header('Location: /hotel/pages/chambres.php');
    exit;
}

$avis  = getAvisByChambre($id);
$note  = getNotemoyenne($id);
$pageTitle = $chambre['titre'];

include __DIR__ . '/../includes/header.php';
?>

<div class="chambre-hero">
  <div class="chambre-img">
    <img src="<?= e($chambre['image']) ?>" alt="<?= e($chambre['titre']) ?>" />
  </div>
  <div class="chambre-info">
    <div class="card-stars" style="font-size:1rem;margin-bottom:10px;">
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <span style="color:<?= $i <= round($note ?: 4.5) ? 'var(--gold)' : '#ccc' ?>">★</span>
      <?php endfor; ?>
      <span style="font-size:.85rem;color:var(--text-muted);margin-left:6px;"><?= count($avis) ?> avis</span>
    </div>
    <h1><?= e($chambre['titre']) ?></h1>
    <p class="location" style="display:flex;align-items:center;gap:5px;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
      <?= e($chambre['localisation']) ?>
    </p>
    <div class="price-tag">
      <?= number_format($chambre['prix_nuit'], 0, ',', ' ') ?> € <span>/ nuit</span>
    </div>
    <p><?= e($chambre['description']) ?></p>
    <div class="chambre-features">
      <span class="feature-tag">Capacité : <?= (int)$chambre['capacite'] ?> pers.</span>
      <span class="feature-tag"><?= ucfirst($chambre['type']) ?></span>
      <span class="feature-tag">WiFi inclus</span>
      <span class="feature-tag">Petit-déjeuner</span>
      <span class="feature-tag">Parking gratuit</span>
    </div>
    <button class="btn btn-primary" style="width:100%;"
      onclick="openModal(<?= (int)$chambre['id'] ?>, '<?= e(addslashes($chambre['titre'])) ?>', '<?= e(addslashes($chambre['localisation'])) ?>', <?= (float)$chambre['prix_nuit'] ?>)">
      Réserver cette chambre
    </button>
  </div>
</div>

<?php if (!empty($avis)): ?>
<div class="section" style="padding-top:0;">
  <div class="section-header">
    <span class="section-eyebrow">Avis clients</span>
    <h2>Ce qu'ils en pensent</h2>
  </div>
  <div class="avis-grid">
    <?php foreach ($avis as $av): ?>
    <div class="avis-card">
      <div class="stars"><?= str_repeat('★', (int)$av['note']) . str_repeat('☆', 5 - (int)$av['note']) ?></div>
      <p>"<?= e($av['commentaire']) ?>"</p>
      <div class="author">— <?= e($av['auteur']) ?>, <?= date('M Y', strtotime($av['created_at'])) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/modal_reservation.php'; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
