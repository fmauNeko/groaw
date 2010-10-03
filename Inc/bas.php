<?php
// Sortie dans un buffer
ob_start();
try
{
	$FONCTION_CTRL();
    echo "\n<br/>\n";
	groaw(imap_errors());
}
catch (CException $e)
{
	echo '<div class="exception"><h3>Exception</h3><p>',$e->getMessage(),'</p><a href="javascript:history.back()">Revenir en arrière</a></div>';
}

// Fermeture de la session imap
CImap::deconnexion();

if ($BODY_ONLY)
{
	ob_end_flush();
}
else
{
	$CONTENU_PAGE = ob_get_contents();
	ob_end_clean();
	require('../View/page.php');
}
?>
