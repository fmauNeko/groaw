<?php
$NOM_CTRL = 'Courriels';

$ACTIONS = array(
	'trier'		=> array('Trier','Espace de livraison'),
	'afficher'	=> array('Afficher','Afficher un courriel'),
	'raw'		=> array('Afficher en raw','Afficher un courriel sans transformations'),
	'liste'		=> array('Messages','Liste des mails'),
    'partie'    => array('Partie', 'Télécharger une partie d\'un courriel'),
    'deplacer'	=> array('Déplacer', 'Déplace un courriel'),
	'enterrer'	=> array('Entérrer', 'Déplace la liste de courriels dans les archives'),
	'detruire_courriels'	=> array('Détruire courriels', 'Détruit définitivement tout les courriers de la boite'),
	'archive'	=> array('Archive', 'Espace des archives'),
	'marquer_tout_lu'	=> array('Lus', 'Marquer tout les messages comme lus')
);

$DEFAULT_ACTION = 'liste';

require ('../Inc/haut.php');

// Toutes les fonctions de ce contrôleur ont une boite
CNavigation::gestionNomBoite();

// Début de la liste des fonctions

function trier()
{
	afficher();
}

function afficher()
{
	$numero = CModCourriel::numero();

	// Si il n'y a aucun message
	if ($numero === 0)
	{
		new CMessage("Il n'y avait plus aucun courriel dans la boite.");	
		new CRedirection("Boites.php");
	}

	$mod = new CModCourriel($numero);
	$mod->analyser();

    $vue = new CVueCourriel($mod);
    $vue->afficherOutilsMessage();

	$mod_boite = new CModBoite();
	$mod_boite->listeBoitesNbMessages();
	
	$vue_boite = new CVueBoite($mod_boite);
	$vue_boite->afficherBoitesDeplacement($numero);
    
	$vue->afficherCourriel();
}

function raw()
{
	$numero = CModCourriel::numero();
	echo nl2br(htmlspecialchars(CImap::body($numero)));
}

function deplacer()
{
	if (isset($_REQUEST['destination']))
	{
		$courriel = new CModCourriel(CModCourriel::numero());
		$courriel->deplacer($_REQUEST['destination']);
	}

	$boite = $GLOBALS['boite'];

	if ($boite === 'INBOX')
	{
		new CRedirection('Courriels.php?EX=afficher');
	}
	else
	{
		new CRedirection('Courriels.php?EX=liste&boite='.rawurlencode($boite));
	}
}

function enterrer()
{
	// Récupération du numéro de la page
	$numero_page = isset($_REQUEST['page']) ? abs(intval($_REQUEST['page'])) : 0;

	$mod = new CModCourriel();
	$liste = $mod->recupererListeTriee($numero_page, COURRIELS_PAR_PAGE);

	$boite = $GLOBALS['boite'];

	// Inutile d'archiver ce qui est déjà archivé
	if (strpos($boite, 'INBOX.Archive.') !== 0)
	{
		$destination = 'INBOX.Archive.'.substr($boite, 6);
		$mod->deplacerListe($liste, $destination);
	}
		
	new CRedirection('Courriels.php?EX=liste&boite='.rawurlencode($boite));
}

function detruire_courriels()
{
	$boite = $GLOBALS['boite'];
	$url_boite = rawurlencode($boite);

	if (isset($_REQUEST['confirmation']))
	{
		$mod = new CModBoite();
		$mod->vider();
		new CRedirection('Courriels.php?EX=liste&boite='.$url_boite);
	}
	
	CVueBoite::afficherConfirmationVidageBoite($boite, $url_boite);
}

function marquer_tout_lu()
{

	$mod = new CModBoite();
	$mod->marquerToutLus();

	$mod->listeBoitesNbNonLus();

	$boites = $mod->boites;

	if (count($boites) > 0 && $boites[0]->nb_non_vus > 0)
	{
		$boite = rawurlencode(CVueBoite::simplifierNomBoite($boites[0]->name));
	}
	else
	{
		$boite = rawurlencode($GLOBALS['boite']).
			(isset($_REQUEST['page']) ? '&page='.abs(intval($_REQUEST['page'])) : '');
	}

	new CRedirection('Courriels.php?EX=liste&boite='.$boite);
}

function liste()
{
	switch ($GLOBALS['boite'])
	{
		case 'INBOX':
			CNavigation::nommer("Espace de livraison");
			break;
		case 'INBOX.Interesting':
			CNavigation::nommer("Courriers intéressant");
			break;
		case 'INBOX.Normal':
			CNavigation::nommer("Courriers normaux");
			break;
		case 'INBOX.Unexciting':
			CNavigation::nommer("Courriers inintéressant");
			break;
		case 'INBOX.Trash':
			CNavigation::nommer("Poubelle");
			break;
		default:
			CNavigation::nommer("Boite ".htmlspecialchars($GLOBALS['boite']));
	}

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
}

function partie()
{

	$mod = new CModCourriel(CModCourriel::numero());
	$mod->analyser();

    $structure = $mod->structure;

    $nouvelle_section = null;

	$section =  isset($_REQUEST['section']) ? $_REQUEST['section'] : '1';
    $section = explode('.',$section);

    foreach ($section as $i)
    {
        $n = intval($i);

        if ($nouvelle_section === null)
        {
            $nouvelle_section = "$n";
        }
        else
        {
            $nouvelle_section .= ".$n";
        }

        $n = $n-1;

        if (isset($structure->parts[$n]))
        {
            $structure = $structure->parts[$n];
        }
		else
		{
		}
    }

	$texte = $mod->recupererPartieTexte($nouvelle_section, $structure);

	$nettoyeur = new CNettoyeurHtml($texte, CONTENU_DISTANT);
   
	global $BODY_ONLY;
    $BODY_ONLY = true;
    header('Content-type:   text/html; charset=UTF-8');
	
	$nettoyeur->nettoyerEtAfficher();
	
}

function archive()
{
	$GLOBALS['NOM_BOITE'] = 'archives';
	
	$mod_boite = new CModBoite();
	$mod_boite->listeBoitesNbNonLus();
	
	$vue_boite = new CVueBoite($mod_boite);

	echo <<<EOT
<div class="outils_courriel">
<h3>Sélectionner une boite :</h3>
EOT;
	$vue_boite->afficherArbreBoites();
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
