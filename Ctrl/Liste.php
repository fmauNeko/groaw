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
	$liste_triee = CImap::sort(SORTDATE, 0);
	$liste_entetes = CImap::fetch_overview("1:12");

	var_dump($liste_entetes);
}

function informations()
{
	var_dump(CImap::mailboxmsginfo());
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
