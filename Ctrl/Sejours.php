<?php
$NOM_CTRL = 'Sejours';

$ACTIONS = array(
	'supprimer'=> array('Supprimer','Suppression d\'un élément'),
	'fiche'=> array('Fiche','Fiche'),
	'lister'=> array('Lister','Liste des séjours'),
	'formulaire'=> array('Formulaire','Mise à jour des séjours')
);
$DEFAULT_ACTION = 'lister';

require ('../Inc/haut.php');
// Début de la liste des fonctions

function supprimer()
{
	$mod = new CModSejours();
		
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
		
			$vue = new CVueSejours($mod);
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
	$mod = new CModSejours();
		
	if (CFormulaire::clefsPresentes($mod->listeAttributsClefs()))
	{
		$mod->select($_REQUEST);
		
		CNavigation::nommer("Fiche de l'élément ".$mod->titreValeur());
		
		$vue = new CVueSejours($mod);
		$vue->afficherFiche();
	}
	else
	{
		throw new CException("Les clefs de la fiche ne sont pas présentes");
	}
}

function lister()
{
	$mod = new CModSejours();
	$mod->lister();
	
	$vue = new CVueSejours($mod);
	$vue->afficherTableau();
}

function formulaire()
{
	$mod = new CModSejours();
	$vue = new CVueSejours($mod);
	
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