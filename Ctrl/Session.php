<?php

class Session {

	public function default() {
		login();
	}

	public function login() {
	if (CFormulaire::soumis())
	{
		try
		{
			CImap::declarerIdentite($_POST['mail_groaw'], $_POST['mdp_groaw'], 'INBOX');
			CImap::authentification();
			
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

	CHead::ajouterJs('sha1');
	new CVueHTML("connexion");
	}
}
$NOM_CTRL = 'Session';

$ACTIONS = array(
	'login'=> array(_('Login'),'Connexion de l\'utilisateur'),
	'logout'=> array('Déconnexion','Déconnexion de l\'utilisateur')
);

$DEFAULT_ACTION = 'login';

require ('../Inc/top.php');

// Beginning of function list

function login()
{
}

function logout()
{
	session_destroy();

	new CMessage(_('Successful logout'));

	new CRedirection('Session.php');
}

// Fin de la liste des fonctions
require ('../Inc/bottom.php');

?>
