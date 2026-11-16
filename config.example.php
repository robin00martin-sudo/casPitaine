<?php
// Copiez ce fichier en « config.local.php » et renseignez vos valeurs.
// config.local.php est ignoré par Git : les identifiants ne sont JAMAIS publiés.
return [
    'host'   => 'localhost',
    'dbname' => 'dbBat',
    'user'   => 'votre_identifiant',
    'pass'   => 'votre_mot_de_passe',
    // Hash du mot de passe administrateur (protège l'ajout de bateaux). Pour le générer :
    //   php -r "echo password_hash('votre_mot_de_passe_admin', PASSWORD_DEFAULT);"
    'admin_hash' => '',
];
