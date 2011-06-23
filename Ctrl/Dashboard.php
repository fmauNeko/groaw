<?php
$NOM_CTRL = _('Dashboard');

$ACTIONS = array(
	'boites'		=> array(_('Boites aux lettres'), _('Liste des boites aux lettres')),
	'messages'		=> array(_('Liste des messages'), _('Liste des messages de la boite courante')),
	'message'		=> array(_('Affichage d\'un message'), _(
);

$DEFAULT_ACTION = 'liste';

require ('../Inc/haut.php');

// Début de la liste des fonctions

function liste()
{
	// Récupération du numéro de la page
	$numero_page = isset($_REQUEST['page']) ? abs(intval($_REQUEST['page'])) : 0;

	$mod = new CModCourriel();
	$mod->recupererCourriels($numero_page, COURRIELS_PAR_PAGE);

	$vue = new CVueCourriel($mod);
    $vue->afficherOutilsListe($numero_page);

	$mod_boite = new CModBoite();
	$mod_boite->listeBoitesNbNonLus();

	$vue_boite = new CVueBoite($mod_boite);
	$vue_boite->afficherArbreBoites();
	
	$vue->afficherCourriels($numero_page, COURRIELS_PAR_PAGE);

	CModBoite::nommerBoite($GLOBALS['boite'], false);
}


// Fin de la liste des fonctions

require ('../Inc/bas.php');

?>
