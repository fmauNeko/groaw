<?php
class CVueListe extends AVueModele
{
	
	public function afficherMessages()
	{
		if (count($this->modele->messages) > 0)
		{
			echo "<table class=\"messages\">\n";	

			foreach ($this->modele->messages as $message)
			{
				echo "\t<tr><td>",
					$message->msgno,
					"</td><td>",
					htmlspecialchars($this->mime_to_utf8($message->subject)),
					"</td><td>",
					htmlspecialchars($this->mime_to_utf8($message->from)),
					"</td><td>",
					strftime(FORMAT_DATE, strtotime($message->date)),
					"</td></tr>\n";
			}

			echo "</table>";
		}
		else
		{
			echo "<h3>Il n'y a pas de messages</h3>";
		}
	}

	public function afficherBoites()
	{
		echo "<ul>\n";
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
