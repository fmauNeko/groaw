<!DOCTYPE html>
<html>
<head>
	<title><?php echo CNavigation::titre();?> - GROAW !</title>
	<link href="../Css/application.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="../Commun/Css/modeles.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="../Css/<?php echo $NOM_CTRL; ?>.css" media="screen" rel="Stylesheet" type="text/css" />
	<script type="text/javascript" src="../Js/application.js"></script>
</head>
<body>
<?php

if (isset($_SESSION['connecte']))
{
	if (isset($NOM_BOITE))
	{
		echo '<h2 id="titre" class="',$NOM_BOITE,'"><a href="#">',
			 CNavigation::titre(), "</a></h3>\n";
	}

	echo "<ul id=\"navigation\">\n";
	if ($NOM_CTRL !== 'Boites' || $FONCTION_CTRL !== 'tableau')
	{
		echo "\t<li><a href=\"Boites.php\">Tableau de bord</a></li>\n";
	}
	if ($NOM_CTRL === 'Courriels' && $FONCTION_CTRL === 'afficher')
	{
		$boite = htmlspecialchars($GLOBALS['boite']);
		echo "\t<li><a href=\"Courriels.php?EX=liste&amp;boite=$boite\">Liste des messages</a></li>\n";
	}
	echo "\t<li><a href=\"Connexion.php?EX=deconnexion\">Sortir de là</a></li>\n";
	echo "</ul>\n";
}

echo $CONTENU_PAGE;
?>


</body>
</html>
