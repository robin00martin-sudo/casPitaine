<?php
/**
 * Connexion / déconnexion de l'administrateur.
 */
class controleurauth
{
    public static function connexion()
    {
        if (estAdmin()) {
            header('Location: index.php?page=ajout');
            exit;
        }
        $erreur = null;
        // Retour autorisé uniquement vers une page connue de l'application
        $retour = in_array($_GET['retour'] ?? '', ['ajout'], true) ? $_GET['retour'] : 'ajout';

        if (!adminConfigure()) {
            $erreur = "Aucun mot de passe administrateur n'est configuré (clé « admin_hash » de config.local.php).";
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (seConnecter((string) ($_POST['mdp'] ?? ''))) {
                header('Location: index.php?page=' . $retour);
                exit;
            }
            http_response_code(401);
            $erreur = "Mot de passe incorrect.";
        }
        controleurPrincipal::afficherVue('connexion', compact('erreur', 'retour'));
    }

    public static function deconnexion()
    {
        seDeconnecter();
        header('Location: index.php?page=voyageurs');
        exit;
    }
}
