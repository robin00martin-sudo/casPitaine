<?php

require_once RACINE . '/modele/Connexion.php';
require_once RACINE . '/core/Metier/BateauFret.php';
require_once RACINE . '/core/Metier/Collection.php';
require_once RACINE . '/core/DAO/BateauDAO.php';

/**
 * DAO de la classe BateauFret.
 * Fourni par symétrie avec BateauVoyageurDAO afin de couvrir
 * l'ensemble de la hiérarchie d'héritage de Bateau.
 */
class BateauFretDAO
{
    private PDO $connexion;

    public function __construct() {
        $this->connexion = Connexion::getConnexion();
    }
    
    /**
     * @return Collection<BateauFret>
     */
    public static function chargerLesBateauxFret(): Collection
    {
        $lesBateaux = new Collection();

        $pdo = Connexion::getConnexion();

        $sql = "SELECT id, nom, longueur, largeur, poidsMax
                FROM BATEAU
                WHERE type = 'f'
                ORDER BY nom";

        $req = $pdo->query($sql);

        while ($ligne = $req->fetch()) {
            $bateauFret = new BateauFret(
                $ligne['id'],
                $ligne['nom'],
                (float) $ligne['longueur'],
                (float) $ligne['largeur'],
                (float) $ligne['poidsMax']
            );

            $lesBateaux->ajouter($bateauFret);
        }

        $req->closeCursor();

        return $lesBateaux;
    }

    /**
     * Enregistre un nouveau bateau de fret.
     * L'identifiant du BateauFret passé en paramètre est ignoré :
     * un nouvel identifiant est attribué.
     *
     * @return string l'identifiant attribué au bateau (ex : B4)
     */
    public static function ajouter(BateauFret $unBateau)
    {
        $cnx = Connexion::getConnexion();
        $cnx->beginTransaction();

        try {
            $id = BateauDAO::prochainId($cnx);

            $req = $cnx->prepare("INSERT INTO BATEAU (id, nom, longueur, largeur, poidsMax, type)
                                  VALUES (:id, :nom, :longueur, :largeur, :poidsMax, 'f')");
            $req->execute([
                ':id'       => $id,
                ':nom'      => $unBateau->getNomBat(),
                ':longueur' => $unBateau->getLongueurBat(),
                ':largeur'  => $unBateau->getLargeurBat(),
                ':poidsMax' => $unBateau->getPoidsmaxBatFret(),
            ]);

            $cnx->commit();
            return $id;
        } catch (Throwable $ex) {
            $cnx->rollBack();
            throw $ex;
        }
    }
}
