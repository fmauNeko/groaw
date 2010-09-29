<?php
$NOM_CTRL = 'Boites';

$ACTIONS = array(
	'boites'		=> array('Boites aux lettres','Liste des boites aux lettres'),
	'informations'	=> array('Informations','Informations sur le compte'),
	'ouvrir'		=> array('Ouvrir une boite','Ouvrir une boite aux lettres'),
	'accueil'		=> array('Accueil','Page d\'accueil du logiciel')
);

$DEFAULT_ACTION = 'accueil';

require ('../Inc/haut.php');

// Début de la liste des fonctions

function informations()
{
	echo "<h3>mailboxmsginfo</h3><pre>";
	var_dump(CImap::mailboxmsginfo());
	echo "</pre><h3>num_recent</h3><pre>";
	var_dump(CImap::num_recent());
	echo "</pre><h3>get_quotaroot</h3><pre>";
	var_dump(CImap::get_quotaroot($_SESSION['boite']));
	echo "</pre>";
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

function accueil()
{
	// Livraison
	$mod = new CModBoite();
	
	$infos = $mod->recupererInfosAcceuil();

	var_dump($infos);
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
