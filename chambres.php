<?php
require_once __DIR__ . '/../config/functions.php';
$pageTitle = 'Chambres & Suites';

$arrivee   = isset($_GET['arrivee'])   ? $_GET['arrivee']   : '';
$depart    = isset($_GET['depart'])    ? $_GET['depart']    : '';
$personnes = isset($_GET['personnes']) ? (int)$_GET['personnes'] : 2;
$recherche = $arrivee && $depart;

if ($recherche) {
    $chambres = searchChambres($arrivee, $depart, $personnes);
} else {
    $chambres = getAllChambres();
}

function renderStars(float $note): string {
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= '<span class="' . ($i <= round($note) ? '' : 'empty') . '">★</span>';
    }
    return $out;
}

include __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="padding: 56px 32px 44px;">
  <span class="hero-eyebrow">Nos hébergements</span>
  <h1>Chambres &amp; Suites</h1>
  <p>Choisissez l'espace qui correspond à votre séjour idéal.</p>
</section>

<div class="search-section">
  <div class="search-card">
    <div class="field">
      <label for="arrival">Arrivée</label>
      <input type="date" id="arrival" name="arrivee" value="<?= e($arrivee) ?>" />
    </div>
    <div class="field">
      <label for="departure">Départ</label>
      <input type="date" id="departure" name="depart" value="<?= e($depart) ?>" />
    </div>
    <div class="field">
      <label for="guests">Personnes</label>
      <select id="guests" name="personnes">
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <option value="<?= $i ?>" <?= $personnes == $i ? 'selected' : '' ?>>
          <?= $i ?><?= $i > 4 ? '+' : '' ?> personne<?= $i > 1 ? 's' : '' ?>
        </option>
        <?php endfor; ?>
      </select>
    </div>
    <button class="btn btn-primary" onclick="handleSearch()">Rechercher</button>
  </div>
</div>

<div class="section">
  <?php if ($recherche): ?>
  <div class="alert alert-success">
    <?= count($chambres) ?> chambre<?= count($chambres) > 1 ? 's' : '' ?> disponible<?= count($chambres) > 1 ? 's' : '' ?>
    du <strong><?= date('d/m/Y', strtotime($arrivee)) ?></strong>
    au <strong><?= date('d/m/Y', strtotime($depart)) ?></strong>
    pour <strong><?= $personnes ?></strong> personne<?= $personnes > 1 ? 's' : '' ?>.
  </div>
  <?php endif; ?>

  <?php if (empty($chambres)): ?>
  <div style="text-align:center; padding: 60px 20px;">
    <p style="font-size:1.1rem; color: var(--text-muted); margin-bottom: 20px;">Aucune chambre disponible pour ces critères.</p>
    <a href="/hotel/pages/chambres.php" class="btn btn-outline">Voir toutes les chambres</a>
  </div>
  <?php else: ?>
  <div class="cards-grid">
    <?php foreach ($chambres as $c): ?>
    <?php
      $note  = getNotemoyenne((int)$c['id']);
      $badges = ['suite' => 'Populaire', 'villa' => 'Exclusif', 'double' => 'Nouveau', 'simple' => 'Économique'];
      $badge  = $badges[$c['type']] ?? 'Disponible';
    ?>
    <div class="card">
      <div class="card-img-wrap">
        <img src="<?= e($c['image']) ?>" alt="<?= e($c['titre']) ?>" loading="lazy" />
        <span class="card-badge"><?= e($badge) ?></span>
      </div>
      <div class="card-body">
        <div class="card-stars"><?= renderStars($note ?: 4.5) ?></div>
        <h3><?= e($c['titre']) ?></h3>
        <p class="card-location">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <?= e($c['localisation']) ?> &mdash; Capacité : <?= (int)$c['capacite'] ?> pers.
        </p>
        <p class="card-desc"><?= e(mb_substr($c['description'] ?? '', 0, 100)) ?>…</p>
        <div class="card-footer">
          <div class="card-price">
            <span class="amount"><?= number_format($c['prix_nuit'], 0, ',', ' ') ?> €</span>
            <span class="per-night">par nuit</span>
          </div>
          <div style="display:flex;gap:8px;">
            <a href="/hotel/pages/chambre.php?id=<?= (int)$c['id'] ?>" class="btn btn-outline btn-sm">Détails</a>
            <button class="btn btn-primary btn-sm"
              onclick="openModal(<?= (int)$c['id'] ?>, '<?= e(addslashes($c['titre'])) ?>', '<?= e(addslashes($c['localisation'])) ?>', <?= (float)$c['prix_nuit'] ?>)">
              Réserver
            </button>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/modal_reservation.php'; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>