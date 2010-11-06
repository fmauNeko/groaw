<?php
class CVueCourriel extends AVueModele
{
	
	public function afficherCourriels()
	{
		if (count($this->modele->courriels) > 0)
		{
			$boite = rawurlencode($GLOBALS['boite']);

			echo "<ul class=\"messages\">\n";	
			
			foreach ($this->modele->courriels as $message)
			{
				echo "\t<li class=\"",
					$message->seen ? "lu" : "nonlu",
				 	"\">\n\t\t<a href=\"Courriels.php?EX=afficher&amp;boite=$boite&amp;numero=",
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

	public function afficherOutils($environemment)
	{
		echo <<<EOT
<div class="outils_courriel">
<ul class="outils_base">
	<li><a href="#">Répondre</a></li>
	<li><a href="#">Transférer</a></li>
	<br/>
	<li><a href="Courriels.php?EX=deplacer&amp;destination=INBOX.Interesting" accesskey="1">Intéressant</a></li>
	<li><a href="Courriels.php?EX=deplacer&amp;destination=INBOX.Normal" accesskey="2">Normal</a></li>
	<li><a href="Courriels.php?EX=deplacer&amp;destination=INBOX.Unexciting" accesskey="3">Inintéressant</a></li>
	<br/>
	<li><a href="Courriels.php?EX=deplacer&amp;destination=INBOX.Trash" accesskey="0">Supprimer</a></li>
	</li>
</ul>

EOT;
	}

	public function afficherPersonne($objet)
	{
		echo htmlspecialchars($objet->mailbox),'@',htmlspecialchars($objet->host); 
	}

    public function afficherCourriel()
    {
        $structure = $this->modele->structure;
        $numero = $this->modele->num_courriel;

		$courriel = $this->modele->courriel;
	
        echo "<div class=\"courriel\">\n\t<div class=\"headers\">\n\t\t<h2>",
					htmlspecialchars($this->mime_to_utf8($courriel->subject)),
					"</h2>\n\t\t<table>\n\t\t\t";
		
		echo "<tr>\n\t\t\t\t<th>Émetteurs</th>",
					"\n\t\t\t\t<td>\n\t\t\t\t\t<ul class=\"emetteurs\">\n";
	
		foreach ($courriel->from as $emetteur)
		{
			echo "\t\t\t\t\t\t<li>";
			$this->afficherPersonne($emetteur);
			echo "</li>\n";
		}

		echo "\t\t\t\t\t</ul>\n\t\t\t\t</td>\n\t\t\t</tr>";
		
		if (isset($courriel->to))
		{
			echo "<tr>\n\t\t\t\t<th>Destinataires</th>",
						"\n\t\t\t\t<td>\n\t\t\t\t\t<ul class=\"destinataires\">\n";

			foreach ($courriel->to as $destinataire)
			{
				echo "\t\t\t\t\t\t<li>";
				$this->afficherPersonne($destinataire);
				echo "</li>\n";
			}

			echo "\t\t\t\t\t</ul>\n\t\t\t\t</td>\n\t\t\t</tr>";
		}
			
		echo "\n\t\t</table>\n\t</div>\n\t<div class=\"corp\">\n";

        // Si c'est un beau mail de plusieurs parties
		if ($structure->type === TYPEMULTIPART && count($structure->parts) > 1)
		{
			$this->affichageRecursif($numero, $structure);
		}
		else
		{
			$this->affichageRecursif($numero,$structure,'1');
		}

        echo "\n\t</div>\n</div>\n<!-- Structure du courriel\n";
        print_r($structure);
        echo "-->\n";
    }

    private function affichageRecursif($numero, $structure, $num_section=null)
    {
		//groaw($num_section);
		switch($structure->type)
		{
			case TYPEMULTIPART:
                if ($structure->ifsubtype && strtoupper($structure->subtype) === 'ALTERNATIVE')
                {
                    //groaw("alternative");
                   
                    // Recherche de chaque type que l'on préfère
                    global $PREFERENCES_MIME;
                    foreach ($PREFERENCES_MIME as $mime)
                    {
                        $c = 1;
                        foreach ($structure->parts as $partie)
                        {
                            if (strtoupper($partie->subtype) === $mime)
                            {
                                // Gestion du numéro de section
                                if ($num_section === null)
                                {
                                    $section = $c;
                                }
                                else
                                {
                                    $section = $num_section.'.'.$c;
                                }
                                //groaw($partie->subtype);
                                $this->affichageRecursif($numero, $partie, $section);
                                return;
                            }
                            ++$c;
                        }
                    }

                    // Si on est là, c'est que l'on ne préfère rien du tout,
                    // on prends donc le premier de la liste
                    if (count($structure->parts) > 0)
                    {
                        // Gestion du numéro de section
                        if ($num_section === null)
                        {
                            $section = '1';
                        }
                        else
                        {
                            $section = $num_section.'.1';
                        }
                        $this->affichageRecursif($numero, $structure->parts[0], $section);
                    }
                }
                else
                {
                    // Compteur pour les sections
                    $c = 1;

                    //groaw("multipart");
                    foreach ($structure->parts as $partie)
                    {
                        // Gestion du numéro de section
                        if ($num_section == null)
                        {
                            $section = $c++;
                        }
                        else
                        {
                            $section = $num_section.'.'.$c++;
                        }
                        // Oh mon DIEU de la récursivité !
                        $this->affichageRecursif($numero, $partie, $section);
                    }
                }
				break;
			case TYPETEXT:
				//groaw("ok c'est du texte");
			    
                if ($structure->ifsubtype && $structure->subtype === 'HTML')
                {
			    	//groaw(htmlspecialchars($texte));

                    echo '<iframe id="apercu_html" src="?EX=partie&amp;numero='.$numero.'&amp;section='.$num_section.'" sandbox="allow-scripts"></iframe>';

                }
                else
                {
                    $texte = $this->modele->recupererPartieTexte($num_section, $structure);
                    $texte = htmlspecialchars($texte);

                    $texte = preg_replace('/(\s)(https?|ftp)\:\/\/(.+?)(\s)/', '$1<a href="$2://$3">$2://$3</a>$4',' '.$texte.' ');

                    echo nl2br($texte);
                }
				break;
			default:
                new Exception("Une partie du mail est non gérée");
				break;
		}
	}
}
?>
