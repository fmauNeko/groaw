<?php
class CExceptionFormulaire extends CException
{
	protected $problemes = array();
	
	public function ajouterProbleme($probleme, $champ)
	{
		$this->problemes[$champ] = $probleme;
	}
	
	public function recupererProblemes()
	{
		return $this->problemes;
	}
}
?>