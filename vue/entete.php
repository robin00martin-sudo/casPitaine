<?php
/**
 * Début commun à toutes les pages : <head>, menu de navigation, titre.
 *
 * Variables attendues (définies par la vue qui inclut ce fichier) :
 *   $titre       : titre de la page
 *   $pageActive  : 'voyageurs', 'fret' ou 'ajout' (pour surligner l'entrée du menu)
 *   $sousTitre   : phrase sous le titre (facultatif)
 */

// Échappe le texte venant de la base avant de l'écrire dans le HTML
$e = fn($texte) => htmlspecialchars((string) $texte, ENT_QUOTES, 'UTF-8');

// Nombres à la française (virgule décimale), comme Bateau::formatNombre()
$nb = fn($valeur) => str_replace('.', ',', (string) $valeur);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($titre) ?></title>
    <link rel="stylesheet" href="vue/CSS/style.css">
</head>
<body>
<nav class="menu" aria-label="Types de bateaux">
    <ul>
        <li>
            <a href="index.php?page=voyageurs"
               <?= $pageActive === 'voyageurs' ? 'aria-current="page"' : '' ?>>Bateaux voyageurs</a>
        </li>
        <li>
            <a href="index.php?page=fret"
               <?= $pageActive === 'fret' ? 'aria-current="page"' : '' ?>>Bateaux de fret</a>
        </li>
        <li>
            <a href="index.php?page=ajout"
               <?= $pageActive === 'ajout' ? 'aria-current="page"' : '' ?>>Ajouter un bateau</a>
        </li>
    </ul>
</nav>

<main class="page">
    <header>
        <h1><?= $e($titre) ?></h1>
        <?php if (!empty($sousTitre)): ?>
            <p class="sous-titre"><?= $e($sousTitre) ?></p>
        <?php endif; ?>
    </header>
