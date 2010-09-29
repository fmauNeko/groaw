<?php
class CModBoite extends AModele
{
	public $boites;
	public $boite;

	public function recupererBoites()
	{
		$boites = CImap::getmailboxes(SERVEUR_IMAP, '*');

		foreach ($boites as $boite)
		{
			$boite->pasvus = CImap::status($boite->name, SA_UNSEEN)->unseen;
		}

		$this->boites = $boites;
	}

	public function recupererInfosAcceuil()
	{
		return Array(
			'livraison'		=> $this->recupererInfoBoite('INBOX', SA_MESSAGES),
			'interessant'	=> $this->recupererInfoBoite('INBOX.Interesting', SA_MESSAGES),
			'normal'		=> $this->recupererInfoBoite('INBOX.Normal', SA_MESSAGES),
			'ininteressant'	=> $this->recupererInfoBoite('INBOX.Unexciting', SA_MESSAGES),
			'poubelle' 		=> $this->recupererInfoBoite('INBOX.Trash', SA_MESSAGES)
		);
	}

	public function recupererInfoBoite($nom,$type)
	{
		$info = CImap::status(SERVEUR_IMAP.$nom, $type);

		if ($info===false)
		{
			$this->creerBoite($nom);
			$info = CImap::status(SERVEUR_IMAP.$nom, $type);
		}

		return $info;
	}

	public function creerBoite($nom)
	{
		if (CImap::createmailbox(SERVEUR_IMAP.$nom)===false)
		{
			throw new Exception('Impossible de créer la boite:«'.$nom.'»');
		}
	}
}

?>
