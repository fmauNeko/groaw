<?php
require_once('../Inc/define.php');
require_once('../Inc/config.php');
require_once('../Commun/Inc/autoload.php');
require_once('../Commun/Inc/exceptions.php');
require_once('../Commun/Inc/tools.php');

// HTTPS only if possible
header('Strict-Transport-Security: max-age=500');

if (FORCE_HTTPS)
{
	// Si c'est possible de s'en occuper cotés serveur
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTP'] !== 'on')
	{
		header('Status-Code: 301');
		new CRedirection('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
	};
	
	// In some config (with a reverse proxy for example), PHP can't known if SSL is activated or not. The client side javascript can do it. It's dirty, but i love it.
	CHead::addJS('forcerHttps');
	
}

$EX = isset($_REQUEST['EX']) ? $_REQUEST['EX'] : 'DEFAULT_ACTION';

if (array_key_exists($EX, $ACTIONS))
{
	$CTRL_FUNCTION = $EX;
}
else
{
	$CTRL_FUNCTION = $DEFAULT_ACTION;
}

require_once('../Inc/init.php');

$BODY_ONLY = isset($_REQUEST['AJAX_MODE']);
?>
