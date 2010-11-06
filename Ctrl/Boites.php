<?php
$NOM_CTRL = 'Boites';

$ACTIONS = array(
	'boites'		=> array('Boites aux lettres','Liste des boites aux lettres'),
	'informations'	=> array('Informations','Informations sur le compte'),
	'accueil'		=> array('Accueil','Page d\'accueil du logiciel'),
	'gestion'			=> array('Gestion', 'Gestion des boites aux lettres')
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
	var_dump(CImap::get_quotaroot($GLOBALS['boite']));
	echo "</pre>";
}

function boites()
{
	$mod = new CModBoite();

	if (!$mod->chargerCacheBoites('liste_boites_nb_non_lus'))
	{
		$mod->recupererBoites();
		$mod->recupererNbNonVusBoites();
		$mod->enregistrerCacheBoites('liste_boites_nb_non_lus');
	}

	$vue = new CVueBoite($mod);
	$vue->afficherBoites();
}

function accueil()
{
	$mod = new CModBoite();
	$mod->recupererInfosAcceuil();

	$vue = new CVueBoite($mod);
	$vue->afficherBoitesAcceuil();
}

function gestion()
{
	if (CFormulaire::soumis() && isset($_REQUEST['nom']))
	{
		$boite = new CModBoite('INBOX.'.$_REQUEST['nom']);
		$boite->creer();
		$boite->effacerCaches();
	}

	new CVueHTML('creer_boite');

	$mod = new CModBoite();

	if (!$mod->chargerCacheBoites('liste_boites_nb_messages'))
	{
		$mod->recupererBoites();
		$mod->recupererNbVusBoites();
		$mod->trierBoitesNbVus();
		$mod->enregistrerCacheBoites('liste_boites_nb_messages');
	}

	$vue = new CVueBoite($mod);
	$vue->afficherBoitesSuppression();
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
