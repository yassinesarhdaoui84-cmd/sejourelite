<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — SéjourÉlite' : 'SéjourÉlite' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>

<header class="site-header">
  <a href="/hotel/index.php" class="logo">Séjour<span>Élite</span></a>
  <nav>
    <a href="/hotel/index.php">Accueil</a>
    <a href="/hotel/pages/chambres.php">Chambres</a>
    <a href="/hotel/pages/contact.php">Contact</a>
    <a href="/hotel/admin/index.php" class="nav-admin">Admin</a>
  </nav>
  <button class="nav-toggle" aria-label="Menu" onclick="document.querySelector('nav').classList.toggle('open')">
    <span></span><span></span><span></span>
  </button>
</header>