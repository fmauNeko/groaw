<?php
$NOM_CTRL = 'Courriels';

$ACTIONS = array(
	'afficher'	=> array('Afficher','Afficher un courriel'),
	'raw'		=> array('Afficher en raw','Afficher un courriel sans transformations'),
	'liste'		=> array('Messages','Liste des mails'),
    'partie'    => array('Partie', 'Télécharger une partie d\'un courriel')
);

$DEFAULT_ACTION = 'liste';

require ('../Inc/haut.php');
// Début de la liste des fonctions

function afficher()
{
	$numero =  isset($_REQUEST['numero']) ? intval($_REQUEST['numero']) : 1;

	$mod = new CModCourriel();
	$mod->analyserCourriel($numero);

    $vue = new CVueCourriel($mod);
    $vue->afficherCourriel();
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

function partie()
{
    $numero =  isset($_REQUEST['numero']) ? intval($_REQUEST['numero']) : 1;
	$section =  isset($_REQUEST['section']) ? $_REQUEST['section'] : '1';

	$mod = new CModCourriel();
	$mod->analyserCourriel($numero);
	$texte = $mod->recupererPartieTexte($section);
    
    global $BODY_ONLY;
    $BODY_ONLY = true;
    header('Content-type:   text/html');

    echo $texte;
	
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
