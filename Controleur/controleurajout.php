<?php
require_once RACINE . '/core/DAO/BateauDAO.php';
require_once RACINE . '/core/DAO/BateauVoyageurDAO.php';
require_once RACINE . '/core/DAO/BateauFretDAO.php';
require_once RACINE . '/core/DAO/EquipementDAO.php';

/**
 * Contrôleur de l'ajout d'un bateau (voyageur ou fret).
 * Gère le formulaire : affichage, validation de la saisie, enregistrement
 * de l'image, puis appel du DAO correspondant au type de bateau.
 */
class controleurAjout
{
    // Limites du formulaire d'ajout
    const NOM_MAX = 50;                 // caractères (colonne BATEAU.nom)
    const IMAGE_MAX = 2097152;          // 2 Mo
    const IMAGES_TYPES = [              // formats d'image acceptés => extension
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
        IMAGETYPE_WEBP => 'webp',
    ];

    /**
     * Page d'ajout d'un bateau.
     *  - GET  : affiche le formulaire vide
     *  - POST : valide la saisie ; si tout est correct, enregistre le bateau
     *           puis redirige vers la liste de son type ; sinon réaffiche le
     *           formulaire avec les erreurs et les valeurs saisies.
     */
    public static function ajouter()
    {
        $saisie = self::saisieVide();
        $erreurs = [];
        $lesEquipements = new Collection();

        try {
            $lesEquipements = EquipementDAO::chargerTousLesEquipements();

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
                // Le corps de la requête dépasse post_max_size : PHP a tout ignoré.
                // Sans ce test, l'utilisateur verrait des erreurs trompeuses
                // ("Le nom est obligatoire") alors qu'il a bien rempli le formulaire.
                http_response_code(413);
                $erreurs['general'] = "Le formulaire est trop volumineux pour le serveur (limite : "
                    . ini_get('post_max_size') . "). Réduisez la taille de l'image.";
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $saisie = self::lireSaisie($_POST);
                $fichier = $_FILES['image'] ?? null;
                $erreurs = self::valider($saisie, $fichier, $lesEquipements);

                if (empty($erreurs)) {
                    $type = self::enregistrer($saisie, $fichier, $lesEquipements);

                    // Redirection après POST : évite le double envoi si on actualise la page
                    $page = ($type === 'v') ? 'voyageurs' : 'fret';
                    header('Location: index.php?page=' . $page . '&ajoute=1');
                    exit;
                }

                http_response_code(422);
            }
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            http_response_code(500);
            $erreurs['general'] = "Le bateau n'a pas pu être enregistré : la base de données est inaccessible.";
        } catch (RuntimeException $ex) {
            error_log($ex->getMessage());
            http_response_code(500);
            $erreurs['image'] = "L'image n'a pas pu être enregistrée sur le serveur.";
        }

        controleur::afficherVue('ajout', compact('saisie', 'erreurs', 'lesEquipements'));
    }

    // ------------------------------------------------------------------
    //  Outils du formulaire d'ajout
    // ------------------------------------------------------------------

    private static function saisieVide()
    {
        return [
            'nom' => '', 'longueur' => '', 'largeur' => '', 'type' => '',
            'vitesse' => '', 'poidsmax' => '', 'equipements' => [],
        ];
    }

    /**
     * Récupère les champs du formulaire dans un tableau propre
     * (tout est converti en texte, pour pouvoir le réafficher tel quel).
     */
    private static function lireSaisie(array $post)
    {
        $texte = fn($cle) => is_scalar($post[$cle] ?? null) ? trim((string) $post[$cle]) : '';

        $ids = is_array($post['equipements'] ?? null) ? $post['equipements'] : [];
        $ids = array_map('strval', array_filter($ids, 'is_scalar'));

        return [
            'nom'         => $texte('nom'),
            'longueur'    => $texte('longueur'),
            'largeur'     => $texte('largeur'),
            'type'        => $texte('type'),
            'vitesse'     => $texte('vitesse'),
            'poidsmax'    => $texte('poidsmax'),
            'equipements' => array_values($ids),
        ];
    }

    /**
     * Convertit un texte saisi ("37,5" ou "37.5") en nombre.
     * Retourne null si le format est invalide ou si la valeur sort de [min ; max].
     * Deux décimales maximum.
     */
    private static function nombre($texte, $min, $max)
    {
        if (!preg_match('/^\d{1,6}([.,]\d{1,2})?$/', $texte)) {
            return null;
        }

        $valeur = (float) str_replace(',', '.', $texte);

        return ($valeur >= $min && $valeur <= $max) ? $valeur : null;
    }

    /**
     * Vérifie la saisie. Retourne un tableau d'erreurs indexé par champ
     * (vide si tout est correct). Les champs propres à l'autre type de bateau
     * sont ignorés.
     */
    private static function valider(array $s, $fichier, Collection $lesEquipements)
    {
        $erreurs = [];

        // --- Champs communs
        if ($s['nom'] === '') {
            $erreurs['nom'] = "Le nom est obligatoire.";
        } elseif (preg_match_all('/./us', $s['nom']) === false) {
            $erreurs['nom'] = "Le nom contient des caractères invalides.";
        } elseif (preg_match_all('/./us', $s['nom']) > self::NOM_MAX) {
            $erreurs['nom'] = "Le nom ne doit pas dépasser " . self::NOM_MAX . " caractères.";
        } elseif (BateauDAO::nomExiste($s['nom'])) {
            $erreurs['nom'] = "Un bateau porte déjà ce nom.";
        }

        if (self::nombre($s['longueur'], 1, 500) === null) {
            $erreurs['longueur'] = "Indiquez une longueur entre 1 et 500 mètres (2 décimales maximum).";
        }

        if (self::nombre($s['largeur'], 1, 100) === null) {
            $erreurs['largeur'] = "Indiquez une largeur entre 1 et 100 mètres (2 décimales maximum).";
        }

        if ($s['type'] !== 'v' && $s['type'] !== 'f') {
            $erreurs['type'] = "Choisissez le type de bateau.";
            return $erreurs;
        }

        // --- Bateau voyageur : vitesse, image, équipements
        if ($s['type'] === 'v') {
            if (self::nombre($s['vitesse'], 1, 100) === null) {
                $erreurs['vitesse'] = "Indiquez une vitesse entre 1 et 100 noeuds (2 décimales maximum).";
            }

            // Notre formulaire contient toujours le champ image : si PHP n'en
            // reçoit aucune trace, c'est que les envois de fichiers sont
            // désactivés (file_uploads = Off). On le signale au lieu d'enregistrer
            // le bateau sans image sans rien dire.
            if ($fichier === null) {
                $erreurImage = ini_get('file_uploads')
                    ? "Le fichier n'a pas été reçu par le serveur, veuillez réessayer."
                    : "Le serveur n'accepte pas l'envoi de fichiers (option PHP file_uploads désactivée).";
            } else {
                $erreurImage = self::validerImage($fichier);
            }
            if ($erreurImage !== null) {
                $erreurs['image'] = $erreurImage;
            }

            // On n'accepte que des identifiants d'équipements qui existent vraiment
            $idsValides = [];
            for ($i = 1; $i <= $lesEquipements->cardinal(); $i++) {
                $idsValides[] = (string) $lesEquipements->obtenirObjet($i)->getIdEquip();
            }
            if (array_diff($s['equipements'], $idsValides)) {
                $erreurs['equipements'] = "Un des équipements choisis n'existe pas.";
            }
        }

        // --- Bateau de fret : poids maximum
        if ($s['type'] === 'f' && self::nombre($s['poidsmax'], 0.1, 500000) === null) {
            $erreurs['poidsmax'] = "Indiquez un poids maximum entre 0,1 et 500 000 tonnes (2 décimales maximum).";
        }

        return $erreurs;
    }

    /**
     * Retourne l'extension ('jpg', 'png'...) si le fichier est réellement
     * une image d'un format accepté, sinon null. On lit le contenu du fichier :
     * le nom donné par l'utilisateur ne prouve rien.
     */
    private static function extensionImage($chemin)
    {
        $info = @getimagesize($chemin);

        return $info ? (self::IMAGES_TYPES[$info[2]] ?? null) : null;
    }

    /**
     * L'image est facultative. Retourne un message d'erreur, ou null si tout va bien.
     */
    private static function validerImage($fichier)
    {
        if ($fichier === null || $fichier['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($fichier['error'] === UPLOAD_ERR_INI_SIZE || $fichier['error'] === UPLOAD_ERR_FORM_SIZE
            || $fichier['size'] > self::IMAGE_MAX) {
            return "L'image est trop volumineuse (2 Mo maximum).";
        }

        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            return "L'envoi de l'image a échoué, veuillez réessayer.";
        }

        if (self::extensionImage($fichier['tmp_name']) === null) {
            return "Format non pris en charge : envoyez une image JPEG, PNG, WebP ou GIF.";
        }

        if (!is_writable(RACINE . '/images')) {
            return "Le dossier images n'est pas accessible en écriture sur le serveur.";
        }

        return null;
    }

    /**
     * Fabrique un nom de fichier à partir du nom du bateau, sur le modèle
     * des images existantes : "Luce isle" => luceisle.jpg
     * Si le fichier existe déjà, on ajoute -2, -3... pour ne rien écraser.
     */
    private static function nomFichierImage($nomBateau, $extension)
    {
        // Accents retirés sans dépendre de l'extension mbstring
        $de   = preg_split('//u', 'àáâäãçéèêëíìîïñóòôöõúùûüýÿ', -1, PREG_SPLIT_NO_EMPTY);
        $vers = str_split('aaaaaceeeeiiiinooooouuuuyy');
        $base = strtolower(strtr($nomBateau, array_combine($de, $vers)));
        $base = preg_replace('/[^a-z0-9]+/', '', $base);
        if ($base === '') {
            $base = 'bateau';
        }

        $nom = $base . '.' . $extension;
        for ($n = 2; file_exists(RACINE . '/images/' . $nom); $n++) {
            $nom = $base . '-' . $n . '.' . $extension;
        }

        return $nom;
    }

    /**
     * Enregistre le bateau (saisie déjà validée) : range l'image dans le
     * dossier images/ puis appelle le DAO. Retourne le type ('v' ou 'f').
     */
    private static function enregistrer(array $s, $fichier, Collection $lesEquipements)
    {
        $longueur = self::nombre($s['longueur'], 1, 500);
        $largeur  = self::nombre($s['largeur'], 1, 100);

        if ($s['type'] === 'f') {
            $poidsMax = self::nombre($s['poidsmax'], 0.1, 500000);
            BateauFretDAO::ajouter(new BateauFret(null, $s['nom'], $longueur, $largeur, $poidsMax));

            return 'f';
        }

        // Équipements cochés : on retrouve les objets Equipement correspondants
        $equipementsChoisis = new Collection();
        for ($i = 1; $i <= $lesEquipements->cardinal(); $i++) {
            $unEquipement = $lesEquipements->obtenirObjet($i);
            if (in_array((string) $unEquipement->getIdEquip(), $s['equipements'], true)) {
                $equipementsChoisis->ajouter($unEquipement);
            }
        }

        // Image (facultative) : déplacée dans images/, chemin mémorisé comme
        // pour les bateaux existants (ex : /images/luceisle.jpg)
        $cheminBase = null;
        $destination = null;
        if ($fichier !== null && $fichier['error'] === UPLOAD_ERR_OK) {
            $extension = self::extensionImage($fichier['tmp_name']);
            $nomFichier = self::nomFichierImage($s['nom'], $extension);
            $destination = RACINE . '/images/' . $nomFichier;

            if (!move_uploaded_file($fichier['tmp_name'], $destination)) {
                throw new RuntimeException("Impossible de déplacer l'image vers " . $destination);
            }
            $cheminBase = '/images/' . $nomFichier;
        }

        try {
            $vitesse = self::nombre($s['vitesse'], 1, 100);
            BateauVoyageurDAO::ajouter(
                new BateauVoyageur(null, $s['nom'], $longueur, $largeur, $vitesse, $cheminBase, $equipementsChoisis)
            );
        } catch (Throwable $ex) {
            // L'enregistrement a échoué : on ne laisse pas d'image orpheline
            if ($destination !== null) {
                @unlink($destination);
            }
            throw $ex;
        }

        return 'v';
    }
}
