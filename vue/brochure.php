<?php
/**
 * Vue de la liste des bateaux voyageurs.
 *
 * Variables fournies par le contrôleur :
 *   $lesBateaux : Collection de BateauVoyageur (null si erreur)
 *   $erreur     : message d'erreur à afficher, ou null
 */

$nbBateaux = $lesBateaux ? $lesBateaux->cardinal() : 0;

$titre = 'Nos bateaux voyageurs';
$pageActive = 'voyageurs';
$sousTitre = $nbBateaux > 0
    ? $nbBateaux . ' bateau' . ($nbBateaux > 1 ? 'x' : '') . ' pour vos traversées'
    : '';

require RACINE . '/vue/entete.php';
?>

    <?php if ($erreur): ?>

        <p class="message erreur" role="alert"><?= $e($erreur) ?></p>

    <?php elseif ($nbBateaux === 0): ?>

        <p class="message">Aucun bateau voyageur n'est enregistré pour le moment.</p>

    <?php else: ?>

        <div class="liste-voyageurs">
        <?php for ($i = 1; $i <= $nbBateaux; $i++):
            $unBateau = $lesBateaux->obtenirObjet($i);

            // Le chemin stocké en base (ex : /images/bateauvoyageur/luceisle.jpg)
            // est relatif à la racine du projet. On retire le "/" initial pour
            // que l'URL reste valable même si le site est dans un sous-dossier.
            $cheminImage = ltrim((string) $unBateau->getImageBatVoy(), '/');
            $imageExiste = $cheminImage !== '' && file_exists(RACINE . '/' . $cheminImage);

            $lesEquipements = $unBateau->getLesEquipements();
        ?>
            <article class="bateau">
                <figure>
                    <?php if ($imageExiste): ?>
                        <img src="<?= $e($cheminImage) ?>"
                             alt="Photo du bateau <?= $e($unBateau->getNomBat()) ?>"
                             loading="lazy">
                    <?php else: ?>
                        <div class="sans-image">Image indisponible</div>
                    <?php endif; ?>
                </figure>

                <div>
                    <h2><?= $e($unBateau->getNomBat()) ?></h2>

                    <dl>
                        <dt>Longueur</dt>
                        <dd><?= $e($nb($unBateau->getLongueurBat())) ?> mètres</dd>
                        <dt>Largeur</dt>
                        <dd><?= $e($nb($unBateau->getLargeurBat())) ?> mètres</dd>
                        <dt>Vitesse</dt>
                        <dd><?= $e($nb($unBateau->getVitesseBatVoy())) ?> noeuds</dd>
                    </dl>

                    <h3>Équipements du bateau</h3>
                    <?php if ($lesEquipements->cardinal() > 0): ?>
                        <ul class="equipements">
                            <?php for ($j = 1; $j <= $lesEquipements->cardinal(); $j++): ?>
                                <li><?= $e($lesEquipements->obtenirObjet($j)->getLibEquip()) ?></li>
                            <?php endfor; ?>
                        </ul>
                    <?php else: ?>
                        <p>Aucun équipement renseigné.</p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endfor; ?>
        </div>

    <?php endif; ?>

<?php require RACINE . '/vue/pied.php'; ?>
