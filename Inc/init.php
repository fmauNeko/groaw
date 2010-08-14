<?php
header ('Content-Type: text/html; charset=utf-8');

// Fuseau horaire
date_default_timezone_set(FUSEAU_HORAIRE);

// Démarrage de la session
session_start();

if (isset($_SESSION['email']) && isset($_SESSION['secret_password']))
{
	$JETON_IMAP = imap_open(SERVEUR_IMAP.'INBOX',$_SESSION['email'],$_SESSION['secret_password']);

	if (!$JETON_IMAP)
	{
		new Exception("Le jeton imap est invalide");
	}
	else
	{
		echo "connecté";
	}
}
else
{
	echo "pas de session, pas de chocolat";
}
?>
