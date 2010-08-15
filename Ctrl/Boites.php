<?php
$NOM_CTRL = 'Boites';

$ACTIONS = array(
	'boites'		=> array('Boites aux lettres','Liste des boites aux lettres'),
	'informations'	=> array('Informations','Informations sur le compte'),
	'ouvrir'		=> array('Ouvrir une boite','Ouvrir une boite aux lettres')
);

$DEFAULT_ACTION = 'boites';

require ('../Inc/haut.php');

// Début de la liste des fonctions

function informations()
{
	var_dump(CImap::mailboxmsginfo());
	var_dump(CImap::num_recent());
}

function boites()
{
	$mod = new CModListe();
	$mod->recupererBoites();

	$vue = new CVueListe($mod);
	$vue->afficherBoites();
}

function ouvrir()
{
	if (isset($_REQUEST['boite']))
	{
		$boite = rawurldecode($_REQUEST['boite']);
		
		if ($boite === '')
		{
			$boite = 'INBOX';
		}
	
		$_SESSION['boite'] = $boite;	

		new CRedirection('Liste.php');
	}
	else
	{
		echo '<h3>Veuillez sélectionner une boite aux lettres</h3>';
		boites();
	}
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
