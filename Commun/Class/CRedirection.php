<?php
class CRedirection
{
	public function __construct($url)
	{
		// Pas la peine de garder ce que l'on avait affiché
		ob_end_clean();
		
		// Redirection au niveau du header
		header("Location:\t".$url);
		
		// Lien de redirection, comme le veut la norme
		echo 'Redirection vers: <a href="',htmlspecialchars($url),'"> une autre page</a>.';
		
		// Continuer l'éxécution peut amener des erreurs en fonction du code qui suit
		exit();
	}
}
?>