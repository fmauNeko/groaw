<?php
class BoxMod
{
	public $boxes;
	public $box;

	public function __construct($box = null)
	{
        $this->box = $box;
	}

	public function getBoxes()
	{
		$boxes = CImap::getmailboxes(IMAP_SERVER, '*');

		$this->boxes = $boxes;
	}

	public function getNbUnread()
	{
		foreach ($this->boxes as &$box)
		{
			$box->nb_unread = CImap::status($box->name, SA_UNSEEN)->unseen;
		}
	}

	public function recupererNbVusBoites()
	{
		foreach ($this->boxes as &$box)
		{
			$box->nb_messages = CImap::status($box->name, SA_MESSAGES)->messages;
		}
	}

	public function loadCache($file)
	{
		$file = $this->getCacheFilename($file);

		if (!file_exists($file))
		{
			return false;
		}

		// Si le cache a plus de 5 minutes
		if (time() - filemtime($file) > CACHE_LENGTH)
		{
			return false;
		}

		$data = file_get_contents($file);

		if ($data === false)
		{
			groaw(_('Unable to load cache'));
			return false;
		}
		
		$this->boxes = unserialize($data);
		
		return true;
	}

	public function saveCache($file)
	{
		$data = serialize($this->boxes);
		$file = $this->getCacheFilename($file);

		if (!file_put_contents($file, $data))
		{
			groaw(_('Unable to cache boxes list'));
		}
	}

	private function getCacheFilename($file)
	{
		return '../Cache/'.md5($_SESSION['email']).'_'.$file;
	}

	public function effacerCaches()
	{
		$file = glob($this->calculerNomfile('*'));

		foreach ($files as $file)
		{
			unlink($file);
		}
	}

	public function listBoxesNbUnread() {

		if (!$this->loadCache('list_boxes_nb_unread'))
		{
			$this->getBoxes();
			$this->getNbUnread();
			$this->treatBoxesNames();
			$this->sortBoxesNbUnread();
			$this->saveCache('list_boxes_nb_unread');
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
			'livraison'		=> BoxMod::recupererInfoBoite('INBOX', SA_MESSAGES),
			'interessant'	=> BoxMod::recupererInfoBoite('INBOX.Interesting', SA_MESSAGES),
			'normal'		=> BoxMod::recupererInfoBoite('INBOX.Normal', SA_MESSAGES),
			'ininteressant'	=> BoxMod::recupererInfoBoite('INBOX.Unexciting', SA_MESSAGES),
			'poubelle' 		=> BoxMod::recupererInfoBoite('INBOX.Trash', SA_MESSAGES)
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
			$box = new BoxMod($nom);
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
	
	public static function getBeautifulName($box) {

		if ($box === 'INBOX') {
			return _('Inbox');
		}

		$box = new CUtf7($box);
		$box = $box->toUtf8();

		$box = preg_replace('/^INBOX\./', '', $box);

		return str_replace('.', ' : ', $box);
	}

	public static function simplifierNomBoite($nom)
	{
		return preg_replace('/^\{.+?\}/','',$nom);
	}

	public static function explodeBoxName($box, $name)
	{
		$name = new CUtf7($name);
		return explode($box->delimiter, $name->toUtf8());
	}

	public static function createDescrsiption($box_array)
	{
		$description = htmlspecialchars(implode(' : ',array_slice($box_array,1)));
		
		if ($description === '')
		{
			$description = "Groaw";
		}

		return $description;
	}

	public function treatBoxesNames()
	{
		$boxes = &$this->boxes;

		foreach($boxes as $key => $box)
		{
			$name = BoxMod::simplifierNomBoite($box->name);

			$l = BoxMod::explodeBoxName($box, $name);
			$description = BoxMod::createDescrsiption($l); 
			
			$box->box_array = $l;
			$box->name = $name;
			$box->description = $description;

			$boxes[$key] = $box;
		}

		return $boxes;

	}
	
	public function sortBoxesNbUnread()
	{
		usort($this->boxes, function($a,$b)
		{
			if ($a->nb_unread === $b->nb_unread)
			{
				return 0;
			}

			return ($a->nb_unread > $b->nb_unread) ? -1 : 1;
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
