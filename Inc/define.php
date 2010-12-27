<?php

define('NOM_APPLICATION', 'Groaw');

$LISTE_CTRLS = array(
	'Connexion' => 'Connexion',
	'Boites'	=> 'Boites aux lettres',
	'Courriels'		=> 'Gestion des courriels'
);

define('FORMAT_DATE_JOUR',		'Aujourd\'hui à %Hh%M');
define('FORMAT_DATE_SEMAINE',	'%A à %Hh%M');
define('FORMAT_DATE_NORMAL',	'%d/%m/%Y à %Hh%M');

define('FUSEAU_HORAIRE', 'Europe/Paris');

define('SERVEUR_IMAP','{88.191.117.94:993/imap/ssl/novalidate-cert}');

define('COURRIELS_PAR_PAGE', 40);
define('CONTENU_DISTANT', true);

// cache de 5 minutes
define('DUREE_CACHE_LISTE',300); 

// Les mails peuvent définir un contenu alternatif
// Voici les types que l'on préfère par ordre de préférence

$PREFERENCES_MIME = array(
    'HTML',     // D'abord le HTML
    'PLAIN',    // Puis on préfère le texte
    'JPEG',     // Puis le jpeg
    'PNG'       // Puis le png
);

define('DEBUG',true);
?>
