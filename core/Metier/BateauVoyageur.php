<?php
require_once __DIR__ . '/Bateau.php';

/**
 * Classe métier BateauVoyageur, hérite de Bateau (cf. Annexe B et C).
 */
class BateauVoyageur extends Bateau
{
    private $vitesseBatVoy;

    // Chemin d'accès vers le fichier image du bateau
    // Exemple : /images/bateauvoyageur/luceisle.jpg
    private $imageBatVoy;

    // Collection d'objets Equipement
    private $lesEquipements;

    /**
     * Constructeur de la classe BateauVoyageur.
     */
    public function __construct($unId, $unNom, $uneLongueur, $uneLargeur, $uneVitesse, $uneImage, $uneCollEquip)
    {
        // Appel du constructeur de la classe mère Bateau
        parent::__construct($unId, $unNom, $uneLongueur, $uneLargeur);

        $this->vitesseBatVoy = $uneVitesse;
        $this->imageBatVoy = $uneImage;
        $this->lesEquipements = $uneCollEquip;
    }

    public function getVitesseBatVoy()
    {
        return $this->vitesseBatVoy;
    }

    public function getLesEquipements()
    {
        return $this->lesEquipements;
    }

    /**
     * Retourne l'attribut privé imageBatVoy.
     */
    public function getImageBatVoy()
    {
        return $this->imageBatVoy;
    }

    /**
     * Retourne sous la forme d'une chaîne toutes les valeurs des attributs
     * de la classe (sauf imageBatVoy), précédées de leurs libellés, ainsi
     * que la liste des équipements du bateau.
     */
    public function versChaine()
    {
        // On récupère d'abord la chaîne construite par la classe mère
        $chaine = parent::versChaine();

        $chaine .= "Vitesse : " . $this->formatNombre($this->vitesseBatVoy) . " noeuds\n";
        $chaine .= "Liste des équipements du bateau : \n";

        // Parcours de la collection d'équipements
        for ($i = 1; $i <= $this->lesEquipements->cardinal(); $i++) {
            $unEquipement = $this->lesEquipements->obtenirObjet($i);
            $chaine .= "- " . $unEquipement->versChaine() . "\n";
        }

        return $chaine;
    }
}
