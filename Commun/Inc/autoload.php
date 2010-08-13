<?php
// Chargement automatique des class (PHP5)
function __autoload($class)
{
	$chemins_possibles = array(
		'../Mod/'.$class.'.php',
		'../Commun/Mod/'.$class.'.php',
		'../View/'.$class.'.php',
		'../Commun/View/'.$class.'.php',
		'../Class/'.$class.'.php',
		'../Commun/Class/'.$class.'.php'
	);
	
	// Il est important de noter que les dossiers communs sont regardés en deuxième,
	// Ceci permettant de redéfinir une classe facilement sans la changer pour tout les sites
	
	foreach($chemins_possibles as $chemin)
	{
		if (file_exists($chemin))
		{
			require_once($chemin);
			return;
		}
	}
}// __autoload()
?>
