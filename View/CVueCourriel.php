<?php
class CVueCourriel extends AVueModele
{
	
	public function afficherCourriels()
	{
		if (count($this->modele->courriels) > 0)
		{
			echo "<ul class=\"messages\">\n";	

			foreach ($this->modele->courriels as $message)
			{
				echo "\t<li class=\"",
					$message->seen ? "lu" : "nonlu",
				 	"\">\n\t\t<a href=\"Courriels.php?EX=afficher&amp;numero=",
					$message->msgno,
					"\">\n\t\t\t<div class=\"num\">",
					$message->msgno,
					"</div>\n\t\t\t<div class=\"expediteur\">",
					htmlspecialchars($this->mime_to_utf8($message->from)),
					"</div>\n\t\t\t<div class=\"date\">",
					$this->formater_date_liste($message->date),
					"</div>\n\t\t\t<div class=\"sujet\">",
					htmlspecialchars($this->mime_to_utf8($message->subject)),
					"</div>\n\t\t</a>\n\t</li>\n";
			}

			echo "</ul>";
		}
		else
		{
			echo "<h3>Il n'y a pas de messages</h3>";
		}
	}

    public function afficherCourriel()
    {
        $structure = $this->modele->structure;
        $numero = $this->modele->num_courriel;
	
        echo "<div class=\"courriel\">\n";
        
        // Si c'est un beau mail de plusieurs parties
		if ($structure->type === TYPEMULTIPART && count($structure->parts) > 1)
		{
			$this->affichageRecursif($numero, $structure);
		}
		else
		{
			$this->affichageRecursif($numero,$structure,'1');
		}

        echo "\n</div>\n<!-- Structure du courriel\n";
        print_r($structure);
        echo "-->\n";
    }

    private function affichageRecursif($numero, $structure, $num_section=null)
    {
		groaw($num_section);
		switch($structure->type)
		{
			case TYPEMULTIPART:
                if (strtolower($structure->subtype) === 'alternative')
                {
                    groaw("alternative");

                    // Recherche des alternatives possibles
                    foreach ($structure->parts as $partie)
                    {
                        groaw($partie->subtype);
                    }
                }
                else
                {
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
                        $this->affichageRecursif($numero, $partie, $section);
                    }
                }
				break;
			case TYPETEXT:
				groaw("ok c'est du texte");
			    
                $texte = $this->modele->recupererPartieTexte($numero, $num_section);
				groaw(htmlspecialchars($texte));
				break;
			default:
                new Exception("Une partie du mail est non gérée");
				break;
		}
	}
}
?>
