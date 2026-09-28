<?php
/**
 * Contrôleur principal : point d'entrée unique de l'application.
 *
 *  - dispatcher()   : lit la page demandée et passe la main au bon contrôleur
 *  - afficherVue()  : affiche une vue (fonction commune aux autres contrôleurs)
 *  - afficherListe(): charge une liste de bateaux puis affiche sa vue, en
 *                     gérant l'erreur de base de données (commune fret / voyage)
 *
 * Les contrôleurs spécialisés sont chargés seulement quand on en a besoin :
 *   controleurvoyage : liste des bateaux voyageurs
 *   controleurfret   : liste des bateaux de fret
 *   controleurajout  : formulaire d'ajout d'un bateau
 */
class controleurPrincipal
{
    /**
     * Oriente la requête selon le paramètre ?page= de l'URL.
     * Une valeur absente ou inconnue affiche les bateaux voyageurs.
     */
    public static function dispatcher($page)
    {
        switch ($page) {
            case 'fret':
                require_once RACINE . '/Controleur/controleurfret.php';
                controleurfret::lister();
                break;

            case 'ajout':
                require_once RACINE . '/Controleur/controleurajout.php';
                controleurajout::ajouter();
                break;

            case 'voyageurs':
            default:
                require_once RACINE . '/Controleur/controleurvoyage.php';
                controleurvoyage::lister();
                break;
        }
    }

    /**
     * Affiche la vue vue/$vue.php. Les valeurs du tableau $donnees deviennent
     * des variables utilisables dans la vue (['erreur' => 'x'] => $erreur).
     */
    public static function afficherVue($vue, array $donnees = [])
    {
        header('Content-Type: text/html; charset=UTF-8');
        extract($donnees, EXTR_SKIP);
        require RACINE . '/vue/' . $vue . '.php';
    }

    /**
     * Charge les bateaux (via la fonction $chargement) puis affiche la vue.
     * Les listes voyageurs et fret ne diffèrent que par le DAO et la vue,
     * la gestion d'erreur est donc écrite une seule fois ici.
     */
    public static function afficherListe($vue, callable $chargement)
    {
        $lesBateaux = null;
        $erreur = null;
        // Affiché après un ajout réussi (redirection vers ?page=...&ajoute=1)
        $succes = ($_GET['ajoute'] ?? '') === '1';

        try {
            $lesBateaux = $chargement();
        } catch (PDOException $ex) {
            // Le détail technique va dans le journal du serveur, pas dans la page
            // (il pourrait révéler des informations de connexion à la base).
            error_log($ex->getMessage());
            http_response_code(500);
            $erreur = "Les bateaux ne peuvent pas être affichés : la base de données est inaccessible.";
        }

        self::afficherVue($vue, compact('lesBateaux', 'erreur', 'succes'));
    }
}
