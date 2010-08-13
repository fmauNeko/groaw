<?php
class CAttString extends AModType
{
	public static $type = 'string';
	
	/*public function htmlAffichageListe($valeur)
	{
		return '«'.htmlspecialchars($valeur).'»';
	}*/
	public function pourSql($valeur)
	{
		if ($valeur === '')
			return null;
			
		return $valeur;
	}
}
?>