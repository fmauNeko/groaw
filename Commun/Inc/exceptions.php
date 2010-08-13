<?php
// Transformation des messages d'erreurs en exceptions
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

// Définition du gestionnaire d'exceptions de base
function exception_handler($exception)
{
	echo "\"<div class =\"exception\" style=\"width:80%;color:red;border:2px solid orange;white-space:pre;\">\n", htmlspecialchars($exception), "\n<br/><a href=\"javascript:history.back()\">Revenir en arrière</a></div>\"";
}
set_exception_handler("exception_handler");

?>