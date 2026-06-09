<?php
// pages/reserver.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$chambre_id = isset($_POST['chambre_id']) ? (int)$_POST['chambre_id'] : 0;
$chambre = getChambreById($chambre_id);

if (!$chambre) {
    echo json_encode(['success' => false, 'message' => 'Chambre introuvable ou inexistante.']);
    exit;
}

// Calcul simple du montant total côté serveur pour éviter les fraudes
$arrivee = new DateTime($_POST['date_arrivee']);
$depart = new DateTime($_POST['date_depart']);
$nuits = $depart->diff($arrivee)->days;
if ($nuits <= 0) $nuits = 1;

$frais_service = 20;
$montant_total = ($chambre['prix_nuit'] * $nuits) + $frais_service;

$data = [
    'chambre_id'    => $chambre_id,
    'nom'           => $_POST['nom'] ?? '',
    'prenom'        => $_POST['prenom'] ?? '',
    'email'         => $_POST['email'] ?? '',
    'telephone'     => $_POST['telephone'] ?? '',
    'date_arrivee'  => $_POST['date_arrivee'] ?? '',
    'date_depart'   => $_POST['date_depart'] ?? '',
    'nb_personnes'  => $_POST['nb_personnes'] ?? 1,
    'notes'         => $_POST['notes'] ?? '',
    'montant_total' => $montant_total
];

$reservation_id = createReservation($data);

if ($reservation_id) {
    echo json_encode(['success' => true, 'message' => 'Votre réservation a bien été enregistrée !']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur technique lors de l\'enregistrement de votre séjour.']);
}
exit;
