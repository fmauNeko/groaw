<?php
class CVueBoite extends AVueModele
{
	
	public function afficherBoitesAccueil($infos)
	{
	}

	public function afficherBoites()
	{
		echo "<ul class=\"boites\">\n";
		foreach($this->modele->boites as $boite => $infos)
		{
			echo "<li class=\"$boite\">\n\t<h3>$boite</h3>\n\t<div><p>",
				'Vous avez ', $infos->messages,' messages à trier :D',
				"</p></div>\n</li>\n";
		}
		echo "</ul>";
	}
}
?>
