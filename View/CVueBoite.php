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
			echo "<li class=\"$boite\">\n\t<h3>$infos->titre</h3>\n\t<a href=\"?EX=ouvrir&boite=",htmlspecialchars($infos->nom),"\"><p>",
				'Vous avez <strong>', $infos->messages,'</strong> messages à trier :D',
				"</p></a>\n</li>\n";
		}
		echo "<li class=\"archives\">\n\t<h3>Archives</h3>\n\t<a><p>Accédez aux courriels classifiés</p></a>\n</li>\n</ul>";
	}
}
?>
