<?php
$NOM_CTRL = 'Courriels';

$ACTIONS = array(
	'afficher'	=> array('Afficher','Afficher un courriel'),
	'raw'		=> array('Afficher en raw','Afficher un courriel sans transformations'),
	'liste'		=> array('Messages','Liste des mails')
);

$DEFAULT_ACTION = 'liste';

require ('../Inc/haut.php');
// Début de la liste des fonctions

function afficher()
{
	$numero =  isset($_REQUEST['numero']) ? intval($_REQUEST['numero']) : 1;

	echo nl2br(htmlspecialchars(CImap::body($numero)));
}

function raw()
{
	$numero =  isset($_REQUEST['numero']) ? intval($_REQUEST['numero']) : 1;

	echo nl2br(htmlspecialchars(CImap::body($numero)));
}

function liste()
{
	$mod = new CModCourriel();
	$mod->recupererCourriels();

	$vue = new CVueCourriel($mod);
	$vue->afficherCourriels();
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
