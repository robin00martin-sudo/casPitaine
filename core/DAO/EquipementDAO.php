<?php

require_once RACINE . '/modele/Connexion.php';
require_once RACINE . '/core/Metier/Collection.php';
require_once RACINE . '/core/Metier/Equipement.php';

/**
 * DAO de la classe Equipement.
 * Equivalent PHP/PDO de la méthode statique
 * Passerelle.chargerLesEquipements(unIdBateau) de l'Annexe D.
 */
class EquipementDAO
{
    private PDO $connexion;

    public function __construct() {
        $this->connexion = Connexion::getConnexion();
    }
    /**
     * Retourne la collection des Equipement du bateau dont l'identifiant
     * est passé en paramètre.
     *
     * @return Collection<Equipement>
     */
    public static function chargerLesEquipements($unIdBateau)
    {
        $lesEquipements = new Collection();

        $cnx = Connexion::getConnexion();

        $sql = "SELECT e.id, e.lib
                FROM EQUIPEMENT e
                INNER JOIN POSSEDER p ON p.idEquip = e.id
                WHERE p.idBat = :idBat
                ORDER BY e.lib";

        $jeu = $cnx->prepare($sql);
        $jeu->bindParam(':idBat', $unIdBateau);
        $jeu->execute();

        // On parcourt le jeu d'enregistrements, comme JeuEnregistrement en Annexe D
        while ($enreg = $jeu->fetch(PDO::FETCH_ASSOC)) {
            $unEquipement = new Equipement($enreg['id'], $enreg['lib']);
            $lesEquipements->ajouter($unEquipement);
        }

        return $lesEquipements;
    }

    /**
     * Retourne la collection de tous les équipements existants
     * (utilisée pour proposer les cases à cocher du formulaire d'ajout).
     *
     * @return Collection<Equipement>
     */
    public static function chargerTousLesEquipements()
    {
        $lesEquipements = new Collection();

        $cnx = Connexion::getConnexion();

        $jeu = $cnx->query("SELECT id, lib FROM EQUIPEMENT ORDER BY lib");

        while ($enreg = $jeu->fetch(PDO::FETCH_ASSOC)) {
            $lesEquipements->ajouter(new Equipement($enreg['id'], $enreg['lib']));
        }

        return $lesEquipements;
    }
}
