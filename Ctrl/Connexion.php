<?php
$NOM_CTRL = 'Connexion';

$ACTIONS = array(
	'connexion'=> array('Connexion','Connexion de l\'utilisateur'),
	'deconnexion'=> array('Déconnexion','Déconnexion de l\'utilisateur'),
	'listeur' => array('list','list2')
);

$DEFAULT_ACTION = 'connexion';

require ('../Inc/haut.php');
// Début de la liste des fonctions

function connexion()
{
	global $JETON_IMAP;

	if (CFormulaire::soumis())
	{
		try
		{

			$JETON_IMAP = imap_open(SERVEUR_IMAP.'INBOX',
							$_POST['mail_groaw']
							,$_POST['mdp_groaw']);
			
			$_SESSION['email'] = $_POST['mail_groaw'];
			$_SESSION['secret_password'] = $_POST['mdp_groaw'];
		}
		catch (ErrorException $e)
		{
			echo "Impossible de se connecter";
		}
	}

	$vue = new CVueConnexion(true);
	$vue->afficherFormulaire();
}

function listeur()
{
	global $JETON_IMAP;
	var_dump(imap_num_msg($JETON_IMAP));
}

function deconnexion()
{
	session_destroy();
}


// Fin de la liste des fonctions
require ('../Inc/bas.php');

?>
