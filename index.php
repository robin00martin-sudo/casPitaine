<?php

define('RACINE', __DIR__);

require_once RACINE . '/Controleur/controleurPrincipal.php';

// Sans paramètre (ou avec une valeur inconnue), on affiche les bateaux voyageurs.
controleurPrincipal::dispatcher($_GET['page'] ?? 'voyageurs');

$controleur = new controleur();

switch ($page) {
    case 'fret':
        $controleur->brochureFret();
        break;

    case 'ajout':
        $controleur->ajouterBateau();
        break;

    case 'voyageurs':
    default:
        $controleur->brochureVoyageur();
        break;
}
