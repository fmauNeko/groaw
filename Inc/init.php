<?php
header ('Content-Type: text/html; charset=utf-8');

// Fuseau horaire
date_default_timezone_set(FUSEAU_HORAIRE);

// Ouverture de la connexion à la base de données
$CONNEXION = new PDO(SCHEMA_PDO);

// Nous, on veut des exceptions, car c'est pratique les exceptions
$CONNEXION->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
