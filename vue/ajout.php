<?php
/**
 * Vue du formulaire d'ajout d'un bateau.
 *
 * Variables fournies par le contrôleur :
 *   $saisie         : valeurs saisies (pour réafficher le formulaire après une erreur)
 *   $erreurs        : messages d'erreur indexés par champ ('nom', 'image', 'general'...)
 *   $lesEquipements : Collection de tous les Equipement disponibles
 */

$titre = 'Ajouter un bateau';
$pageActive = 'ajout';
$sousTitre = 'Renseignez les informations du nouveau bateau.';

require RACINE . '/vue/entete.php';

// Affiche le message d'erreur d'un champ, s'il y en a un
$msg = function ($champ) use ($erreurs, $e) {
    return isset($erreurs[$champ])
        ? '<p class="erreur-champ" id="err-' . $champ . '">' . $e($erreurs[$champ]) . '</p>'
        : '';
};
// Attributs d'accessibilité d'un champ en erreur
$aria = fn($champ) => isset($erreurs[$champ]) ? ' aria-invalid="true" aria-describedby="err-' . $champ . '"' : '';
?>

    <?php if (!empty($erreurs['general'])): ?>
        <p class="message erreur" role="alert"><?= $e($erreurs['general']) ?></p>
    <?php elseif (!empty($erreurs)): ?>
        <p class="message erreur" role="alert">Le formulaire contient des erreurs, merci de les corriger.</p>
    <?php endif; ?>

    <form class="formulaire" method="post" action="index.php?page=ajout" enctype="multipart/form-data" novalidate>

        <fieldset>
            <legend>Informations générales</legend>

            <div class="champ">
                <label for="nom">Nom du bateau</label>
                <input type="text" id="nom" name="nom" maxlength="50" required
                       value="<?= $e($saisie['nom']) ?>"<?= $aria('nom') ?>>
                <?= $msg('nom') ?>
            </div>

            <div class="champ">
                <label for="longueur">Longueur (mètres)</label>
                <input type="text" inputmode="decimal" id="longueur" name="longueur" required
                       value="<?= $e($saisie['longueur']) ?>"<?= $aria('longueur') ?>>
                <?= $msg('longueur') ?>
            </div>

            <div class="champ">
                <label for="largeur">Largeur (mètres)</label>
                <input type="text" inputmode="decimal" id="largeur" name="largeur" required
                       value="<?= $e($saisie['largeur']) ?>"<?= $aria('largeur') ?>>
                <?= $msg('largeur') ?>
            </div>

            <div class="champ">
                <label for="type">Type de bateau</label>
                <select id="type" name="type" required<?= $aria('type') ?>>
                    <option value="">— Choisir —</option>
                    <option value="v"<?= $saisie['type'] === 'v' ? ' selected' : '' ?>>Bateau voyageur</option>
                    <option value="f"<?= $saisie['type'] === 'f' ? ' selected' : '' ?>>Bateau de fret</option>
                </select>
                <?= $msg('type') ?>
            </div>
        </fieldset>

        <!-- Champs propres aux bateaux voyageurs -->
        <fieldset id="bloc-v" data-type="v">
            <legend>Bateau voyageur</legend>

            <div class="champ">
                <label for="vitesse">Vitesse (noeuds)</label>
                <input type="text" inputmode="decimal" id="vitesse" name="vitesse"
                       value="<?= $e($saisie['vitesse']) ?>"<?= $aria('vitesse') ?>>
                <?= $msg('vitesse') ?>
            </div>

            <div class="champ">
                <label for="image">Image <span class="facultatif">(facultatif, 2 Mo maximum)</span></label>
                <input type="file" id="image" name="image"
                       accept="image/jpeg,image/png,image/webp,image/gif"<?= $aria('image') ?>>
                <?= $msg('image') ?>
            </div>

            <div class="champ">
                <span class="etiquette" id="lib-equip">Équipements</span>
                <?= $msg('equipements') ?>
                <?php if ($lesEquipements->cardinal() === 0): ?>
                    <p class="aide">Aucun équipement n'est enregistré dans la base.</p>
                <?php else: ?>
                    <div class="cases" role="group" aria-labelledby="lib-equip">
                        <?php for ($i = 1; $i <= $lesEquipements->cardinal(); $i++):
                            $unEquipement = $lesEquipements->obtenirObjet($i);
                            $idEquip = (string) $unEquipement->getIdEquip();
                        ?>
                            <label>
                                <input type="checkbox" name="equipements[]"
                                       value="<?= $e($idEquip) ?>"
                                       <?= in_array($idEquip, $saisie['equipements'], true) ? 'checked' : '' ?>>
                                <?= $e($unEquipement->getLibEquip()) ?>
                            </label>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </fieldset>

        <!-- Champs propres aux bateaux de fret -->
        <fieldset id="bloc-f" data-type="f">
            <legend>Bateau de fret</legend>

            <div class="champ">
                <label for="poidsmax">Poids maximum (tonnes)</label>
                <input type="text" inputmode="decimal" id="poidsmax" name="poidsmax"
                       value="<?= $e($saisie['poidsmax']) ?>"<?= $aria('poidsmax') ?>>
                <?= $msg('poidsmax') ?>
            </div>
        </fieldset>

        <button type="submit" class="bouton">Ajouter le bateau</button>
    </form>

    <script>
        // Affiche uniquement le bloc correspondant au type choisi.
        // Sans JavaScript, les deux blocs restent visibles : le serveur ignore
        // de toute façon les champs de l'autre type.
        (function () {
            var choix = document.getElementById('type');
            var blocs = document.querySelectorAll('fieldset[data-type]');

            function afficher() {
                blocs.forEach(function (bloc) {
                    bloc.hidden = bloc.getAttribute('data-type') !== choix.value;
                });
            }

            choix.addEventListener('change', afficher);
            afficher();
        })();
    </script>

<?php require RACINE . '/vue/pied.php'; ?>
