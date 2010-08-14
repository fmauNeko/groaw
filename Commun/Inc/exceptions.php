<?php
// Transformation des messages d'erreurs en exceptions
$exception_error_handler_semaphore = true;
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
	// L'utilité de ce truc est de contourner un bug de merde de php
	// Quand une fonction créé deux erreurs,
	// la deuxième exception est attaquée avant que la première soit relachée
	global $exception_error_handler_semaphore;
	if ($exception_error_handler_semaphore)
	{
		$exception_error_handler_semaphore = false;
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
}
set_error_handler("exception_error_handler");

// Définition du gestionnaire d'exceptions de base
function exception_handler($exception)
{
	echo "\"<div class =\"exception\" style=\"width:80%;color:red;border:2px solid orange;white-space:pre;\">\n", htmlspecialchars($exception), "\n<br/><a href=\"javascript:history.back()\">Revenir en arrière</a></div>\"";
	exit();
}
set_exception_handler("exception_handler");

?>
