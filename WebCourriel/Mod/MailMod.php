<?php
class MailMod
{
	public $courriel;
    public $id;
    public $structure;

	public $mails;
	public $nb_mails;

	public function __construct($id = 0)
	{
        $this->id = $id;
	}

	public function analyser()
	{
		$this->structure = CImap::fetchstructure($this->num_courriel);

		$headers = CImap::fetchheader($this->num_courriel);

		// Déplissage des headers
		$headers = preg_replace('/\r\n([\t ])/', '$1', $headers);

		// Parsage bourrin
		preg_match_all('/(.+?)[\t ]*:[\t ]*(.+)/', $headers, $matches);
		
		$headers = new stdClass();
		$l_matches = count($matches[0]);

		for ($i = 0; $i < $l_matches; ++$i)
		{
			$clef = strtolower($matches[1][$i]);
			$headers->$clef = rtrim($matches[2][$i]);
		}

		$this->courriel = $headers;	
	}

	public function marquerLu($mod_boite)
	{
		$mail = CImap::fetch_overview($this->num_courriel);

		if ($mail[0]->seen === 0)
		{
			// Parfois, fetchstructure de analyser ne suffit pas…
			CImap::setflag_full($this->num_courriel, '\Seen');
			
			// Gestion du cache
			$mod_boite->changerNbNonVus($GLOBALS['boite'], -1, true);
		}
	}

    public function recupererPartie($num_section, $structure)
    {
		/*define('ENC7BIT', 0);
		define('ENC8BIT', 1);
		define('ENCBINARY', 2);
		define('ENCBASE64', 3);
		define('ENCQUOTEDPRINTABLE', 4);
		define('ENCOTHER', 5);*/

		//groaw($structure);
		$partie = CImap::fetchbody($this->num_courriel,$num_section);

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
				$partie = imap_binary($partie);
                // Pas de break;

            // base64
            case ENCBASE64:
                $partie = imap_base64($partie);
                break;

            // quoted-printable moche
            case ENCQUOTEDPRINTABLE:
                $partie = imap_qprint($partie);
                break;

            // autre (pas de chance mec)
            case ENCOTHER:
                throw new Exception("Une partie du mail est illisible");
		}

		return $partie;
	}

	public function recupererPartieTexte($num_section, $structure)
	{
		$texte = $this->recupererPartie($num_section, $structure);

        // Recherche de l'encodage, pour effectuer une conversion
        if ($structure->ifparameters)
        {
            $charset = null;
            foreach ($structure->parameters as $parametre)
            {
                if ($parametre->attribute === 'charset')
                {
                    $charset = strtoupper($parametre->value);
                }
            }


            if ($charset !== null && $charset !== 'UTF-8')
            {
                $texte = str_replace('charset=iso-8859-1', '', COutils::toUtf8($charset, $texte));
            }
        }

        return $texte;
    }

	public function loadSortedList($page = 0, $nb_by_page = 12)
	{
		$sorted_list = CImap::sort(SORTDATE, 1, SE_NOPREFETCH);
		$nb_mails = count($sorted_list);

		$i_start = min($nb_mails, $page * $nb_by_page);
		$i_end = min($nb_mails, $i_start + $nb_by_page);

		$this->nb_mails = $nb_mails;
		return array_slice($sorted_list, $i_start, $i_end-$i_start);
	}

	public function loadMails($page = 0, $nb_by_page = 12)
	{
		$sorted_list = $this->loadSortedList($page, $nb_by_page);

		$header_list = CImap::fetch_overview(implode(',',$sorted_list));

		$final_list = $sorted_list;

		foreach ($header_list as $header)
		{
			$key = array_search($header->msgno, $sorted_list);
			$final_list[$key] = $header;
		}


		$this->mails = $final_list;
	}

	public function deplacer($destination)
	{
		$this->deplacerListe($this->num_courriel, $destination);
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

		if (CImap::mail_move(implode(',',$liste), $destination))
		{
			// Applique la suppression du message dans la boite de départ
			CImap::expunge();
		}
	}

	// Utilitaire permettant de récupérer le numéro d'un message
	public static function numero()
	{
		if (isset($_REQUEST['numero']))
		{
			return intval($_REQUEST['numero']);
		}
		else
		{
			// Si on ne connait pas le numéro, on prends le premier
			return min(1, CImap::num_msg());

		}
	}

	// Récupère le nom du fichier donné dans la structure
	public static function getNomAttachment($structure, $nom_base = 'Sans nom')
	{
		foreach (	array_merge($structure->ifdparameters ? $structure->dparameters : array(),
					$structure->ifparameters ? $structure->parameters : array())
					as $parametre)
		{
			if (strpos($parametre->attribute,'filename') === 0 || strpos($parametre->attribute,'name') === 0)
			{
				return pathinfo(COutils::mimeToUtf8($parametre->value));
			}
		}

		$infos = array('basename' => $nom_base, 'filename' => $nom_base);

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
