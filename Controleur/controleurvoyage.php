<?php
require_once RACINE . '/core/DAO/BateauVoyageurDAO.php';

/**
 * Contrôleur des bateaux voyageurs.
 */
class controleurVoyage
{
    /**
     * Page de la liste des bateaux voyageurs (avec leurs équipements).
     */
    public static function lister()
    {
        controleurPrincipal::afficherListe('brochure', fn() => BateauVoyageurDAO::chargerLesBateauxVoyageurs());
    }
}
