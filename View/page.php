<html>
<head>
	<title>Page d'exemple</title>
	<link href="../Commun/Css/application.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="../Commun/Css/modeles.css" media="screen" rel="Stylesheet" type="text/css" />
</head>
<body>
<h1><?php echo CNavigation::titre();?></h1>
<?php
echo $CONTENU_PAGE;
?>
<div class="navigation">
<h3>Navigation</h3>
<?php
CNavigation::afficher();
?>
</div>
</body>
</html>