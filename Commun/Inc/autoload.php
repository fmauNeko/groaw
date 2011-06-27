<?php
// Autoloading of class (PHP5)
function __autoload($class)
{
	$possible_paths = array(
		'../Ctrl/'.$class.'.php',
		'../Mod/'.$class.'.php',
		'../View/'.$class.'.php',
		'../Tools/'.$class.'.php'
	);
	
	foreach($possible_paths as $chemin)
	{
		if (file_exists($chemin))
		{
			require_once($chemin);
			return;
		}
	}
}// __autoload()
?>
