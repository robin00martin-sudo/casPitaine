<?php
/**
 * Vue de la liste des bateaux de fret.
 *
 * Variables fournies par le contrôleur :
 *   $lesBateaux : Collection de BateauFret (null si erreur)
 *   $erreur     : message d'erreur à afficher, ou null
 */

$nbBateaux = $lesBateaux ? $lesBateaux->cardinal() : 0;

$titre = 'Nos bateaux de fret';
$pageActive = 'fret';
$sousTitre = $nbBateaux > 0
    ? $nbBateaux . ' bateau' . ($nbBateaux > 1 ? 'x' : '') . ' pour le transport de marchandises'
    : '';

require RACINE . '/vue/entete.php';
?>

    <?php if ($erreur): ?>

        <p class="message erreur" role="alert"><?= $e($erreur) ?></p>

    <?php elseif ($nbBateaux === 0): ?>

        <p class="message">Aucun bateau de fret n'est enregistré pour le moment.</p>

    <?php else: ?>

        <div class="liste-fret">
        <?php for ($i = 1; $i <= $nbBateaux; $i++):
            $unBateau = $lesBateaux->obtenirObjet($i);
        ?>
            <article class="bateau">
                <h2><?= $e($unBateau->getNomBat()) ?></h2>

                <dl>
                    <dt>Longueur</dt>
                    <dd><?= $e($nb($unBateau->getLongueurBat())) ?> mètres</dd>
                    <dt>Largeur</dt>
                    <dd><?= $e($nb($unBateau->getLargeurBat())) ?> mètres</dd>
                    <dt>Poids maximum</dt>
                    <dd><?= $e($nb($unBateau->getPoidsmaxBatFret())) ?> tonnes</dd>
                </dl>
            </article>
        <?php endfor; ?>
        </div>

    <?php endif; ?>

<?php require RACINE . '/vue/pied.php'; ?>
