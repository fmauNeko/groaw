<?php
require_once('../Inc/define.php');
require_once('../Commun/Inc/autoload.php');
require_once('../Commun/Inc/exceptions.php');
require_once('../Commun/Inc/outils.php');

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
