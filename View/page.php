<!DOCTYPE html>
<html>
<head>
	<title>Page d'exemple</title>
	<link href="../Commun/Css/application.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="../Commun/Css/modeles.css" media="screen" rel="Stylesheet" type="text/css" />
</head>
<body>
<header>
<h1><?php echo CNavigation::titre();?></h1>
</header>
<?php
echo $CONTENU_PAGE;
?>
<nav>
<h3>Navigation</h3>
<?php
CNavigation::afficher();
?>
</nav>
</body>
</html>
