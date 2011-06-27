<?php
if (!isset($_REQUEST['PRELOAD_MODE']))
{
	header ('Content-Type: text/html; charset=utf-8');
}

date_default_timezone_set(TIME_ZONE);

session_start();

// If the user is not at the login page
if (!($CTRL_NAME === 'Session' && $CTRL_FUNCTION === 'login'))
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
		new CRedirection("Session.php");
    }
}
?>
