<?php
class CModBoite extends AModele
{
	public $boites;
	public $boite;

	public function __construct($boite = null)
	{
        $this->boite = $boite;
	}

	public function recupererBoites()
	{
		$boites = CImap::getmailboxes(SERVEUR_IMAP, '*');

		$this->boites = $boites;
	}

	public function recupererNbNonVusBoites()
	{
		foreach ($this->boites as &$boite)
		{
			$boite->nb_non_vus = CImap::status($boite->name, SA_UNSEEN)->unseen;
		}
	}

	public function recupererNbVusBoites()
	{
		foreach ($this->boites as &$boite)
		{
			$boite->nb_messages = CImap::status($boite->name, SA_MESSAGES)->messages;
		}
	}

	public function trierBoitesNbVus()
	{
		usort($this->boites, function($a,$b)
		{
			if ($a->nb_messages === $b->nb_messages)
			{
				return 0;
			}

			return ($a->nb_messages > $b->nb_messages) ? -1 : 1;
		});
	}
	
	public function trierBoitesNbNonVus()
	{
		usort($this->boites, function($a,$b)
		{
			if ($a->nb_non_vus === $b->nb_non_vus)
			{
				return 0;
			}

			return ($a->nb_non_vus > $b->nb_non_vus) ? -1 : 1;
		});
	}

	public function chargerCacheBoites($fichier, $duree_cache = DUREE_CACHE_LISTE)
	{
		$fichier = $this->calculerNomfichier($fichier);

		if (!file_exists($fichier))
		{
			return false;
		}

		// Si le cache a plus de 5 minutes
		if (time() - filemtime($fichier) > $duree_cache)
		{
			groaw("cache");
			return false;
		}

		$donnees = file_get_contents($fichier);

		if ($donnees === false)
		{
			groaw("Impossible de se servir du fichier de cache.");
			return false;
		}
		
		$this->boites = unserialize($donnees);
		
		return true;
	}

	public function enregistrerCacheBoites($fichier)
	{
		$donnees = serialize($this->boites);
		$fichier = $this->calculerNomfichier($fichier);

		if (!file_put_contents($fichier, $donnees))
		{
			groaw("Impossible de mettre en cache les boites. L'application peut être lente.");	
		}
	}

	private function calculerNomfichier($fichier)
	{
		return '../Cache/'.md5($_SESSION['email']).'_'.$fichier;
	}

	public function effacerCaches()
	{
		$fichiers = glob($this->calculerNomfichier('*'));

		foreach ($fichiers as $fichier)
		{
			unlink($fichier);
		}
	}

	public function listeBoitesNbNonLus()
	{
		if (!$this->chargerCacheBoites('liste_boites_nb_non_lus'))
		{
			$this->recupererBoites();
			$this->recupererNbNonVusBoites();
			$this->trierBoitesNbNonVus();
			$this->enregistrerCacheBoites('liste_boites_nb_non_lus');
		}
	}

	public function listeBoitesNbMessages()
	{
		if (!$this->chargerCacheBoites('liste_boites_nb_messages', 7200))
		{
			$this->recupererBoites();
			$this->recupererNbVusBoites();
			$this->trierBoitesNbVus();
			$this->enregistrerCacheBoites('liste_boites_nb_messages');
		}
	}

	public function recupererInfosAcceuil()
	{
		$this->boites = Array(
			'livraison'		=> CModBoite::recupererInfoBoite('INBOX', SA_MESSAGES),
			'interessant'	=> CModBoite::recupererInfoBoite('INBOX.Interesting', SA_MESSAGES),
			'normal'		=> CModBoite::recupererInfoBoite('INBOX.Normal', SA_MESSAGES),
			'ininteressant'	=> CModBoite::recupererInfoBoite('INBOX.Unexciting', SA_MESSAGES),
			'poubelle' 		=> CModBoite::recupererInfoBoite('INBOX.Trash', SA_MESSAGES)
		);

		$this->boites['livraison']->titre		= "Livraison";
		$this->boites['interessant']->titre		= "Intéressant";
		$this->boites['normal']->titre			= "Normal";
		$this->boites['ininteressant']->titre	= "Inintéressant";
		$this->boites['poubelle']->titre		= "Poubelle";
	}

	public static function recupererInfoBoite($nom,$type)
	{
		$info = CImap::status(SERVEUR_IMAP.$nom, $type);

		if ($info===false)
		{
			$boite = new CModBoite($nom);
			$boite->creer();
			$info = CImap::status(SERVEUR_IMAP.$nom, $type);
		}

		$info->nom = $nom;
		return $info;
	}

	public function existe()
	{
		$nom = new CUtf7($this->boite);
		$status = CImap::status(SERVEUR_IMAP.$nom->fromUtf8(), 0);

		return $status !== false;
	}

	public function creer()
	{
		$nom = new CUtf7($this->boite);
		if (CImap::createmailbox(SERVEUR_IMAP.$nom->fromUtf8())===false)
		{
			throw new Exception('Impossible de créer la boite:«'.$this->boite.'»');
		}
	}
	
	public function supprimer()
	{
		if (CImap::deletemailbox(SERVEUR_IMAP.$this->boite)===false)
		{
			throw new Exception('Impossible de supprimer la boite:«'.$this->boite.'»');
		}
	}

	public function vider()
	{
		CImap::delete('1:*');
		CImap::expunge();
	}

	public function marquerToutLus()
	{
		$num_msg = CImap::num_msg();

		if ($num_msg > 0)
		{
			CImap::setflag_full('1:'.$num_msg, '\Seen');
			$this->changerNbNonVus($GLOBALS['boite'], 0, false);
		}

	}

	public function changerNbNonVus($boite, $nb, $relatif)
	{
		$this->listeBoitesNbNonLus();

		$clef = SERVEUR_IMAP.$boite;
		$boites = $this->boites;

		foreach ($this->boites as $boite)
		{
			if ($boite->name === $clef)
			{
				if ($relatif)
				{
					$nb = $boite->nb_non_vus+$nb;
				}

				$boite->nb_non_vus = $nb;

				break;
			}
		}

		$this->trierBoitesNbNonVus();
		$this->enregistrerCacheBoites('liste_boites_nb_non_lus');
	}
}

?>
