<?php

define('NOM_APPLICATION', 'Gestion simplifiée d\'une chaîne hôtelière');

$LISTE_CTRLS = array(
	'Sejours' => 'Séjours',
	'Clients' => 'Clients',
	'Chambres' => 'Chambres',
	'Hotels' => 'Hôtels'
);

define('SCHEMA_PDO', 'sqlite:../bd.sql');
define('FORMAT_DATE', '%d/%m/%Y à %Hh%M');
define('FUSEAU_HORAIRE', 'Europe/Paris');

?>