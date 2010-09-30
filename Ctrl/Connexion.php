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
			$_SESSION['boite'] = 'INBOX';
			
			CImap::authentification($_POST['mail_groaw'], $_POST['mdp_groaw']);
			
			$_SESSION['email'] = $_POST['mail_groaw'];
			$_SESSION['mdp_secret'] = $_POST['mdp_groaw'];

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
}

// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
