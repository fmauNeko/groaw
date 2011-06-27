<?php

// The output is in a buffer
ob_start();

require_once('config.php');

require_once('Tools/autoload.php');
require_once('Tools/exceptions.php');
require_once('Tools/debug.php');

if (FORCE_HTTPS)
{
	// If the server side gestion is possible
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTP'] !== 'on')
	{
		CNavigation::redirectToURL('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
	};
	
	// In some config (with a reverse proxy for example), PHP can't known if SSL is activated or not. The client side javascript can do it. It's dirty, but i love it.
	CHead::addJS('forcerHttps');
}

$ROOT_PATH = dirname($_SERVER['SCRIPT_NAME']);

if (URL_REWRITING) {
	CNavigation::urlRewriting();
}

if (!isset($_REQUEST['PRELOAD_MODE']))
{
	header ('Content-Type: text/html; charset=utf-8');

	// HTTPS only if possible
	header('Strict-Transport-Security: max-age=500');
}

date_default_timezone_set(TIME_ZONE);

session_start();

$CTRL_NAME = isset($_REQUEST['CTRL']) ? $_REQUEST['CTRL'] : 'Session';
$ACTION_NAME = isset($_REQUEST['EX']) ? $_REQUEST['EX'] : 'index';

// It's better to remove path special characters
$ctrl_filename = 'Ctrl/'.strtr($CTRL_NAME, '/\\.', '   ').'.php';
if (file_exists($ctrl_filename)) {
	require_once($ctrl_filename);
} else {
	$CTRL_NAME = 'Session';
}

$CTRL = new $CTRL_NAME();
if (!method_exists($CTRL, $ACTION_NAME)) {
	$ACTION_NAME = 'index';
}

// If the user is not at the login page
if ($CTRL_NAME !== 'Session')
{
    // If the user is logged
    if (isset($_SESSION['logged']))
    {
		global $box;

		// The selected box is in the url, and INBOX is the default
		$box = isset($_REQUEST['box']) ? $_REQUEST['box'] : 'INBOX';

        CImap::declareIdentity($_SESSION['email'], $_SESSION['secret_password'], $box);
    }
    else
    {
		CNavigation::redirectToApp(null,null,null);
    }
}

$CTRL->{$ACTION_NAME}();

CImap::logout();

// If just the body is requested, the page is printed
if (isset($_REQUEST['AJAX_MODE']))
{
	ob_end_flush();
}
else
{
	// Call of the function
	CHead::addCSS('application');
	CHead::addCSS($CTRL_NAME);
	CHead::addJS('application');
	CHead::addJS($CTRL_NAME);

	CMessage::showMessages();

	echo "\n<br/>\n";
	$errors = imap_errors();
	if ($errors !== false) {
		groaw($errors);
	}

	$html_title = htmlspecialchars(CNavigation::getTitle());

	$PAGE_CONTENT = ob_get_contents();
	ob_end_clean();

	if (isset($_REQUEST['PRELOAD_MODE']))
	{
		header('Content-Type: image/gif');
		echo file_get_contents('Img/Transparent.gif');
	}
	else
	{
		require('View/template.php');
	}
}
?>
