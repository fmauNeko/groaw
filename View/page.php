<!DOCTYPE html>
<html>
<head>
    <title><?php echo CNavigation::titre();?> - GROAW !</title>
	<link href="../Css/application.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="../Commun/Css/modeles.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="../Css/<?php echo $NOM_CTRL; ?>.css" media="screen" rel="Stylesheet" type="text/css" />
</head>
<body>
<?php
echo $CONTENU_PAGE;
?>

<nav>
<h3>Navigation</h3>
<?php
CNavigation::afficher();
echo "</nav>\n";
if (isset($_SESSION['connecte']))
{
	echo "<a href=\"Connexion.php?EX=deconnexion\" id=\"bouton_deconnexion\">Sortir de là</a>\n";
}
?>

</body>
</html>
