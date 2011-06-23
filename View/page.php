<!DOCTYPE html>
<html>
<head>
	<title><?php echo CNavigation::titre();?> - GROAW !</title>
<?php foreach (CHead::$css as $css)
{
	echo "\t<link href=\"../Css/$css.css\" media=\"screen\" rel=\"Stylesheet\" type=\"text/css\" />\n";
}
foreach (CHead::$js as $js)
{
	echo "\t<script type=\"text/javascript\" src=\"../Js/$js.js\"></script>\n";
}
?>
</head>
<body>
<?php

if (isset($_SESSION['connecte']))
{
	echo "<header>\n\t";
	if (isset($NOM_BOITE))
	{
		echo '<h2 id="titre" class="',$NOM_BOITE,'">',
			 CNavigation::titre(), "</h2>\n";
	}

	echo "\t<nav><ul id=\"navigation\">\n";
	if ($NOM_CTRL === 'Courriels' && $FONCTION_CTRL === 'afficher')
	{
		$boite = rawurlencode($GLOBALS['boite']);
		echo "\t<li><a href=\"Courriels.php?EX=liste&amp;boite=$boite\">Liste des messages</a></li>\n";
	}
	if ($NOM_CTRL !== 'Boites' || $FONCTION_CTRL !== 'tableau')
	{
		echo "\t<li><a href=\"Boites.php\">Tableau de bord</a></li>\n";
	}
	echo "\t<li><a href=\"Connexion.php?EX=deconnexion\">Sortir de là</a></li>\n";
	echo "\t</ul></nav>\n";
	echo "</header>\n";
}
?>
<div id="body">
<?php
echo $CONTENU_PAGE;
?>
</div>
</body>
</html>
