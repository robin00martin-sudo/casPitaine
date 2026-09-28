<?php

define('RACINE', __DIR__);

require_once RACINE . '/Controleur/controleur.php';

// Page demandée dans l'URL : index.php?page=voyageurs, ?page=fret ou ?page=ajout
// Sans paramètre (ou avec une valeur inconnue), on affiche les bateaux voyageurs.
$page = $_GET['page'] ?? 'voyageurs';

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
