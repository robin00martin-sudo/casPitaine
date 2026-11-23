<?php
/**
 * Classe métier Equipement (cf. Annexe B et C).
 */
class Equipement
{
    private $idEquip;
    private $libEquip;

    public function __construct($unId, $unLib)
    {
        $this->idEquip = $unId;
        $this->libEquip = $unLib;
    }

    public function getIdEquip()
    {
        return $this->idEquip;
    }

    public function getLibEquip()
    {
        return $this->libEquip;
    }

    /**
     * Retourne sous la forme d'une chaîne la valeur de l'attribut libEquip.
     * L'identifiant de l'équipement n'est pas inséré dans la chaîne.
     */
    public function versChaine()
    {
        return $this->libEquip;
    }
}
