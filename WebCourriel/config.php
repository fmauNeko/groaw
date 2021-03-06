<?php

define('DEFAULT_DOMAIN', 'blogjaune.fr');

//$ACCEPT_ONLY_DOMAINS = array('gmail.com', 'blogjaune.fr');
$ACCEPT_ONLY_DOMAINS = null;

// Validate SSL CERT of imap servers
define('VALIDATE_CERT', false);

define('TIME_ZONE', 'Europe/Paris');
setlocale (LC_ALL, 'fr_FR.utf8','fr_FR', 'fr'); 

define('NB_MAILS_BY_PAGE', 40);
define('DISTANT_CONTENT', true);

define('VIGNETTE_SIZE', 200);

// 5 minutes for cache length
// if you don't want cache, set 0
define('CACHE_LENGTH',1300);

// Force HTTPS if it's possible ?
define('FORCE_HTTPS', false);

// Use of url rewriting
//define('URL_REWRITING', false);
define('URL_REWRITING', 'app');

define('DEBUG',true);

// Mails contains alternative contents
// Here's the order in which they are selected
$MIME_ORDER = array(
    'HTML',
    'PLAIN',
    'JPEG',
    'PNG'
);

// Order of boxes when they have 0 unread mails
$BOXES_ORDER = array(
	'INBOX',
	'INBOX.RSS'
);

?>
