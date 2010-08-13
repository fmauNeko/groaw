<?php
class CFormulaire
{
	static function soumis()
	{
		if ($_SERVER['REQUEST_METHOD'] === "POST")
		{
			return true;
		}
		return false;
	}
	
	static function clefsPresentes($liste, $environnement=null)
	{
		if ($environnement === null) $environnement = $_REQUEST;
		
		foreach ($liste as $e)
		{
			if (!isset($environnement[$e->champ]))
			{
				return false;
			}
		}
		return true;
	}
	
	// Le but de cette fonction est de détecter si il s'agit d'une mise à jour
	// Une mise à jour est caractérisée par le fait que la liste des anciennes
	// clefs primaires correspond à la liste des nouvelles clefs primaires
	static function miseAJour($retour, $attributs)
	{
		foreach ($attributs as $attribut)
		{
			$nom = $attribut->champ;
			if (!(isset($retour[$nom]) && isset($retour["UPDATE_$nom"])))
			{
				return false;
			}
		}
		return true;
	}
}
?>