<?php
class CAttBoolean extends AModType
{
	public function affichageHtml($valeur)
	{
		if ($valeur)
		{
			return 'Oui';
		}
		return 'Non';
	}
}
?>