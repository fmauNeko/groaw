<?php
class CModCourriel extends AModele
{
	public $courriel;
	public $courriels;

	public function analyserCourriel($numero)
	{
		$structure = CImap::fetchstructure($numero);

		// Si c'est un beau mail de plusieurs parties
		if ($structure->type === TYPEMULTIPART && count($structure->parts) > 1)
		{
			$this->recPart($numero, $structure);
		}
		else
		{
			$this->recPart($numero,$structure,'1');
		}

		echo "<hr/>";
		echo "<hr/>";
		groaw($structure);
	}

	private function recPart($numero, $structure, $num_section=null)
	{
		echo "<hr/>";
		groaw($num_section);
		switch($structure->type)
		{
			case TYPEMULTIPART:
				$c = 1;
				groaw("multipart");
				foreach ($structure->parts as $partie)
				{
					// Oh mon DIEU de la récursivité !
					if ($num_section == null)
					{
						$section = $c++;
					}
					else
					{
						$section = $num_section.'.'.$c++;
					}
					$this->recPart($numero, $partie, $section);
				}
				break;
			case TYPETEXT:
				groaw("ok c'est du texte");
				
				$texte = CImap::fetchbody($numero,$num_section);

				switch ($structure->encoding)
				{
					/*// 7 bits (donc de l'ASCII de pas rigolo)
					case 0:
						break;

					// 8 bits
					case 1:
						break;*/

					// binaire
					case 2:
						$texte = "ohoh";
						break;

					// base64
					case 3:
						$texte = imap_base64($texte);
						break;

					// quoted-printable moche
					case 4:
						$texte = "ohoh";
						break;

					// autre (pas de chance mec)
					case 5:
						$texte = "ohoh";
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
							$charset = $parametre->value;
						}
					}

					if (strtoupper($charset) !== 'UTF-8')
					{
						$texte = iconv($charset, 'UTF-8', $texte);	
					}
				}

				groaw(htmlspecialchars($texte));
				break;
			default:
				groaw("non géré, c'est un mail de merde que vous avez");
				break;
		}
	}

	public function recupererCourriels()
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

			$this->courriels = $liste_finale;
		}
	}
}

?>
