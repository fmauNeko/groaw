<?php

define('NOM_APPLICATION', 'Groaw');

$LISTE_CTRLS = array(
	'Connexion' => 'Connexion',
	'Boites'	=> 'Boites aux lettres',
	'Courriels'		=> 'Gestion des courriels'
);

define('FORMAT_DATE_JOUR',		'à <strong>%Hh%M</strong>');
define('FORMAT_DATE_SEMAINE',	'le <strong>%A à %Hh%M</strong>');
define('FORMAT_DATE_NORMAL',	'le <strong>%A %d %B %Y à %Hh%M</strong>');

define('FUSEAU_HORAIRE', 'Europe/Paris');
setlocale (LC_ALL, 'fr_FR.utf8','fr_FR', 'fr'); 

define('SERVEUR_IMAP','{88.191.117.94:993/imap/ssl/novalidate-cert}');

define('COURRIELS_PAR_PAGE', 40);
define('CONTENU_DISTANT', true);

define('TAILLE_VIGNETTES', 200);

// cache de 5 minutes
define('DUREE_CACHE_LISTE',300);

// Forcer le https si il est disponible ?
define('FORCE_HTTPS', false);

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
