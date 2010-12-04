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

		$c = imap_rfc822_parse_headers($headers);

		//groaw($c);	

		$this->courriel = $c;	
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

	public function recupererCourriels($page = 0, $nb_par_page = 12)
	{
		$liste_triee = CImap::sort(SORTDATE, 1);
		$nb_courriels = count($liste_triee);

		$i_debut = min($nb_courriels, $page * $nb_par_page);
		$i_fin = min($nb_courriels, $i_debut + $nb_par_page);

		if (($i_fin - $i_debut) === 0)
		{
			$this->courriels = array();
		}
		else
		{
			$liste = strval($liste_triee[$i_debut]);
			for ($i = $i_debut+1; $i < $i_fin; ++$i)
			{
				$liste .= ','.strval($liste_triee[$i]);
			}
			$this->courriels = CImap::fetch_overview($liste);
		}

		$this->nb_max_courriels = $nb_courriels;
	}

	public function deplacer($destination)
	{
		if (CImap::mail_move($this->num_courriel, $destination))
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
