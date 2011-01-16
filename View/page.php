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
	if (isset($NOM_BOITE))
	{
		echo '<h2 id="titre" class="',$NOM_BOITE,'"><a href="#">',
			 CNavigation::titre(), "</a></h2>\n";
	}

	echo "<ul id=\"navigation\">\n";
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
	echo "</ul>\n";
}

echo $CONTENU_PAGE;
?>


</body>
</html>
