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
		$JETON_IMAP = imap_open(SERVEUR_IMAP.'INBOX',$_POST['mail_groaw'],$_POST['mdp_groaw'],OP_READONLY|OP_DEBUG,0);

		//print_r(imap_errors());
		if ($JETON_IMAP)
		{
			$_SESSION['email'] = $_POST['mail_groaw'];
			$_SESSION['secret_password'] = $_POST['mdp_groaw'];
			echo "ok";
		}
		else
		{
			echo "loupé";
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
