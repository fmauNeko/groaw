<?php
class CAttText extends AModType
{
	public static $type = 'text';
	
	public function affichageHtml($valeur)
	{
		return '<blockquote>'.nl2br(htmlspecialchars($valeur)).'</blockquote>';
	}
	
	public function affichageFormulaireHtml($valeur)
	{
		$grand = $this->champ;
		echo "<textarea type=\"text\" id=\"$grand\" name=\"$grand\">";
		
		if ($valeur)
		{
			echo htmlspecialchars($valeur);
		}
		
		echo "</textarea>";
	}
	
	public function affichageListeHtml($valeur)
	{
		if (strlen($valeur) > 50)
		{
			if (preg_match("/(.{1,50})\s/ms", $valeur, $match))
			{
				$valeur = $match[1].'…';
			}
			else
			{
				$valeur = substr($valeur,0,50).'…';
			}
		}
		return htmlspecialchars($valeur);
	}
}
?>