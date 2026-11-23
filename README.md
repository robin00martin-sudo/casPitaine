# casPitaine
Site web permettant de **consulter** et d'**ajouter** des bateaux **voyageurs** et de **fret** pour une compagnie maritime.
Il remplace une brochure PDF statique par des pages dynamiques alimentées par une base de données.

Projet en **PHP orienté objet**, architecture **MVC** avec une couche **DAO** : héritage (`Bateau` → `BateauVoyageur` / `BateauFret`), composition (équipements d'un bateau voyageur) et classe de collection, d'après les annexes B, C et D du sujet.

## Fonctionnalités
- **Bateaux voyageurs** : photo (ou « Image indisponible »), nom, longueur, largeur, vitesse en nœuds et liste des équipements.
- **Bateaux de fret** : nom, longueur, largeur et poids maximum transporté.
- **Ajout d'un bateau** (voyageur ou fret) avec un formulaire qui s'adapte au type choisi :
  - validation côté serveur (nom unique, formats et limites des nombres, équipements existants) ;
  - envoi d'une image facultative (JPEG, PNG, GIF ou WebP, 2 Mo maximum), contrôlée d'après son contenu ;
  - enregistrement en **transaction** (bateau + équipements : tout ou rien) ;
  - redirection vers la liste du type ajouté.
- Affichage à la française (virgule décimale), messages d'erreur clairs (codes HTTP 413, 422 et 500).

## Prérequis
- PHP 7.4 ou plus, avec l'extension `pdo_mysql` (aucune autre extension n'est nécessaire).
- MySQL ou MariaDB.
- Dans `php.ini` : `file_uploads = On`, `upload_max_filesize` et `post_max_size` d'au moins 2 Mo.

## Installation
1. **Créer la base** (le script SQL ne la crée pas) : `CREATE DATABASE dbBat CHARACTER SET utf8mb4;`
2. **Importer** `casPitaine.sql` dans cette base : il crée les tables `BATEAU`, `EQUIPEMENT` et `POSSEDER` et ajoute un jeu de test (3 bateaux voyageurs, 4 équipements, 9 associations).
   Le script ne précise pas l'encodage : importez-le **en UTF-8** (phpMyAdmin le fait par défaut ; en ligne de commande : `mysql --default-character-set=utf8mb4 dbBat < casPitaine.sql`), sinon les accents (« Accès Handicapé ») sont abîmés.
3. **Renseigner la connexion** dans `modele/Connexion.php` : `$login`, `$mdp`, `$bd` (nom de la base, ici `dbBat`) et `$serveur` (`localhost` ou l'adresse de votre serveur).
   > ⚠ Ne publiez jamais vos vrais identifiants sur GitHub : utilisez des valeurs locales avant de pousser le code.
4. **Rendre le dossier `images/` accessible en écriture** par le serveur web (les images envoyées y sont rangées).
5. Ouvrir `index.php` dans le navigateur (voir « Points à corriger » ci-dessous).

## Pages
| Adresse | Contenu |
|---|---|
| `index.php?page=voyageurs` | Bateaux voyageurs (page par défaut, aussi affichée pour une valeur inconnue) |
| `index.php?page=fret` | Bateaux de fret |
| `index.php?page=ajout` | Formulaire d'ajout d'un bateau |

> L'ajout d'un bateau n'est **pas protégé** : toute personne qui peut ouvrir le site peut en ajouter.

## Organisation
```
casPitaine/
├── index.php                     point d'entrée
├── casPitaine.sql                tables + jeu de test
├── modele/Connexion.php          connexion à la base (PDO, singleton)
├── core/
│   ├── Metier/                   classes métier : Bateau, BateauVoyageur, BateauFret, Equipement, Collection
│   └── DAO/                      accès aux données : BateauDAO, BateauVoyageurDAO, BateauFretDAO, EquipementDAO
├── Controleur/                   controleurPrincipal (aiguillage + affichage) et un contrôleur par page
├── vue/                          en-tête/pied communs, pages (vueVoyage, vueFret, ajout) et CSS (vue/CSS)
└── images/                       photos des bateaux voyageurs
```

## Points à corriger avant de lancer le site
Quatre noms ne sont pas cohérents entre `index.php`, le contrôleur principal et les fichiers : tant qu'ils ne sont pas alignés, la page ne s'affiche pas
(*« Failed opening required … Controleur/controleur.php »*). Correction testée (PHP 8.3, MariaDB) :

1. **`index.php`** : remplacer son contenu par
   ```php
   <?php
   define('RACINE', __DIR__);
   require_once RACINE . '/Controleur/controleurPrincipal.php';
   controleurPrincipal::dispatcher($_GET['page'] ?? 'voyageurs');
   ```
2. **Renommer les contrôleurs en minuscules** (le dispatcher charge ces noms ; indispensable sous Linux) :
   `controleurAjout.php` → `controleurajout.php`, `controleurFret.php` → `controleurfret.php`, `controleurVoyage.php` → `controleurvoyage.php`.
3. **`Controleur/controleurajout.php`** : remplacer `controleur::afficherVue(` par `controleurPrincipal::afficherVue(`.
4. **Renommer les vues** demandées par les contrôleurs : `vue/vueVoyage.php` → `vue/brochure.php` et `vue/vueFret.php` → `vue/fret.php`.

## Autres limites connues
- Le script SQL référence `images/alxi.jpg`, absente du dépôt : « Al xi » s'affiche avec « Image indisponible ».
- Après un ajout, la redirection transmet `ajoute=1` mais aucune page n'affiche encore de message de confirmation.
- Pas de modification ni de suppression de bateau (consultation et ajout seulement).
