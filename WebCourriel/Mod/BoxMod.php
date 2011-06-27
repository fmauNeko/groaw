<?php
class BoxMod
{
	public $boxes;
	public $box;

	public function __construct($box = null)
	{
        $this->box = $box;
	}

	public function recupererBoites()
	{
		$boxes = CImap::getmailboxes(SERVEUR_IMAP, '*');

		$this->boxes = $boxes;
	}

	public function recupererNbNonVusBoites()
	{
		foreach ($this->boxes as &$box)
		{
			$box->nb_non_vus = CImap::status($box->name, SA_UNSEEN)->unseen;
		}
	}

	public function recupererNbVusBoites()
	{
		foreach ($this->boxes as &$box)
		{
			$box->nb_messages = CImap::status($box->name, SA_MESSAGES)->messages;
		}
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
		
		$this->boxes = unserialize($donnees);
		
		return true;
	}

	public function enregistrerCacheBoites($fichier)
	{
		$donnees = serialize($this->boxes);
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
			$this->traiterNomsBoites();
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
			$this->traiterNomsBoites();
			$this->trierBoitesNbVus();
			$this->enregistrerCacheBoites('liste_boites_nb_messages');
		}
	}

	public function recupererInfosAcceuil()
	{
		$this->boxes = Array(
			'livraison'		=> CModBoite::recupererInfoBoite('INBOX', SA_MESSAGES),
			'interessant'	=> CModBoite::recupererInfoBoite('INBOX.Interesting', SA_MESSAGES),
			'normal'		=> CModBoite::recupererInfoBoite('INBOX.Normal', SA_MESSAGES),
			'ininteressant'	=> CModBoite::recupererInfoBoite('INBOX.Unexciting', SA_MESSAGES),
			'poubelle' 		=> CModBoite::recupererInfoBoite('INBOX.Trash', SA_MESSAGES)
		);

		$this->boxes['livraison']->titre		= "Livraison";
		$this->boxes['interessant']->titre		= "Intéressant";
		$this->boxes['normal']->titre			= "Normal";
		$this->boxes['ininteressant']->titre	= "Inintéressant";
		$this->boxes['poubelle']->titre		= "Poubelle";
	}

	public static function recupererInfoBoite($nom,$type)
	{
		$info = CImap::status(SERVEUR_IMAP.$nom, $type);

		if ($info===false)
		{
			$box = new CModBoite($nom);
			$box->creer();
			$info = CImap::status(SERVEUR_IMAP.$nom, $type);
		}

		$info->nom = $nom;
		return $info;
	}

	public function existe()
	{
		$nom = new CUtf7($this->box);
		$status = CImap::status(SERVEUR_IMAP.$nom->fromUtf8(), 0);

		return $status !== false;
	}

	public function creer()
	{
		$nom = new CUtf7($this->box);
		if (CImap::createmailbox(SERVEUR_IMAP.$nom->fromUtf8())===false)
		{
			throw new Exception('Impossible de créer la boite:«'.$this->box.'»');
		}
	}
	
	public function supprimer()
	{
		if (CImap::deletemailbox(SERVEUR_IMAP.$this->box)===false)
		{
			throw new Exception('Impossible de supprimer la boite:«'.$this->box.'»');
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
			$this->changerNbNonVus($GLOBALS['box'], 0, false);
		}

	}

	public function changerNbNonVus($box, $nb, $relatif)
	{
		$this->listeBoitesNbNonLus();

		$clef = SERVEUR_IMAP.$box;
		$boxes = $this->boxes;

		foreach ($this->boxes as $box)
		{
			if ($box->name === $clef)
			{
				if ($relatif)
				{
					$nb = $box->nb_non_vus+$nb;
				}

				$box->nb_non_vus = $nb;

				break;
			}
		}

		$this->trierBoitesNbNonVus();
		$this->enregistrerCacheBoites('liste_boites_nb_non_lus');
	}
	
	public static function nommerBoite($nom, $complement)
	{
		if ($complement && $complement !== '')
		{
			$complement = ' : '.$complement;
		}

		switch ($nom)
		{
			case 'INBOX':
				CNavigation::nommer("Espace de livraison$complement");
				break;
			case 'INBOX.Interesting':
				CNavigation::nommer("Courriers intéressant$complement");
				break;
			case 'INBOX.Normal':
				CNavigation::nommer("Courriers normaux$complement");
				break;
			case 'INBOX.Unexciting':
				CNavigation::nommer("Courriers inintéressant$complement");
				break;
			case 'INBOX.Trash':
				CNavigation::nommer("Poubelle$complement");
				break;
			default:
				$nom = new CUtf7($nom);
				CNavigation::nommer(htmlspecialchars($nom->toUtf8()).$complement);
		}
	}

	public static function simplifierNomBoite($nom)
	{
		return preg_replace('/^\{.+?\}/','',$nom);
	}

	public static function explodeNomBoite($box, $nom)
	{
		$nom = new CUtf7($nom);
		return explode($box->delimiter, $nom->toUtf8());
	}

	public static function creerDescription($tableau_box)
	{
		$description = htmlspecialchars(implode(' : ',array_slice($tableau_box,1)));
		
		if ($description === '')
		{
			$description = "Groaw";
		}

		return $description;
	}

	public function traiterNomsBoites()
	{
		$boxes = &$this->boxes;

		foreach($boxes as $clef => $box)
		{
			$nom = CModBoite::simplifierNomBoite($box->name);

			$l = CModBoite::explodeNomBoite($box, $nom);
			$description = CModBoite::creerDescription($l); 
			
			$lien = rawurlencode($nom);

			$box->lien = $lien;
			$box->tableau_box = $l;
			$box->nom = $nom;
			$box->description = $description;

			$boxes[$clef] = $box;
		}

		return $boxes;

	}
	
	public function trierBoitesNbVus()
	{
		usort($this->boxes, function($a,$b)
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
		$boxes = &$this->boxes;

		usort($boxes, function($a,$b)
		{
			if ($a->nb_non_vus === $b->nb_non_vus)
			{
				// Le trie par nombre de messages est prioritaire
				if ($a->nb_non_vus ===  0)
				{

					foreach ($GLOBALS['ORDRE_BOITES'] as $box)
					{
						if ($a->nom === $box)
						{
							return -1;
						}
						elseif ($b->nom === $box)
						{
							return 1;
						}
					}

					if (strpos($a->nom, 'INBOX.RSS') === 0)
					{
						if (strpos($b->nom, 'INBOX.RSS') !== 0)
							return -1;
					}
						
					elseif (strpos($b->nom, 'INBOX.RSS') === 0)
					{
						if (strpos($a->nom, 'INBOX.RSS') !== 0)
							return 1;
					}

					// Les archives vont à la fin :-)	
					elseif (strpos($a->nom, 'INBOX.Archive') === 0)
					{
						if (strpos($b->nom, 'INBOX.Archive') !== 0)
							return 1;
					}
						
					elseif (strpos($b->nom, 'INBOX.Archive') === 0)
					{
						if (strpos($a->nom, 'INBOX.Archive') !== 0)
							return -1;
					}
				}
				
				return strcmp($a->name, $b->name);
			}

			return ($a->nb_non_vus > $b->nb_non_vus) ? -1 : 1;
		});


		/*foreach ($boites as $b)
		{
			groaw($b->nom . " -- " . $b->nb_non_vus);
		}	
		die("canard");*/
	}


}

?>
