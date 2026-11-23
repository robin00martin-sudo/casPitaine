<?php

require_once RACINE . '/modele/Connexion.php';
require_once RACINE . '/core/Metier/BateauVoyageur.php';
require_once RACINE . '/core/DAO/EquipementDAO.php';
require_once RACINE . '/core/DAO/BateauDAO.php';
//include RACINE . '/core/Metier/Collection.php';

/**
 * DAO de la classe BateauVoyageur.
 * Equivalent PHP/PDO de la méthode statique
 * Passerelle.chargerLesBateauxVoyageurs() de l'Annexe D.
 */
class BateauVoyageurDAO
{ 
    private PDO $connexion;

    public function __construct() {
        $this->connexion = Connexion::getConnexion();
    }
    /**
     * Instancie et retourne une collection d'objets BateauVoyageur à partir
     * des données lues dans dbBat. Instancie également, pour chaque bateau,
     * sa collection lesEquipements.
     *
     * @return Collection<BateauVoyageur>
     */
    public static function chargerLesBateauxVoyageurs()
    {
        $lesBateaux = new Collection();

        $cnx = Connexion::getConnexion();

        $sql = "SELECT id, nom, longueur, largeur, vitesse, image
                FROM BATEAU
                WHERE type = 'v'
                ORDER BY nom";

        $jeu = $cnx->query($sql);

        while ($enreg = $jeu->fetch(PDO::FETCH_ASSOC)) {
            // Pour chaque bateau, on charge sa collection d'équipements
            $lesEquipements = EquipementDAO::chargerLesEquipements($enreg['id']);

            $unBateauVoyageur = new BateauVoyageur(
                $enreg['id'],
                $enreg['nom'],
                $enreg['longueur'],
                $enreg['largeur'],
                $enreg['vitesse'],
                $enreg['image'],
                $lesEquipements
            );

            $lesBateaux->ajouter($unBateauVoyageur);
        }

        return $lesBateaux;
    }

    /**
     * Enregistre un nouveau bateau voyageur et ses équipements.
     * L'identifiant du BateauVoyageur passé en paramètre est ignoré :
     * un nouvel identifiant est attribué. Les deux insertions (BATEAU puis
     * POSSEDER) sont faites dans une transaction : tout ou rien.
     *
     * @return string l'identifiant attribué au bateau (ex : B4)
     */
    public static function ajouter(BateauVoyageur $unBateau)
    {
        $cnx = Connexion::getConnexion();
        $cnx->beginTransaction();

        try {
            $id = BateauDAO::prochainId($cnx);

            $req = $cnx->prepare("INSERT INTO BATEAU (id, nom, longueur, largeur, vitesse, image, type)
                                  VALUES (:id, :nom, :longueur, :largeur, :vitesse, :image, 'v')");
            $req->execute([
                ':id'       => $id,
                ':nom'      => $unBateau->getNomBat(),
                ':longueur' => $unBateau->getLongueurBat(),
                ':largeur'  => $unBateau->getLargeurBat(),
                ':vitesse'  => $unBateau->getVitesseBatVoy(),
                ':image'    => $unBateau->getImageBatVoy(),
            ]);

            $lien = $cnx->prepare("INSERT INTO POSSEDER (idBat, idEquip) VALUES (:idBat, :idEquip)");
            $lesEquipements = $unBateau->getLesEquipements();

            for ($i = 1; $i <= $lesEquipements->cardinal(); $i++) {
                $lien->execute([
                    ':idBat'   => $id,
                    ':idEquip' => $lesEquipements->obtenirObjet($i)->getIdEquip(),
                ]);
            }

            $cnx->commit();
            return $id;
        } catch (Throwable $ex) {
            $cnx->rollBack();
            throw $ex;
        }
    }
}
