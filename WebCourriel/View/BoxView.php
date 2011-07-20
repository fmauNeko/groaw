<?php
class BoxView extends AbstractView {

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

	public function showBoxesTree()
	{
		$tree = array();

		// Création de la hiérarchie
		foreach($this->model->boxes as $box)
		{
			$t = $box->box_array;

			// Au départ, on se branche sur le tableau de base
			$branch = &$tree;

			// Pour chaque élément du tableau (sauf le dernier)
			$nb_t = count($t)-1;
			for ($i = 0; $i < $nb_t; ++$i)
			{
				$e = $t[$i];

				// Si il n'y a pas encore de branche à se nom
				if (!isset($branch[$e]))
				{
					$branch[$e] = array();
				}

				// On se branche sur la branche suivante
				$branch = &$branch[$e];
			}

			// Ajout de l'élément sur la dernière branche
			$branch[] = $box;
		}

		echo "<ul class=\"boxes\" id=\"boxes_list\">\n";
		//BoxView::showBoxesTreeRec(null, $tree[0]);
		//groaw($tree);
		foreach ($tree as $sous_clef => $sous_branche)
		{
			BoxView::showBoxesTreeRec($sous_clef, $sous_branche);
		}

		echo "</ul>";
	}
	
	private static function showBoxesTreeRec($key, $branch)
	{
		if (is_array($branch))
		{
			echo "<li><h4>$key</h4><ul>\n";
			foreach ($branch as $new_key => $new_branch)
			{
				BoxView::showBoxesTreeRec($new_key, $new_branch);
			}
			echo "</ul></li>\n";
		}
		else
		{
			$description = htmlspecialchars($branch->box_array[count($branch->box_array)-1]);
			$nb_unread = $branch->nb_unread;

			$url = CNavigation::generateUrlToApp('Dashboard', 'index', array(
						'box' => $branch->name
						));

			$class = ($branch->name === $GLOBALS['box']) ? 'box selected_box' : 'box';

			echo "\t<li class=\"$class\"><a href=\"$url\">", wordwrap($description, 20, "<br/>", true),

				($nb_unread > 0) ? "</a> <strong>($nb_unread)</strong>" : '</a>', "</li>\n";
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
