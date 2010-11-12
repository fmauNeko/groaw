<?php
$NOM_CTRL = 'Courriels';

$ACTIONS = array(
	'trier'		=> array('Trier','Espace de livraison'),
	'afficher'	=> array('Afficher','Afficher un courriel'),
	'raw'		=> array('Afficher en raw','Afficher un courriel sans transformations'),
	'liste'		=> array('Messages','Liste des mails'),
    'partie'    => array('Partie', 'Télécharger une partie d\'un courriel'),
    'deplacer'	=> array('Déplacer', 'Déplace un courriel')
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

	$mod = new CModCourriel();
	$mod->recupererCourriels();

	$vue = new CVueCourriel($mod);
    $vue->afficherOutilsListe();

	$mod_boite = new CModBoite();
	$mod_boite->listeBoitesNbNonLus();
	
	$vue_boite = new CVueBoite($mod_boite);
	$vue_boite->afficherArbreBoites();
	
	$vue->afficherCourriels();
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
    
    global $BODY_ONLY;
    $BODY_ONLY = true;
    header('Content-type:   text/html; charset=UTF-8');

    echo $texte;
	
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
