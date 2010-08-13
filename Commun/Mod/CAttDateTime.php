<?php
class CAttDateTime extends AModType
{
	public static $type = 'datetime';
	
	public function affichageHtml($valeur)
	{
		return strftime(FORMAT_DATE, $valeur);
	}
	
	public function pourSql($valeur)
	{
		# La transformation en date se fait en trois étapes
		# D'abord en utilisant le format de l'application,
		# Puis en utilisant la fonction de base de php
		# Et enfin en utilisant un timestamp
		$tdate = strptime($valeur, FORMAT_DATE);
	
		if ($tdate === false)
		{
			$date = strtotime($valeur);
		
			if ($date === false)
			{
				if (is_numeric($valeur))
				{
					$date = intval($valeur);
				}
				else
				{
					throw new CException("La date n'a pas un format valide");
				}
			}
		}
		else
		{
			$date = mktime($tdate['tm_hour'],$tdate['tm_min'],$tdate['tm_sec'],$tdate['tm_mon']+1,$tdate['tm_mday'],$tdate['tm_year']+1900);
		}

		return $date;
	}
	public function affichageFormulaireHtml($valeur)
	{
		$grand = $this->champ;
		
		# Si il n'y a pas de valeurs, on prends la date de l'instant présent
		if (!$valeur)
		{
			$valeur = time();
		}
		
		# Si il y a une exception dans la création de la date, c'est qu'elle a un format non valide, et donc qu'il faut la modifier
		try
		{
			$date = strftime(FORMAT_DATE,$valeur);
		}
		catch (Exception $e)
		{
			$date = htmlspecialchars($valeur);
		}
		
		echo "<input type=\"text\" id=\"$grand\" name=\"$grand\" value=\"",$date,'" />';
	}
}
?>