<?php
/**
 * Classe technique Collection (cf. Annexe D).
 * Permet de stocker une liste d'objets (bateaux, équipements, ...)
 * et de la parcourir par index, comme décrit dans le sujet.
 */
class Collection
{
    // Tableau PHP utilisé pour stocker les objets de la collection
    private $elements = array();

    /**
     * Ajoute un objet à la collection.
     */
    public function ajouter($unObjet)
    {
        $this->elements[] = $unObjet;
    }

    /**
     * Renvoie le nombre d'objets de la collection.
     */
    public function cardinal()
    {
        return count($this->elements);
    }

    /**
     * Retourne l'objet d'index unIndex.
     * Le premier objet de la collection a pour index 1 (et non 0).
     */
    public function obtenirObjet($unIndex)
    {
        return $this->elements[$unIndex - 1];
    }
}
