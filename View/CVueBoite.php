<?php
class CVueBoite extends AVueModele
{
	public function afficherBoites()
	{
		echo "<ul class=\"boites\">\n";
		foreach($this->traiterNomsBoites() as $boite)
		{
			echo "\t<li class=\"boite_pleine\">\n\t<h3>$boite->description</h3>\n\t<a href=\"Courriels.php?EX=liste&amp;boite=$boite->lien\"><p>",
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
					$action = 'trier';
					$verbe = 'trier';
					$phrase_max = 'Ça <strong>déborde</strong> du TGV…';
				}
				else
				{
					$action = 'liste&boite='.rawurlencode($infos->nom);

					if ($boite === 'poubelle')
					{
						$verbe = 'supprimer';
						$phrase_max = '<strong>Il est temps</strong> de sortir les poubelles.';
					}
					else
					{
						$verbe = 'gérer';
						$phrase_max = '<strong>Prenez votre journée</strong> pour gérer tout ça.';
					}
				}
				
				echo "\t<li class=\"boite_pleine $boite\">\n\t\t<h3>$infos->titre</h3>\n\t\t<a href=\"Courriels.php?EX=$action\"><p>",
					 CPifometrie::nbMailsBoites($infos->messages, $verbe, $phrase_max),
					"</p></a>\n\t</li>\n";
			}
			else
			{
				echo "\t<li class=\"boite_vide $boite\"><h3>$infos->titre</h3><div></div></li>\n";
			}
		}
		echo "\t<li class=\"boite_pleine archives\">\n\t\t<h3>Archives</h3>\n\t\t<a href=\"Courriels.php?EX=archive\"><p>Accédez aux courriels classifiés</p></a>\n\t</li>\n</ul>";
	}

	public function afficherBoitesSuppression()
	{

		echo "<select name=\"supprimer_boites[]\" multiple>\n";
		foreach(array_reverse($this->modele->boites) as $boite)
		{
			$t = array('Trash','Interesting','Normal','Unexciting');
			if (!isset($boite->tableau_boite[1]) || in_array($boite->tableau_boite[1], $t))
			{
				continue;
			}

			echo "\t<option value=\"$boite->lien\">$boite->description (",$boite->nb_messages," messages)</option>\n";
		}
		echo "</select>";
	}

	public function afficherBoitesDeplacement($numero_courriel)
	{
		echo "<h3>Classer dans :</h3><ul class=\"boites_deplacement\">\n";

		foreach($this->modele->boites as $boite)
		{

			$t = array('RSS', 'Trash','Interesting','Normal','Unexciting');
			if (!isset($boite->tableau_boite[1]) || in_array($boite->tableau_boite[1], $t))
			{
				continue;
			}

			echo "\t<li><a href=\"Courriels.php?EX=deplacer&amp;destination=$boite->lien&amp;boite=",
			rawurlencode($GLOBALS['boite']),"&amp;numero=$numero_courriel\">$boite->description</a></li>\n";
		}
		echo "</ul></div>";
	}

	public function afficherArbreBoites()
	{
		$arbre = array();

		// Création de la hiérarchie
		foreach($this->modele->boites as $boite)
		{
			$t = $boite->tableau_boite;

			// Au départ, on se branche sur le tableau de base
			$branche = &$arbre;

			// Pour chaque élément du tableau (sauf le dernier)
			$nb_t = count($t)-1;
			for ($i = 0; $i < $nb_t; ++$i)
			{
				$e = $t[$i];

				// Si il n'y a pas encore de branche à se nom
				if (!isset($branche[$e]))
				{
					$branche[$e] = array();
				}

				// On se branche sur la branche suivante
				$branche = &$branche[$e];
			}

			// Ajout de l'élément sur la dernière branche
			$branche[] = $boite;
		}

		echo "<ul class=\"boites\">\n";
		foreach ($arbre['INBOX'] as $sous_clef => $sous_branche)
		{
			CVueBoite::afficherArbreBoitesRec($sous_clef, $sous_branche);
		}

		echo "</ul></div>";
	}
	
	public static function afficherArbreBoitesRec($clef, $branche)
	{
		if (is_array($branche))
		{
			echo "<li><h4>$clef</h4><ul>\n";
			foreach ($branche as $sous_clef => $sous_branche)
			{
				CVueBoite::afficherArbreBoitesRec($sous_clef, $sous_branche);
			}
			echo "</ul></li>\n";
		}
		else
		{
			$description = htmlspecialchars($branche->tableau_boite[count($branche->tableau_boite)-1]);
			$nb_non_vus = $branche->nb_non_vus;

			echo "\t<li><a href=\"Courriels.php?EX=liste&amp;boite=$branche->lien\">",
				($nb_non_vus > 0) ? '<em>' : '', wordwrap($description, 20, "<br/>", true),
				($nb_non_vus > 0) ? " ($nb_non_vus)</em>" : '', "</a></li>\n";
		}
	}

	public static function afficherConfirmationVidageBoite($boite, $url_boite)
	{
		echo <<<EOT
		<p>Les courriels ne pourront plus jamais être récupérés, êtes-vous certain de votre action ?</p>
		<p><a href="?EX=detruire_courriels&amp;boite=$url_boite&amp;confirmation=ok">T'es qui pour me poser ces questions ? Oui je supprime tout.</a></p>
		<p><a href="?EX=liste&amp;boite=$url_boite">Non, j'ose pas.</a></p>
EOT;
	}
}
?>
