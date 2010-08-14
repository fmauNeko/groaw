<?php
$NOM_CTRL = 'Connexion';

$ACTIONS = array(
	'connexion'=> array('Connexion','Connexion de l\'utilisateur'),
	'deconnexion'=> array('Déconnexion','Déconnexion de l\'utilisateur'),
);
$DEFAULT_ACTION = 'connexion';

require ('../Inc/haut.php');
// Début de la liste des fonctions

function connexion()
{
	echo session_id();
	print_r($_SESSION);

	if (CFormulaire::soumis())
	{
		print_r($_POST);	
	}

	$vue = new CVueConnexion(true);
	$vue->afficherFormulaire();
}

function deconnexion()
{
	session_destroy();
}

function supprimer()
{
	$mod = new CModHotels();
		
	if (isset($_POST['CONFIRMER_SUPPRESSION']))
	{
	
		// La suppression se fait à partir de POST, obligatoirement
		if (CFormulaire::clefsPresentes($mod->listeAttributsClefs(), $_POST))
		{
			$mod->supprimer($_POST);
			new CRedirection("?EX=lister");
		}
		else
		{
			throw new CException("Je ne sais pas quoi supprimer");
		}
	}
	else
	{
		if (CFormulaire::clefsPresentes($mod->listeAttributsClefs(), $_REQUEST))
		{
			$mod->select($_REQUEST);
		
			$vue = new CVueHotels($mod);
			$vue->afficherConfirmationSuppression();
			
			CNavigation::nommer("Suppression de l'élément ".$mod->titreValeur());
		}
		else
		{
			throw new CException("Il manque les clefs de l'élément à supprimer");
		}
	}
}

function fiche()
{
	$mod = new CModHotels();
		
	if (CFormulaire::clefsPresentes($mod->listeAttributsClefs()))
	{
		$mod->select($_REQUEST);
		
		CNavigation::nommer("Fiche de l'élément ".$mod->titreValeur());
		
		$vue = new CVueHotels($mod);
		$vue->afficherFiche();
	}
	else
	{
		throw new CException("Les clefs de la fiche ne sont pas présentes");
	}
}

function lister()
{
	$mod = new CModHotels();
	$mod->lister();
	
	$vue = new CVueHotels($mod);
	$vue->afficherTableau();
}

function formulaire()
{
	$mod = new CModHotels();
	$vue = new CVueHotels($mod);
	
	if (CFormulaire::soumis())
	{
		$maj = CFormulaire::miseAJour($_POST, $mod->listeAttributsClefs());
		
		try
		{
			if ($maj)
			{
				$mod->update($_POST);
				
				new CRedirection("?EX=lister");
			}
			else
			{
				$mod->insert($_POST);
			}
		}
		catch (CExceptionFormulaire $e)
		{
			$mod->specifierProblemes($e->recupererProblemes());
			$mod->definirValeurDepuisPOST($_POST);
			
			if ($maj)
			{
				CNavigation::nommer("Modification de l'élément ".$mod->titreValeur($mod->clefs_maj));
			}
			else
			{
				CNavigation::nommer("Ajout d'un nouvel élément");
			}
		}
	}
	elseif (CFormulaire::clefsPresentes($mod->listeAttributsClefs()))
	{
		$mod->select($_REQUEST);
		$mod->construireClefsMaj();
		CNavigation::nommer("Modification de l'élément ".$mod->titreValeur());
	}
	else
	{
		CNavigation::nommer("Ajout d'un nouvel élément");
	}
	
	$vue->afficherFormulaire();
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
