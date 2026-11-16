# casPitaine
Site web permettant de consulter et d'ajouter des bateaux **voyageurs** et de **fret** (PHP orienté objet, MVC, DAO).

- Brochure des bateaux voyageurs (photo, dimensions, vitesse, équipements) et liste des bateaux de fret.
- Ajout d'un bateau (réservé à l'administrateur) avec validation serveur et envoi d'image sécurisé (JPEG/PNG/GIF/WebP, 2 Mo max).

## Installation
1. Importer `casPitaine.sql` (script rejouable : crée la base `dbBat`, les tables et un jeu de test).
2. Copier `config.example.php` en `config.local.php` (ignoré par Git : **ne jamais publier ce fichier**) et renseigner la connexion MySQL
   (ou variables d'environnement `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `ADMIN_HASH`).
3. Générer le hash du mot de passe administrateur et le placer dans `admin_hash` :
   `php -r "echo password_hash('votre_mot_de_passe', PASSWORD_DEFAULT);"`.
4. Le dossier `images/` doit être accessible en écriture ; `images/.htaccess` interdit l'exécution de scripts dans ce dossier.

## Navigation
`index.php?page=voyageurs` (défaut) · `fret` · `ajout` (connexion requise) · `connexion` · `deconnexion`.

## Organisation
- `index.php` : point d'entrée (session, CSRF, dispatcher).
- `modele/Connexion.php` : connexion PDO (singleton) et configuration.
- `core/Metier/` : classes métier (`Bateau`, `BateauVoyageur`, `BateauFret`, `Equipement`, `Collection`).
- `core/DAO/` : accès aux données (requêtes préparées, transactions, verrou sur la génération des identifiants).
- `Controleur/` : `controleurPrincipal` (dispatcher), `controleurvoyage`, `controleurfret`, `controleurajout`, `controleurauth`, `securite.php`.
- `vue/` : gabarit commun et pages (`brochure`, `fret`, `ajout`, `connexion`).
- `images/` : photos des bateaux voyageurs.
