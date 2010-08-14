<?php
header ('Content-Type: text/html; charset=utf-8');

// Fuseau horaire
date_default_timezone_set(FUSEAU_HORAIRE);

// Démarrage de la session
session_start();

print_r($_SESSION);
//die("cool");
if (isset($_SESSION['email']) && isset($_SESSION['secret_password']))
{
	$JETON_IMAP = imap_open(SERVEUR_IMAP.'INBOX',$_SESSION['email'],$_SESSION['secret_password'],OP_READONLY|OP_DEBUG,1);

	if (!$JETON_IMAP)
	{
		new Exception("Le jeton imap est invalide");
	}
}
else
{
	// Si l'on est pas en train de se connecter
	if (!($NOM_CTRL === 'Connexion' && $FONCTION_CTRL === 'connexion'))
	{
		new CRedirection("Connexion.php");
	}
}
?>
