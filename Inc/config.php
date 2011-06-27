<?php

define('IMAP_SERVER','{88.191.117.94:993/imap/ssl/novalidate-cert}');

define('TIME_ZONE', 'Europe/Paris');
setlocale (LC_ALL, 'fr_FR.utf8','fr_FR', 'fr'); 

define('NB_MAILS_BY_PAGE', 40);
define('DISTANT_CONTENT', true);

define('VIGNETTE_SIZE', 200);

// 5 minutes for cache length
define('LIST_CACHE_LENGTH',300);

// Force HTTPS if it's possible ?
define('FORCE_HTTPS', false);

define('DEBUG',true);

// Mails contains alternative contents
// Here's the order in which they are selected
$MIME_ORDER = array(
    'HTML',
    'PLAIN',
    'JPEG',
    'PNG'
);

?>
