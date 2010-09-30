<?php
class CVueCourriel extends AVueModele
{
	
	public function afficherCourriels()
	{
		if (count($this->modele->courriels) > 0)
		{
			echo "<ul class=\"messages\">\n";	

			foreach ($this->modele->courriels as $message)
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
}
?>
