<?php
// Sortie dans un buffer
ob_start();
try
{
	CHead::ajouterCss('application');
	CHead::ajouterCss($NOM_CTRL);
	CHead::ajouterJs('application');
	CHead::ajouterJs($NOM_CTRL);
	
	CMessage::afficherCMessages();

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
