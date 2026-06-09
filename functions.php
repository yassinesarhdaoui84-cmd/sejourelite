<?php
// ============================================================
//  config/functions.php — Fonctions utilitaires globales
// ============================================================

require_once __DIR__ . '/db.php';

// --- Sécurité -----------------------------------------------
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitize(string $str): string {
    return trim(strip_tags($str));
}

// --- Chambres ------------------------------------------------
function getAllChambres(bool $disponibleOnly = true): array {
    $pdo = getPDO();
    $sql = 'SELECT * FROM chambres' . ($disponibleOnly ? ' WHERE disponible = 1' : '') . ' ORDER BY prix_nuit ASC';
    return $pdo->query($sql)->fetchAll();
}

function getChambreById(int $id): array|false {
    $stmt = getPDO()->prepare('SELECT * FROM chambres WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function searchChambres(string $arrivee, string $depart, int $personnes): array {
    $pdo = getPDO();
    // Exclut les chambres déjà réservées sur ces dates (chevauchement)
    $sql = "SELECT c.* FROM chambres c
            WHERE c.disponible = 1
              AND c.capacite >= :personnes
              AND c.id NOT IN (
                SELECT r.chambre_id FROM reservations r
                WHERE r.statut != 'annulee'
                  AND r.date_arrivee < :depart
                  AND r.date_depart  > :arrivee
              )
            ORDER BY c.prix_nuit ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':personnes' => $personnes,
        ':arrivee'   => $arrivee,
        ':depart'    => $depart,
    ]);
    return $stmt->fetchAll();
}

// --- Réservations --------------------------------------------
function createReservation(array $data): int|false {
    $pdo  = getPDO();
    $stmt = $pdo->prepare("
        INSERT INTO reservations
            (chambre_id, nom, prenom, email, telephone,
             date_arrivee, date_depart, nb_personnes,
             notes, montant_total, paiement)
        VALUES
            (:chambre_id, :nom, :prenom, :email, :telephone,
             :date_arrivee, :date_depart, :nb_personnes,
             :notes, :montant_total, :paiement)
    ");
    $ok = $stmt->execute([
        ':chambre_id'    => (int) $data['chambre_id'],
        ':nom'           => sanitize($data['nom']),
        ':prenom'        => sanitize($data['prenom']),
        ':email'         => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
        ':telephone'     => sanitize($data['telephone'] ?? ''),
        ':date_arrivee'  => $data['date_arrivee'],
        ':date_depart'   => $data['date_depart'],
        ':nb_personnes'  => (int) $data['nb_personnes'],
        ':notes'         => sanitize($data['notes'] ?? ''),
        ':montant_total' => (float) $data['montant_total'],
        ':paiement'      => $data['paiement'] ?? 'carte',
    ]);
    return $ok ? (int) $pdo->lastInsertId() : false;
}

function getAllReservations(): array {
    return getPDO()->query("
        SELECT r.*, c.titre AS chambre_titre, c.prix_nuit
        FROM reservations r
        JOIN chambres c ON c.id = r.chambre_id
        ORDER BY r.created_at DESC
    ")->fetchAll();
}

function updateReservationStatut(int $id, string $statut): bool {
    $stmt = getPDO()->prepare("UPDATE reservations SET statut = ? WHERE id = ?");
    return $stmt->execute([$statut, $id]);
}

// --- Avis ----------------------------------------------------
function getAvisByChambre(int $chambreId): array {
    $stmt = getPDO()->prepare("SELECT * FROM avis WHERE chambre_id = ? AND approuve = 1 ORDER BY created_at DESC");
    $stmt->execute([$chambreId]);
    return $stmt->fetchAll();
}

function getNotemoyenne(int $chambreId): float {
    $stmt = getPDO()->prepare("SELECT AVG(note) as moy FROM avis WHERE chambre_id = ? AND approuve = 1");
    $stmt->execute([$chambreId]);
    return round((float) $stmt->fetchColumn(), 1);
}

function createAvis(array $data): bool {
    $stmt = getPDO()->prepare("
        INSERT INTO avis (chambre_id, auteur, email, note, commentaire)
        VALUES (:chambre_id, :auteur, :email, :note, :commentaire)
    ");
    return $stmt->execute([
        ':chambre_id'  => (int) $data['chambre_id'],
        ':auteur'      => sanitize($data['auteur']),
        ':email'       => filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL),
        ':note'        => max(1, min(5, (int) $data['note'])),
        ':commentaire' => sanitize($data['commentaire'] ?? ''),
    ]);
}

// --- Calcul prix ---------------------------------------------
function calculerMontant(float $prixNuit, string $arrivee, string $depart): float {
    $diff = (new DateTime($depart))->diff(new DateTime($arrivee))->days;
    $nuits = max(1, $diff);
    return round($prixNuit * $nuits + 20, 2); // +20€ frais de service
}

function nbNuits(string $arrivee, string $depart): int {
    return max(1, (new DateTime($depart))->diff(new DateTime($arrivee))->days);
}

// --- Statistiques admin --------------------------------------
function getStats(): array {
    $pdo = getPDO();
    return [
        'total_reservations' => $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn(),
        'reservations_mois'  => $pdo->query("SELECT COUNT(*) FROM reservations WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetchColumn(),
        'revenus_total'      => $pdo->query("SELECT SUM(montant_total) FROM reservations WHERE statut='confirmee'")->fetchColumn() ?? 0,
        'chambres_dispo'     => $pdo->query("SELECT COUNT(*) FROM chambres WHERE disponible=1")->fetchColumn(),
    ];
}
