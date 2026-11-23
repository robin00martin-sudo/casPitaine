<?php
require_once RACINE . '/core/DAO/BateauFretDAO.php';

/**
 * Contrôleur des bateaux de fret.
 */
class controleurFret
{
    /**
     * Page de la liste des bateaux de fret.
     */
    public static function lister()
    {
        controleurPrincipal::afficherListe('fret', fn() => BateauFretDAO::chargerLesBateauxFret());
    }
}
