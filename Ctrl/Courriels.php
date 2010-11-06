<?php
$NOM_CTRL = 'Courriels';

$ACTIONS = array(
	'afficher'	=> array('Afficher','Afficher un courriel'),
	'raw'		=> array('Afficher en raw','Afficher un courriel sans transformations'),
	'liste'		=> array('Messages','Liste des mails'),
    'partie'    => array('Partie', 'Télécharger une partie d\'un courriel'),
    'deplacer'	=> array('Déplacer', 'Déplace un courriel')
);

$DEFAULT_ACTION = 'liste';

require ('../Inc/haut.php');
// Début de la liste des fonctions

function afficher()
{
	$mod = new CModCourriel(CModCourriel::numero());
	$mod->analyser();

    $vue = new CVueCourriel($mod);
    $vue->afficherOutils(null);

	$mod_boite = new CModBoite();

	if (!$mod->chargerCacheBoites('liste_boites_nb_messages'))
	{
		$mod->recupererBoites();
		$mod->recupererNbVusBoites();
		$mod->trierBoitesNbVus();
		$mod->enregistrerCacheBoites('liste_boites_nb_messages');
	}
    $vue->afficherCourriel();
}

function raw()
{
	$numero = $mod::numero();
	echo nl2br(htmlspecialchars(CImap::body($numero)));
}

function deplacer()
{
	if (isset($_REQUEST['destination']))
	{
		$courriel = new CModCourriel(CModCourriel::numero());
		$courriel->deplacer($_REQUEST['destination']);
	}
	
	new CRedirection('Courriels.php?EX=afficher');
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

        if (isset($structure->parts[$n-1]))
        {
            $structure = $structure->parts[$n-1];
        }
    }

	$texte = $mod->recupererPartieTexte($nouvelle_section, $structure);
    
    global $BODY_ONLY;
    $BODY_ONLY = true;
    header('Content-type:   text/html');

    echo $texte;
	
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
