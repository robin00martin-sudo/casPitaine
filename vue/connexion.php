<?php
/**
 * Vue de connexion de l'administrateur.
 *   $erreur : message d'erreur, ou null
 *   $retour : page vers laquelle revenir après connexion
 */
$titre = 'Connexion administrateur';
$pageActive = 'ajout';
$sousTitre = "L'ajout de bateaux est réservé à l'administrateur.";
require RACINE . '/vue/entete.php';
?>
    <?php if ($erreur): ?>
        <p class="message erreur" role="alert"><?= $e($erreur) ?></p>
    <?php endif; ?>
    <form class="formulaire" method="post" action="index.php?page=connexion&amp;retour=<?= $e($retour) ?>">
        <?= csrfChamp() ?>
        <fieldset>
            <legend>Authentification</legend>
            <div class="champ">
                <label for="mdp">Mot de passe</label>
                <input type="password" id="mdp" name="mdp" required autocomplete="current-password">
            </div>
        </fieldset>
        <button type="submit">Se connecter</button>
    </form>
<?php require RACINE . '/vue/pied.php'; ?>
