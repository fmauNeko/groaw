<?php
$NOM_CTRL = 'Courriel';

$ACTIONS = array(
	'afficher'		=> array('Afficher','Afficher un courriel'),
);

$DEFAULT_ACTION = 'afficher';

require ('../Inc/haut.php');

// Début de la liste des fonctions

function afficher()
{
	$numero =  isset($_REQUEST['numero']) ? intval($_REQUEST['numero']) : 1;

	echo nl2br(htmlspecialchars(CImap::body($numero)));
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
