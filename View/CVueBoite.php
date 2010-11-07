<?php
class CVueBoite extends AVueModele
{
	
	public function afficherBoites()
	{
		echo "<ul class=\"boites\">\n";
		foreach($this->modele->boites as $boite)
		{
			$nom = $this->simplifierNomBoite($boite->name);

			$l = $this->explodeNomBoite($boite, $nom);

			$description = htmlspecialchars(implode(' : ',array_slice($l,1)));
			
			if ($description === '')
			{
				$description = "Groaw";
			}

			$lien = rawurlencode($nom);

			echo "\t<li class=\"boite_pleine\">\n\t<h3>$description</h3>\n\t<a href=\"Courriels.php?EX=liste&amp;boite=$lien\"><p>",
				'Vous avez <strong>', $boite->nb_non_vus,'</strong> messages à lire :D',
				"</p></a>\n</li>\n";
		}
		echo "</ul>";
	}
	
	public function afficherBoitesAcceuil()
	{
		echo "<ul class=\"boites\">\n";
		foreach($this->modele->boites as $boite => $infos)
		{
			$nb_messages = $infos->messages;

			if ($nb_messages > 0)
			{
				if ($boite === 'livraison')
				{
					$action = 'afficher';
					$verbe = 'trier';
					$phrase_max = 'Ça <strong>déborde</strong> du TGV…';
				}
				else if ($boite === 'poubelle')
				{
					$action = 'liste';
					$verbe = 'supprimer';
					$phrase_max = '<strong>Il est temps</strong> de sortir les poubelles.';
				}
				else
				{
					$action = 'liste';
					$verbe = 'gérer';
					$phrase_max = '<strong>Prenez votre journée</strong> pour gérer tout ça.';
				}
				
				echo "\t<li class=\"boite_pleine $boite\">\n\t\t<h3>$infos->titre</h3>\n\t\t<a href=\"Courriels.php?EX=$action&boite=",
					 rawurlencode($infos->nom),"\"><p>",
					 CPifometrie::nbMailsBoites($infos->messages, $verbe, $phrase_max),
					"</p></a>\n\t</li>\n";
			}
			else
			{
				echo "\t<li class=\"boite_vide $boite\"><h3>$infos->titre</h3><div></div></li>\n";
			}
		}
		echo "\t<li class=\"boite_pleine archives\">\n\t\t<h3>Archives</h3>\n\t\t<a href=\"?EX=boites\"><p>Accédez aux courriels classifiés</p></a>\n\t</li>\n</ul>";
	}

	public function afficherBoitesSuppression()
	{

		echo "<select name=\"supprimer_boites[]\" multiple>\n";
		foreach(array_reverse($this->modele->boites) as $boite)
		{
			$nom = $this->simplifierNomBoite($boite->name);

			$l = $this->explodeNomBoite($boite, $nom);

			$description = htmlspecialchars(implode(' : ',array_slice($l,1)));
			
			$t = array('Trash','Interesting','Normal','Unexciting');
			if (!isset($l[1]) || in_array($l[1], $t))
			{
				continue;
			}

			$lien = rawurlencode($nom);

			echo "\t<option value=\"$lien\">$description (",$boite->nb_messages," messages)</option>\n";
		}
		echo "</select>";
	}

	public function afficherBoitesDeplacement()
	{
		echo "<ul class=\"boites_deplacement\">\n";

		foreach($this->modele->boites as $boite)
		{
			$nom = $this->simplifierNomBoite($boite->name);

			$l = $this->explodeNomBoite($boite, $nom);

			$t = array('RSS', 'Trash','Interesting','Normal','Unexciting');
			if (isset($l[1]) && in_array($l[1], $t))
			{
				continue;
			}

			$description = htmlspecialchars(implode(' : ',array_slice($l,1)));
			
			if ($description === '')
			{
				$description = "Groaw";
			}

			$lien = rawurlencode($nom);

			echo "\t<li><a href=\"Courriels.php?EX=deplacer&amp;destination=$lien\">$description</a></li>\n";
		}
		echo "</ul></div>";
	}

	private function simplifierNomBoite($nom)
	{
		return preg_replace('/^\{.+?\}/','',$nom);
	}

	private function explodeNomBoite($boite, $nom)
	{
		return explode($boite->delimiter,utf7_to_utf8($nom));
	}
}
?>
