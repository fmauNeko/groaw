<?php
class CVueListe extends AVueModele
{
	
	public function afficherMessages()
	{
		echo "<table>\n";	

		foreach ($this->modele->messages as $message)
		{
			echo "\t<tr><td>",
				$message->msgno,
				"</td><td>",
				htmlspecialchars($this->mime_to_utf8($message->subject)),
				"</td><td>",
				htmlspecialchars($this->mime_to_utf8($message->from)),
				"</td><td>",
				htmlspecialchars($message->date),
				"</td></tr>\n";
		}

		echo "</table>";
	}
}
?>
