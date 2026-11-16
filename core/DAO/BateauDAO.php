<?php

require_once RACINE . '/modele/Connexion.php';

/**
 * Opérations communes à tous les types de bateaux (table BATEAU).
 * Utilisé par BateauVoyageurDAO et BateauFretDAO lors d'un ajout.
 */
class BateauDAO
{
    /**
     * Indique si un bateau porte déjà ce nom (sans tenir compte de la casse).
     */
    public static function nomExiste($unNom)
    {
        $cnx = Connexion::getConnexion();

        $req = $cnx->prepare("SELECT COUNT(*) FROM BATEAU WHERE nom = :nom");
        $req->execute([':nom' => $unNom]);

        return (int) $req->fetchColumn() > 0;
    }

    /**
     * Calcule le prochain identifiant libre, sur le modèle des existants :
     * B1, B2, B3... => B4.
     * À appeler à l'intérieur de la transaction qui insère le bateau.
     */
    public static function prochainId(PDO $cnx)
    {
        $sql = "SELECT COALESCE(MAX(CAST(SUBSTRING(id, 2) AS UNSIGNED)), 0) + 1
                FROM BATEAU
                WHERE id REGEXP '^B[0-9]+$'";

        return 'B' . (int) $cnx->query($sql)->fetchColumn();
    }

    /**
     * Verrou nommé MySQL : sérialise « calcul du prochain identifiant + insertion »
     * pour que deux ajouts simultanés n'obtiennent jamais le même identifiant.
     * À libérer avec deverrouillerIds() une fois la transaction terminée.
     */
    public static function verrouillerIds(PDO $cnx)
    {
        $req = $cnx->query("SELECT GET_LOCK('casPitaine_bateau_id', 10)");
        if ((int) $req->fetchColumn() !== 1) {
            throw new RuntimeException("Impossible d'obtenir le verrou de génération d'identifiant.");
        }
    }

    public static function deverrouillerIds(PDO $cnx)
    {
        $cnx->query("SELECT RELEASE_LOCK('casPitaine_bateau_id')")->fetchColumn();
    }
}
