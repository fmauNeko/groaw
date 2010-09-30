<?php
class CVueBoite extends AVueModele
{
	
	public function afficherBoitesAccueil($infos)
	{
	}

	public function afficherBoitesAcceuil()
	{
		echo "<ul class=\"boites\">\n";
		foreach($this->modele->boites as $boite => $infos)
		{
			echo "<li class=\"$boite\">\n\t<h3>$infos->titre</h3>\n\t<a href=\"?EX=ouvrir&boite=",rawurlencode($infos->nom),"\"><p>",
				'Vous avez <strong>', $infos->messages,'</strong> messages à trier :D',
				"</p></a>\n</li>\n";
		}
		echo "<li class=\"archives\">\n\t<h3>Archives</h3>\n\t<a><p>Accédez aux courriels classifiés</p></a>\n</li>\n</ul>";
	}

	public function afficherBoites()
	{
		echo "<ul class=\"boites\">\n";
		foreach($this->modele->boites as $boite)
		{
			$l = explode($boite->delimiter,$this->utf7_to_utf8($boite->name));

			$description = htmlspecialchars(implode(' : ',array_slice($l,1)));
			
			if ($description === '')
			{
				$description = "Groaw";
			}

			$lien = rawurlencode(preg_replace('/^\{.+?\}/','',$boite->name));

			echo "\t<li>\n\t<h3>$description</h3>\n\t<a href=\"Boites.php?EX=ouvrir&amp;boite=$lien\"><p>",
				'Vous avez <strong>', $boite->pasvus,'</strong> messages à lire :D',
				"</p></a>\n</li>\n";
		}
		echo "</ul>";
	}
}
?>
