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
		$boxes = CImap::getAllBoxes();

		$this->boxes = $boxes;
	}

	public function getNbUnread()
	{
		foreach ($this->boxes as &$box)
		{
			$status = CImap::status($box->name, SA_UNSEEN);
			$box->nb_unread = $status ? $status->unseen : 0;
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
		return 'Cache/'.md5($_SESSION['email']).'_'.$file;
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
		$info = CImap::status(CImap::getServer().$nom, $type);

		if ($info===false)
		{
			$box = new BoxMod($nom);
			$box->creer();
			$info = CImap::status(CImap::getServer().$nom, $type);
		}

		$info->nom = $nom;
		return $info;
	}

	public function existe()
	{
		$nom = new CUtf7($this->box);
		$status = CImap::status(CImap::getServer().$nom->fromUtf8(), 0);

		return $status !== false;
	}

	public function creer()
	{
		$nom = new CUtf7($this->box);
		if (CImap::createmailbox(CImap::getServer().$nom->fromUtf8())===false)
		{
			throw new Exception('Impossible de créer la boite:«'.$this->box.'»');
		}
	}
	
	public function supprimer()
	{
		if (CImap::deletemailbox(CImap::getServer().$this->box)===false)
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

	public function updateNbUnread($key, $nb, $relatif) {
		if (!$this->boxes) {
			$this->listBoxesNbUnread();
		}

		$boxes = $this->boxes;

		foreach ($this->boxes as $box) {
			if ($box->name === $key) {
				if ($relatif) {
					$nb = $box->nb_unread+$nb;
				}

				$box->nb_unread = $nb;

				break;
			}
		}

		$this->sortBoxesNbUnread();
		$this->saveCache('list_boxes_nb_unread');
	}
	
	public static function getBeautifulName($box) {

		if ($box === 'INBOX') {
			return _('Inbox');
		}

		$box = new CUtf7($box);
		$box = $box->toUtf8();

		$box = preg_replace('/^INBOX\./', '', $box);

		return preg_replace('@[\.\/\\\]@', ' : ', $box);
	}

	public static function simplifierNomBoite($nom)
	{
		return preg_replace('/^\{.+?\}/','',$nom);
	}

	public static function explodeBoxName($box, $name)
	{
		$name = new CUtf7($name);
		$name = $name->toUtf8();

		if ($box->delimiter) {
			return explode($box->delimiter, $name);
		} else {
			return array($name);
		}
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
	
	public function sortBoxesNbUnread() {
		usort($this->boxes, function($a,$b) {

			foreach (array('INBOX') as $box) {
				if ($a->name === $box) {
					return -1;
				}
				elseif ($b->name === $box) {
					return 1;
				}
			}

			if ($a->nb_unread === $b->nb_unread) {

				// Unread mails in boxes are placed in first
				if ($a->nb_unread ===  0 && isset($GLOBALS['BOXES_ORDER'])) {

					foreach ($GLOBALS['BOXES_ORDER'] as $box) {
						if ($a->name === $box) {
							return -1;
						}
						elseif ($b->name === $box) {
							return 1;
						}
					}
				}
				
				return strcmp($a->name, $b->name);
			}

			return ($a->nb_unread > $b->nb_unread) ? -1 : 1;
		});
	}

}

?>
