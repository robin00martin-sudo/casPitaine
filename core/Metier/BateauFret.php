<?php
require_once __DIR__ . '/Bateau.php';

/**
 * Classe métier BateauFret, hérite de Bateau (cf. Annexe B).
 * Fournie pour compléter la hiérarchie d'héritage, comme demandé.
 */
class BateauFret extends Bateau
{
    private $poidsmaxBatFret;

    public function __construct($unId, $unNom, $uneLongueur, $uneLargeur, $unPoidsMax)
    {
        parent::__construct($unId, $unNom, $uneLongueur, $uneLargeur);
        $this->poidsmaxBatFret = $unPoidsMax;
    }

    public function getPoidsmaxBatFret()
    {
        return $this->poidsmaxBatFret;
    }

    public function versChaine()
    {
        $chaine = parent::versChaine();
        $chaine .= "Poids maximum transporté : " . $this->formatNombre($this->poidsmaxBatFret) . " tonnes\n";

        return $chaine;
    }
}
