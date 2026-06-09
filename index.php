<?php
require_once __DIR__ . '/config/functions.php';
$pageTitle = 'Accueil';
$chambres  = getAllChambres();
$featured  = array_slice($chambres, 0, 3);

function renderStars(float $note): string {
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= '<span class="' . ($i <= round($note) ? '' : 'empty') . '">★</span>';
    }
    return $out;
}

include __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
  <span class="hero-eyebrow">Bienvenue à SéjourÉlite</span>
  <h1>Vivez une expérience hôtelière d'exception</h1>
  <p>Découvrez nos hébergements de prestige et réservez en quelques clics.</p>
</section>

<!-- SEARCH BAR -->
<div class="search-section">
  <div class="search-card">
    <div class="field">
      <label for="arrival">Arrivée</label>
      <input type="date" id="arrival" name="arrivee" />
    </div>
    <div class="field">
      <label for="departure">Départ</label>
      <input type="date" id="departure" name="depart" />
    </div>
    <div class="field">
      <label for="guests">Personnes</label>
      <select id="guests" name="personnes">
        <option value="1">1 personne</option>
        <option value="2" selected>2 personnes</option>
        <option value="3">3 personnes</option>
        <option value="4">4 personnes</option>
        <option value="5">5+ personnes</option>
      </select>
    </div>
    <button class="btn btn-primary" onclick="handleSearch()">Rechercher</button>
  </div>
</div>

<!-- FEATURED CARDS -->
<div class="section">
  <div class="section-header">
    <span class="section-eyebrow">Nos hébergements</span>
    <h2>Chambres &amp; Suites</h2>
    <p>Des espaces conçus pour votre confort et votre détente.</p>
  </div>

  <div class="cards-grid">
    <?php foreach ($featured as $c): ?>
    <?php
      $note = getNotemoyenne((int)$c['id']);
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
          <?= e($c['localisation']) ?>
        </p>
        <p class="card-desc"><?= e(mb_substr($c['description'] ?? '', 0, 90)) ?>…</p>
        <div class="card-footer">
          <div class="card-price">
            <span class="amount"><?= number_format($c['prix_nuit'], 0, ',', ' ') ?> €</span>
            <span class="per-night">par nuit</span>
          </div>
          <button class="btn btn-primary btn-sm"
            onclick="openModal(<?= (int)$c['id'] ?>, '<?= e(addslashes($c['titre'])) ?>', '<?= e(addslashes($c['localisation'])) ?>', <?= (float)$c['prix_nuit'] ?>)">
            Réserver
          </button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div style="text-align:center; margin-top:36px;">
    <a href="/hotel/pages/chambres.php" class="btn btn-outline">Voir toutes les chambres</a>
  </div>
</div>

<!-- AVIS -->
<?php $avisListe = array_merge(...array_map(fn($c) => getAvisByChambre((int)$c['id']), $featured)); ?>
<?php if (!empty($avisListe)): ?>
<div class="section" style="background: var(--surface); padding-top: 56px; max-width: 100%; margin: 0;">
  <div style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">
    <div class="section-header">
      <span class="section-eyebrow">Témoignages</span>
      <h2>Ce que disent nos clients</h2>
    </div>
    <div class="avis-grid">
      <?php foreach (array_slice($avisListe, 0, 3) as $av): ?>
      <div class="avis-card">
        <div class="stars"><?= str_repeat('★', (int)$av['note']) . str_repeat('☆', 5 - (int)$av['note']) ?></div>
        <p>"<?= e($av['commentaire']) ?>"</p>
        <div class="author">— <?= e($av['auteur']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- MODAL RÉSERVATION -->
<?php include __DIR__ . '/includes/modal_reservation.php'; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>