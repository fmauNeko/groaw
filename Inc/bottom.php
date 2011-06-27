<?php
// The output is in a buffer
ob_start();

try
{
	CHead::addCSS('application');
	CHead::addCSS($CTRL_NAME);
	CHead::addJS('application');
	CHead::addJS($CTRL_NAME);
	
	CMessage::showMessages();

	$CTRL_FUNCTION();
}
catch (CException $e)
{
	echo '<div class="exception"><h3>',_('Exception'),'</h3><p>',$e->getMessage(),'</p><a href="javascript:history.back()">',_('Revenir en arrière'),'</a></div>';
}

CImap::logout();

// If just the body is requested, the page is printed
if ($BODY_ONLY)
{
	ob_end_flush();
}
else
{
	echo "\n<br/>\n";
	groaw(imap_errors());

	$PAGE_CONTENT = ob_get_contents();
	ob_end_clean();

	if (isset($_REQUEST['PRELOAD_MODE']))
	{
		header('Content-Type: image/gif');
		echo file_get_contents('../Img/Transparent.gif');
	}
	else
	{
		require('../View/page.php');
	}
}
?>
