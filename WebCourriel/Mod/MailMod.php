<?php
class MailMod
{
	public $mail;
    public $id;
    public $structure;

	public $mails;
	public $nb_mails;

	public function __construct($id = 0)
	{
        $this->id = $id;
	}

	public function analyse()
	{
		$this->structure = CImap::fetchstructure($this->id, FT_UID);

		$headers = CImap::fetchheader($this->id, FT_UID);

		// Déplissage des headers
		$headers = preg_replace('/\r\n([\t ])/', '$1', $headers);

		// Parsage bourrin
		preg_match_all('/(.+?)[\t ]*:[\t ]*(.+)/', $headers, $matches);
		
		$headers = new stdClass();
		$l_matches = count($matches[0]);

		for ($i = 0; $i < $l_matches; ++$i)
		{
			$key = strtolower($matches[1][$i]);
			$headers->$key = rtrim($matches[2][$i]);
		}

		$this->mail = $headers;	
	}

	public function setSeen($box_mod = null)
	{
		$mail = CImap::NC_fetch_overview($this->id, FT_UID);

		if ($mail[0]->seen === 0)
		{
			// Parfois, fetchstructure de analyser ne suffit pas…
			CImap::setflag_full($this->id, '\Seen', ST_UID);
			
			if ($box_mod) {
				$box_mod->updateNbUnread($GLOBALS['box'], -1, true);
			}
		}
	}

    public function loadSection($section_id, $structure)
    {
		/*define('ENC7BIT', 0);
		define('ENC8BIT', 1);
		define('ENCBINARY', 2);
		define('ENCBASE64', 3);
		define('ENCQUOTEDPRINTABLE', 4);
		define('ENCOTHER', 5);*/

		//groaw($structure);
		$part = CImap::fetchbody($this->id, $section_id, FT_UID);

        switch ($structure->encoding)
        {
            // 7 bits (donc de l'ASCII de pas rigolo)
            case ENC7BIT:
                break;

            // 8 bits (plein d'encodages)
            case ENC8BIT:
                break;

            // binaire
			case ENCBINARY:
				$part = imap_binary($part);
                // Pas de break;

            // base64
            case ENCBASE64:
                $part = imap_base64($part);
                break;

            // quoted-printable moche
            case ENCQUOTEDPRINTABLE:
                $part = imap_qprint($part);
                break;

            // autre (pas de chance mec)
            case ENCOTHER:
                throw new Exception(_('A part of the mail is unreadable'));
		}

		return $part;
	}

	public function loadTextSection($section_id, $structure)
	{
		$text = $this->loadSection($section_id, $structure);

        // Recherche de l'encodage, pour effectuer une conversion
        if ($structure->ifparameters)
        {
            $charset = null;
            foreach ($structure->parameters as $parameter)
            {
                if (strtolower($parameter->attribute) === 'charset')
                {
                    $charset = strtoupper($parameter->value);
                }
            }

            if ($charset !== null && $charset !== 'UTF-8') {
				// TODO changer ça
                $text = str_replace('charset=iso-8859-1', '', CTools::toUtf8($charset, $text));
            }
        }

        return $text;
    }

	public function loadSortedList($page = 0, $nb_by_page = 12)
	{
		$sorted_list = CImap::sort(SORTDATE, 1, SE_NOPREFETCH | SE_UID);
		$nb_mails = count($sorted_list);

		$i_start = min($nb_mails, $page * $nb_by_page);
		$i_end = min($nb_mails, $i_start + $nb_by_page);

		$this->nb_mails = $nb_mails;
		return array_slice($sorted_list, $i_start, $i_end-$i_start);
	}

	public function loadMails($page = 0, $nb_by_page = 12)
	{
		$sorted_list = $this->loadSortedList($page, $nb_by_page);

		$header_list = CImap::fetch_overview(implode(',',$sorted_list), FT_UID);

		$final_list = $sorted_list;

		foreach ($header_list as $header)
		{
			$key = array_search($header->uid, $sorted_list);
			$final_list[$key] = $header;
		}


		$this->mails = $final_list;
	}

	public function deplacer($destination)
	{
		$this->deplacerListe($this->id, $destination);
	}

	public function deplacerListe($liste, $destination)
	{
		$boite = new CModBoite($destination);

		if (!$boite->existe())
		{
			$boite->creer();
		}

		if (!is_array($liste))
		{
			$liste = array($liste);
		}

		if (CImap::mail_move(implode(',',$liste), $destination, CP_UID))
		{
			// Applique la suppression du message dans la boite de départ
			CImap::expunge();
		}
	}

	// Utilitaire permettant de récupérer le numéro d'un message
	public static function getId() {
		return isset($_REQUEST['id']) ? intval($_REQUEST['id']) : false;
		// Si on ne connait pas le numéro, on prends le premier
		//		return min(1, CImap::num_msg());
	}

	// Récupère le nom du fichier donné dans la structure
	public static function getAttachmentName($structure, $default_name = 'Unknown')
	{
		foreach (	array_merge($structure->ifdparameters ? $structure->dparameters : array(),
					$structure->ifparameters ? $structure->parameters : array())
					as $parameter)
		{
			if (strpos(strtolower($parameter->attribute),'filename') === 0 || strpos(strtolower($parameter->attribute),'name') === 0)
			{
				return pathinfo(CTools::mimeToUtf8($parameter->value));
			}
		}

		$infos = array('basename' => $default_name, 'filename' => $default_name);

		if ($structure->type === 2)
		{
			$infos['extension'] = 'eml';
			$infos['basename'] .= '.eml';
		}

		return $infos;
	}

	// Récupère le mimetype à partir de la structure donnée
	public static function getMimeType($structure)
	{
		$types = array('text', 'multipart', 'message', 'application', 'audio', 'image', 'video', 'model', 'other');

		$mimetype = $types[$structure->type];

		if ($structure->ifsubtype)
		{
			$mimetype .= '/'.strtolower($structure->subtype);
		}

		return $mimetype;
	}
}

?>
