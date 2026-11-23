<?php
/**
 * Classe métier abstraite Bateau.
 * Classe mère de BateauVoyageur et BateauFret (cf. Annexe B et C).
 */
abstract class Bateau
{
    // Attributs protégés (accessibles par les classes filles, encapsulés pour le reste)
    protected $idBat;
    protected $nomBat;
    protected $longueurBat;
    protected $largeurBat;

    /**
     * Constructeur de la classe Bateau.
     */
    public function __construct($unId, $unNom, $uneLongueur, $uneLargeur)
    {
        $this->idBat = $unId;
        $this->nomBat = $unNom;
        $this->longueurBat = $uneLongueur;
        $this->largeurBat = $uneLargeur;
    }

    // ----- Accesseurs (getters) -----

    public function getIdBat()
    {
        return $this->idBat;
    }

    public function getNomBat()
    {
        return $this->nomBat;
    }

    public function getLongueurBat()
    {
        return $this->longueurBat;
    }

    public function getLargeurBat()
    {
        return $this->largeurBat;
    }

    /**
     * Retourne sous la forme d'une chaîne de caractères toutes les valeurs
     * des attributs de la classe, précédées de leurs libellés.
     * Exemple :
     *   Nom du bateau : Luce isle
     *   Longueur : 37,2 mètres
     *   Largeur : 8,6 mètres
     */
    public function versChaine()
    {
        $chaine  = "Nom du bateau : " . $this->nomBat . "\n";
        $chaine .= "Longueur : " . $this->formatNombre($this->longueurBat) . " mètres\n";
        $chaine .= "Largeur : " . $this->formatNombre($this->largeurBat) . " mètres\n";

        return $chaine;
    }

    /**
     * Petite fonction utilitaire pour afficher les nombres à la française
     * (virgule à la place du point). Utilisée aussi par les classes filles.
     */
    protected function formatNombre($valeur)
    {
        return str_replace('.', ',', $valeur);
    }
}
