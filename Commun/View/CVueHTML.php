<?php
class CVueHTML
{
	public function __construct($page)
	{
		$fichier = "../Html/$page.html";

		if (file_exists($fichier))
		{
			include $fichier;
			return;
		}

		$fichier = "../Commun/Html/$page.html";
		if (file_exists($fichier))
		{
			include $fichier;
			return;
		}

		throw new Exception("Impossible de charger la vue HTML");
	}
}
?>
