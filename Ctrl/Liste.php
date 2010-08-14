<?php
$NOM_CTRL = 'Liste';

$ACTIONS = array(
	'messages' => array('Messages','Liste des mails'),
	'boites' => array('Boites aux lettres','Liste des boites aux lettres'),
	'informations' => array('Informations','Informations sur le compte')
);

$DEFAULT_ACTION = 'messages';

require ('../Inc/haut.php');

// Début de la liste des fonctions

function messages()
{
	$mod = new CModListe();
	$mod->recupererMessages();

	$vue = new CVueListe($mod);
	$vue->afficherMessages();
}

function informations()
{
	var_dump(CImap::mailboxmsginfo());
	var_dump(CImap::num_recent());
}

function boites()
{
	var_dump(CImap::getmailboxes(SERVEUR_IMAP, '*'));
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
