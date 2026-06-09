<?php
// ============================================================
//  config/db.php — Connexion PDO à MySQL
//  Adapter les constantes selon votre environnement MAMP
// ============================================================

define('DB_HOST', 'localhost');
define('DB_PORT', '8889');        // Port MySQL de MAMP par défaut
define('DB_NAME', 'sejour_elite');
define('DB_USER', 'root');
define('DB_PASS', 'root');        // Mot de passe MAMP par défaut
define('DB_CHARSET', 'utf8mb4');

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // En production, ne jamais afficher le message brut
            die('<p style="font-family:monospace;color:red;padding:20px">
                 Erreur de connexion à la base de données.<br>
                 Vérifiez config/db.php et que MAMP est démarré.<br>
                 <small>' . htmlspecialchars($e->getMessage()) . '</small>
                 </p>');
        }
    }
    return $pdo;
}
