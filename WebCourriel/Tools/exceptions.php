<?php
// PHP errors are transformed to exceptions
$exception_error_handler_mutex = true;
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
	// The mutex is used when php create to errors at the same time
	// Exceptions doesn't like that.
	global $exception_error_handler_mutex;
	if ($exception_error_handler_mutex && $errfile !== 'Unknown' && $errline !== 0)
	{
		$exception_error_handler_mutex = false;
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	else
	{
		var_dump($errno);
		var_dump($errstr);
		var_dump($errfile);
		var_dump($errline);
	}
}
set_error_handler("exception_error_handler");

// Exception handler that just show the message and exit
function exception_handler($exception)
{
	echo "\"<div class =\"exception\" style=\"width:80%;color:red;border:2px solid orange;white-space:pre;\">\n", htmlspecialchars($exception), "\n<br/><a href=\"javascript:history.back()\">",_('Return back'),"</a></div>\"";
	showGroaw();
	exit();
}
set_exception_handler("exception_handler");

?>
