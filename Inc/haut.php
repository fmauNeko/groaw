<?php
require_once('../Inc/define.php');
require_once('../Commun/Inc/autoload.php');
require_once('../Commun/Inc/exceptions.php');
require_once('../Commun/Inc/outils.php');

if (FORCE_HTTPS)
{

	// Si c'est possible de s'en occuper cotés serveur
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTP'] !== 'on')
	{
		new CRedirection('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
	};

	// Sinon, c'est cotés client
	// chez moi, le reverse proxy charge les pages en http, ce qui fait que php
	// n'a aucune info sur la sécurité cotés serveur :D
	CHead::ajouterJs('forcerHttps');
}

$EX = isset($_REQUEST['EX']) ? $_REQUEST['EX'] : '@DEFAULT_ACTION@';

if (array_key_exists($EX, $ACTIONS))
{
	$FONCTION_CTRL = $EX;
}
else
{
	$FONCTION_CTRL = $DEFAULT_ACTION;
}

require_once('../Inc/init.php');

// Le mode Ajax, c'est très bien'
if (isset($_REQUEST['AJAX_MODE']))
{
    $BODY_ONLY = true;
}
else
{
	$BODY_ONLY = false;
}
?>
