<?php

require_once RACINE . '/modele/Connexion.php';
require_once RACINE . '/core/Metier/BateauVoyageur.php';
require_once RACINE . '/core/DAO/EquipementDAO.php';
require_once RACINE . '/core/DAO/BateauDAO.php';
require_once RACINE . '/core/Metier/Collection.php';
require_once RACINE . '/core/Metier/Equipement.php';

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

        // Une SEULE requête : chaque bateau est répété une fois par équipement
        // (LEFT JOIN : un bateau sans équipement apparaît quand même, une fois).
        $sql = "SELECT b.id, b.nom, b.longueur, b.largeur, b.vitesse, b.image,
                       e.id AS idEquip, e.lib
                FROM BATEAU b
                LEFT JOIN POSSEDER p   ON p.idBat = b.id
                LEFT JOIN EQUIPEMENT e ON e.id = p.idEquip
                WHERE b.type = 'v'
                ORDER BY b.nom, e.lib";
        $jeu = $cnx->query($sql);

        $courant = null;          // bateau en cours de construction
        $equipements = null;
        while ($enreg = $jeu->fetch(PDO::FETCH_ASSOC)) {
            if ($courant === null || $courant['id'] !== $enreg['id']) {
                if ($courant !== null) {
                    $lesBateaux->ajouter(self::construire($courant, $equipements));
                }
                $courant = $enreg;
                $equipements = new Collection();
            }
            if ($enreg['idEquip'] !== null) {
                $equipements->ajouter(new Equipement($enreg['idEquip'], $enreg['lib']));
            }
        }
        if ($courant !== null) {
            $lesBateaux->ajouter(self::construire($courant, $equipements));
        }
        return $lesBateaux;
    }

    private static function construire(array $enreg, Collection $lesEquipements)
    {
        return new BateauVoyageur(
            $enreg['id'], $enreg['nom'], $enreg['longueur'], $enreg['largeur'],
            $enreg['vitesse'], $enreg['image'], $lesEquipements
        );
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
            if ($cnx->inTransaction()) {
                $cnx->rollBack();
            }
            throw $ex;
        } finally {
            BateauDAO::deverrouillerIds($cnx);
        }
    }
}
