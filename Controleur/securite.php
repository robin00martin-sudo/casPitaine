<?php
require_once RACINE . '/modele/Connexion.php';

/* ===== Protection CSRF : jeton aléatoire en session, renvoyé par chaque formulaire POST ===== */
function csrfToken(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfChamp(): string {
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrfToken()) . '">';
}

function verifierCsrf(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }
    // Un envoi trop volumineux vide $_POST : le contrôleur d'ajout affichera alors l'erreur 413
    if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        return;
    }
    $envoye  = $_POST['csrf'] ?? '';
    $attendu = $_SESSION['csrf'] ?? '';
    if ($attendu === '' || !is_string($envoye) || !hash_equals($attendu, $envoye)) {
        http_response_code(403);
        echo 'Requête refusée : jeton de sécurité invalide ou expiré. <a href="index.php">Retour</a>';
        exit;
    }
}

/* ===== Accès administrateur (ajout de bateaux) ===== */
function adminConfigure(): bool {
    return !empty(Connexion::config()['admin_hash']);
}

function estAdmin(): bool {
    return !empty($_SESSION['admin']);
}

/** Redirige vers la page de connexion si l'administrateur n'est pas connecté. */
function exigerAdmin(string $retour): void {
    if (!estAdmin()) {
        header('Location: index.php?page=connexion&retour=' . urlencode($retour));
        exit;
    }
}

function seConnecter(string $mdp): bool {
    $hash = Connexion::config()['admin_hash'] ?? '';
    if ($hash !== '' && password_verify($mdp, $hash)) {
        session_regenerate_id(true);       // anti fixation de session
        unset($_SESSION['csrf']);
        $_SESSION['admin'] = true;
        return true;
    }
    usleep(500000);                        // ralentit les essais successifs
    return false;
}

function seDeconnecter(): void {
    $_SESSION = [];
    session_destroy();
}
