<?php

// Classe abstraite représentant un attribut d'un certain type
abstract class AModType
{
	public static $type = 'inconnu';
	
	public $nom;
	public $champ;
	
	public $clef_primaire;
	public $null_possible;
	
	public function __construct($nom, $champ, $clef_primaire, $null_possible)
	{
		$this->nom = $nom;
		$this->champ = $champ;
		$this->clef_primaire = $clef_primaire;
		$this->null_possible = $null_possible;
	}
	
	public function affichageHtml($valeur)
	{
		return htmlspecialchars($valeur);
	}
	
	public function pourSql($valeur)
	{
		if ($valeur === '')
			return null;
		return $valeur;
	}
	
	public function affichageFormulaireHtml($valeur)
	{
		$grand = $this->champ;
		echo "<input type=\"text\" id=\"$grand\" name=\"$grand";
		
		if ($valeur)
		{
			echo '" value="',htmlspecialchars($valeur);
		}
		
		echo '" />';
	}
	
	public function affichageListeHtml($valeur)
	{
		return $this->affichageHtml($valeur);
	}
}
?>