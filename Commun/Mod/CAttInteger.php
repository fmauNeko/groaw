<?php
class CAttInteger extends AModType
{
	public static $type = 'integer';
	/*public function htmlAffichageListe($valeur)
	{
		return date(FORMAT_DATE, $valeur);
	}*/
	
	public function pourSql($valeur)
	{
		if ($valeur === '')
			return null;
		return intval($valeur);
	}
}
?>