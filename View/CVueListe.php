<?php
class CVueListe extends AVueModele
{
	
	public function afficherMessages()
	{
		if (count($this->modele->messages) > 0)
		{
			echo "<ul class=\"messages\">\n";	

			foreach ($this->modele->messages as $message)
			{
				echo "\t<li class=\"",
					$message->seen ? "lu" : "nonlu",
				 	"\"><a href=\"Courriels.php?EX=afficher&amp;numero=",
					$message->msgno,
					"\"><div class=\"num\">",
					$message->msgno,
					"</div><div class=\"expediteur\">",
					htmlspecialchars($this->mime_to_utf8($message->from)),
					"</div><div class=\"date\">",
					$this->formater_date_liste($message->date),
					"</div><div class=\"sujet\">",
					htmlspecialchars($this->mime_to_utf8($message->subject)),
					"</div></a></li>\n";
			}

			echo "</ul>";
		}
		else
		{
			echo "<h3>Il n'y a pas de messages</h3>";
		}
	}

	public function afficherBoites()
	{
		echo "<ul class=\"boites\">\n";
		foreach($this->modele->boites as $boite)
		{
			$l = explode($boite->delimiter,$this->utf7_to_utf8($boite->name));

			$description = htmlspecialchars(implode(':',array_slice($l,1)));
			
			if ($description === '')
			{
				$description = "Groaw";
			}

			$lien = rawurlencode(preg_replace('/^\{.+?\}/','',$boite->name));

			echo "\t<li><a href=\"Boites.php?EX=ouvrir&amp;boite=$lien\">", $description, '</a>';

			if ($boite->pasvus > 0)
			{
				echo ' <strong>(', $boite->pasvus, ')</strong>';
			}

			echo "</li>\n";
		}
		echo "</ul>";
	}
}
?>
