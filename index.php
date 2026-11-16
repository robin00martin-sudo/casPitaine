<?php
// Point d'entrée unique : ?page=voyageurs (défaut) | fret | ajout | connexion | deconnexion
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
session_start();

define('RACINE', __DIR__);
require_once RACINE . '/Controleur/securite.php';
require_once RACINE . '/Controleur/controleurPrincipal.php';

verifierCsrf();   // tout formulaire POST doit porter le jeton de la session

controleurPrincipal::dispatcher($_GET['page'] ?? 'voyageurs');
