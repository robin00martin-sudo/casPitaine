# casPitaine
Site web permetant de créer, lire des bateaux de voyage et de fret.

Le script SQL permet de mettre en place la BDD, le fichier contient aussi un jeu de test.

! IMPORTANT !
  Une fois la BDD initialiser dans "modele/Connexion.php" veuillez renseigner vos info pour vous connecter à la BDD
  (login, mot de passe, nom de la table, IP serveur si vous en avez une sinon localhost)
! FIN IMPORTANT !



index.php -> Point d'entré.

vue -> permet d'avoir un affichage les pages web et où se situe le CSS.

modele/Connexion.php -> permet la connexion à la BDD.

Core/Métier et Core/Dao -> sert à créer les objets en POO et à communiquer avec la BDD pour les données demandées.

controleur -> permet de relier le Core avec les bonnes vues.

images/ -> permet le stockage des photos pour les bateaux de voyages.
