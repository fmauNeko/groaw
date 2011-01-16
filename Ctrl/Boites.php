<?php
$NOM_CTRL = 'Boites';

$ACTIONS = array(
	'boites'		=> array('Boites aux lettres','Liste des boites aux lettres'),
	'informations'	=> array('Informations','Informations sur le compte'),
	'tableau'		=> array('Tableau','Tableau de bord'),
	'gestion'		=> array('Gestion', 'Gestion des boites aux lettres')
);

$DEFAULT_ACTION = 'tableau';

require ('../Inc/haut.php');

// Début de la liste des fonctions

function informations()
{
	echo "<h3>mailboxmsginfo</h3><pre>";
	var_dump(CImap::mailboxmsginfo());
	echo "</pre><h3>num_recent</h3><pre>";
	var_dump(CImap::num_recent());
	echo "</pre><h3>get_quotaroot</h3><pre>";
	var_dump(CImap::get_quotaroot($GLOBALS['boite']));
	echo "</pre>";
}

function boites()
{
	$mod = new CModBoite();
	$mod->listeBoitesNbNonLus();

	$vue = new CVueBoite($mod);
	$vue->afficherBoites();
}

function tableau()
{
	$mod = new CModBoite();
	$mod->recupererInfosAcceuil();

	$vue = new CVueBoite($mod);
	$vue->afficherBoitesAcceuil();
	
	CHead::ajouterJs('tableau');
}

function gestion()
{
	if (CFormulaire::soumis())
	{
		$boite = null;

		// Si c'est une création
		if (isset($_REQUEST['nom_boite']))
		{
			$boite = new CModBoite('INBOX.'.$_REQUEST['nom_boite']);
			$boite->creer();
		}

		// Si c'est une suppression
		if (isset($_REQUEST['supprimer_boites']))
		{
			$boite = new CModBoite();

			foreach ($_REQUEST['supprimer_boites'] as $nom_boite)
			{
				$boite->boite = rawurldecode($nom_boite);
				groaw($nom_boite);
				$boite->supprimer();
			}
		}
			
		$boite->effacerCaches();
	}

	new CVueHTML('creer_boite');

	$mod = new CModBoite();
	$mod->listeBoitesNbMessages();

	$vue = new CVueBoite($mod);
	$vue->afficherBoitesSuppression();

	new CVueHTML('supprimer_boites');
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
