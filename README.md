# casPitaine
Site web permettant de **consulter** et d'**ajouter** des bateaux **voyageurs** et de **fret** pour une compagnie maritime.
Il remplace une brochure PDF statique par des pages dynamiques alimentées par une base de données.

Projet en **PHP orienté objet**, architecture **MVC** avec une couche **DAO** : héritage (`Bateau` → `BateauVoyageur` / `BateauFret`), composition (équipements d'un bateau voyageur) et classe de collection.

## Fonctionnalités
- **Bateaux voyageurs** : photo (ou « Image indisponible »), nom, longueur, largeur, vitesse en nœuds et liste des équipements.
- **Bateaux de fret** : nom, longueur, largeur et poids maximum transporté.
- **Ajout d'un bateau** (voyageur ou fret) avec un formulaire qui s'adapte au type choisi :
  - validation côté serveur (nom unique, formats et limites des nombres, équipements existants) ;
  - envoi d'une image facultative (JPEG, PNG, GIF ou WebP, 2 Mo maximum), contrôlée d'après son contenu ;
  - enregistrement en **transaction** (bateau + équipements : tout ou rien) ;
  - redirection vers la liste du type ajouté.
- Affichage à la française (virgule décimale), messages d'erreur clairs (codes HTTP 413, 422 et 500).

## Installation
1. **Créer la base** (le script SQL ne la crée pas) : `CREATE DATABASE dbBat CHARACTER SET utf8mb4;`
2. **Importer** `casPitaine.sql` dans cette base : il crée les tables `BATEAU`, `EQUIPEMENT` et `POSSEDER` et ajoute un jeu de test (3 bateaux voyageurs, 4 équipements, 9 associations).
3. **Renseigner la connexion** dans `modele/Connexion.php` : `$login`, `$mdp`, `$bd` (nom de la base, ici `dbBat`) et `$serveur` (`localhost` ou l'adresse de votre serveur).
4. **Rendre le dossier `images/` accessible en écriture** par le serveur web (les images envoyées y sont rangées).
5. Ouvrir `index.php` dans le navigateur.
