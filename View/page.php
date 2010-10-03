<!DOCTYPE html>
<html>
<head>
    <title><?php echo CNavigation::titre();?> - GROAW !</title>
	<link href="../Commun/Css/application.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="../Commun/Css/modeles.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="../Css/<?php echo $NOM_CTRL; ?>.css" media="screen" rel="Stylesheet" type="text/css" />
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
echo "</nav>\n";
if (isset($_SESSION['boite']))
{
	echo "<a href=\"Connexion.php?EX=deconnexion\" id=\"bouton_deconnexion\">Sortir de là</a>\n";
}
?>

</body>
</html>
