<?php
if (!isset($_REQUEST['PRELOAD_MODE']))
{
	header ('Content-Type:	text/html; charset=utf-8');
}

// Fuseau horaire
date_default_timezone_set(FUSEAU_HORAIRE);

// Démarrage de la session
session_start();

// Si l'on est pas en train de gérer la connexion
if ($NOM_CTRL !== 'Connexion')
{
    // Si les informations sont présentes pour se connecter
    if (isset($_SESSION['connecte']))
    {
		global $boite;

		// Si aucune boite n'est passée dans l'url, c'est INBOX
		$boite = isset($_REQUEST['boite']) ? $_REQUEST['boite'] : 'INBOX';

        // Déclaration de l'identité
        CImap::declarerIdentite($_SESSION['email'], $_SESSION['mdp_secret'], $boite);
    }
    else
    {
        // Si l'on est pas en train de se connecter
        if (!($NOM_CTRL === 'Connexion' && $FONCTION_CTRL === 'connexion'))
        {
            new CRedirection("Connexion.php");
        }
    }
}
?>
