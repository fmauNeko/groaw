<?php
class CModListe extends AModele
{
	public $messages;
	public $boites;

	public function recupererMessages()
	{
		$liste_triee = CImap::sort(SORTDATE, 1);
		$nb_entetes = count($liste_triee);

		if ($nb_entetes === 0)
		{
			$this->messages = array();
		}
		else
		{
			$liste_entetes = CImap::fetch_overview("1:$nb_entetes");

			$liste_finale = $liste_triee;

			foreach ($liste_entetes as $entete)
			{
				$clef = array_search($entete->msgno,$liste_triee);
				$liste_finale[$clef] = $entete;
			}

			$this->messages = $liste_finale;
		}
	}

	public function recupererBoites()
	{
		$boites = CImap::getmailboxes(SERVEUR_IMAP, '*');

		foreach ($boites as $boite)
		{
			$boite->pasvus = CImap::status($boite->name, SA_UNSEEN)->unseen;
		}

		$this->boites = $boites;
	}
}

?>
