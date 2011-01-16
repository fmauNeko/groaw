<?php
class CModCourriel extends AModele
{
	public $courriel;
    public $num_courriel;
    public $structure;
	public $courriels;
	public $nb_max_courriels;

	public function __construct($numero = 0)
	{
        $this->num_courriel = $numero;
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

    public function recupererPartieTexte($num_section, $structure)
    {
		//groaw($structure);
        $texte = CImap::fetchbody($this->num_courriel,$num_section);

        switch ($structure->encoding)
        {
            /*// 7 bits (donc de l'ASCII de pas rigolo)
            case 0:
                break;

            // 8 bits (plein d'encodages)
            case 1:
                break;*/

            // binaire
            case 2:
                break;

            // base64
            case 3:
                $texte = imap_base64($texte);
                break;

            // quoted-printable moche
            case 4:
                $texte = imap_qprint($texte);
                break;

            // autre (pas de chance mec)
            case 5:
                new Exception("Une partie du mail est illisible");
                break;
        }
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
                $texte = str_replace('charset=iso-8859-1', '', iconv($charset, 'UTF-8', $texte));
            }
        }

        return $texte;
    }

	public function recupererListeTriee($page = 0, $nb_par_page = 12)
	{
		$liste_triee = CImap::sort(SORTDATE, 1);
		$nb_courriels = count($liste_triee);

		$i_debut = min($nb_courriels, $page * $nb_par_page);
		$i_fin = min($nb_courriels, $i_debut + $nb_par_page);

		$this->nb_max_courriels = $nb_courriels;
		return array_slice($liste_triee, $i_debut, $i_fin-$i_debut);
	}

	public function recupererCourriels($page = 0, $nb_par_page = 12)
	{
		$liste_triee = $this->recupererListeTriee($page, $nb_par_page);

		$liste_entetes = CImap::fetch_overview(implode(',',$liste_triee));

		$liste_finale = $liste_triee;

		foreach ($liste_entetes as $entete)
		{
			$clef = array_search($entete->msgno, $liste_triee);
			$liste_finale[$clef] = $entete;
		}


		$this->courriels = $liste_finale;
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
			// Si on ne connait pas le numéro, on prends le dernier numéro
			return CImap::num_msg();

		}
	}
}

?>
