<?php
$NOM_CTRL = 'Liste';

$ACTIONS = array(
	'messages' => array('Messages','Liste des mails'),
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

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
