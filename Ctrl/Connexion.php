<?php
$NOM_CTRL = 'Connexion';

$ACTIONS = array(
	'connexion'=> array('Connexion','Connexion de l\'utilisateur'),
	'deconnexion'=> array('Déconnexion','Déconnexion de l\'utilisateur')
);

$DEFAULT_ACTION = 'connexion';

require ('../Inc/haut.php');

// Début de la liste des fonctions

function connexion()
{
	if (CFormulaire::soumis())
	{
		try
		{
			CImap::authentification($_POST['mail_groaw'], $_POST['mdp_groaw'], 'INBOX');
			
			$_SESSION['email'] = $_POST['mail_groaw'];
			$_SESSION['mdp_secret'] = $_POST['mdp_groaw'];

			$_SESSION['connecte'] = true;

			new CRedirection('Boites.php');
		}
		catch (ErrorException $e)
		{
			echo "Impossible de se connecter";
		}
	}
	
	new CVueHTML("connexion");
}

function deconnexion()
{
	session_destroy();

	new CRedirection('Connexion.php');
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
