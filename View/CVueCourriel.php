<?php
class CVueCourriel extends AVueModele
{
	
	public function afficherCourriels($numero_page, $nb_par_page)
	{
		if (count($this->modele->courriels) > 0)
		{
			$boite = rawurlencode($GLOBALS['boite']);

			echo "<ul class=\"messages\">\n";	
			
			foreach ($this->modele->courriels as $message)
			{
				$sujet = $this->mime_to_utf8($message->subject);
				$sujet = ($sujet === '') ? 'Pas de sujet' : $sujet;

				echo "\t<li>\n\t\t<a href=\"?EX=afficher&amp;boite=$boite&amp;numero=",
					$message->msgno,
					"\">\n\t\t\t<h4>",
					htmlspecialchars($sujet),
					"</h4>\n\t\t\t<p>",
					$message->seen ? "Lu" : "Non lu",
					", de <strong>",
					$this->formaterDate($message->date),
					"</strong> par <strong>",
					htmlspecialchars(str_replace('@MISSING_DOMAIN','',preg_replace('/\s<.+>$/','',$this->mime_to_utf8($message->from)))),
					"</strong>.</p>\n\t\t</a>\n\t</li>\n";
			}

			echo "</ul>";

			$pagination = CNavigation::pagination($this->modele->nb_max_courriels, $numero_page, $nb_par_page);

			if ($pagination)
			{
				function afficherPage($boite, $num_page, $texte)
				{
					if ($num_page !== false)
					{
						echo "<a href=\"?EX=liste&amp;boite=$boite&amp;page=$num_page\">$texte</a> ";
					}
				}
				
				echo "<p class=\"pagination\">Pages:<br/>\n";

				afficherPage($boite, $pagination['directions']['precedent'], 'Précedent');

				$difference = -1;
				foreach ($pagination['pages'] as $pagin)
				{
					// Si il y a un décalage supérieur à 1
					// C'est qu'il y a eu un saut dans la pagination
					if ($pagin-$difference > 1)
					{
						echo '… ';
					}

					if ($pagin === $numero_page)
					{
						echo "<strong>";
						afficherPage($boite, $pagin, $pagin+1);
						echo "</strong>";
					} else {
						afficherPage($boite, $pagin, $pagin+1);
					}
					
					$difference = $pagin;
				}

				afficherPage($boite, $pagination['directions']['suivant'], 'Suivant');
				
				echo "\n</p>\n";
			}

		}
		else
		{
			echo "<h3>La boite est vide.</h3>";
		}
	}

	public function afficherOutilsMessage()
	{
		$boite = rawurlencode($GLOBALS['boite']);
		$numero = $this->modele->num_courriel;
		echo <<<EOT
<div class="outils_courriel">
<ul class="outils_base">
	<li><a href="#">Répondre</a></li>
	<li><a href="#">Transférer</a></li>
	<br/>
	<li><a href="Courriels.php?EX=deplacer&amp;destination=INBOX.Interesting&amp;boite=$boite&amp;numero=$numero" accesskey="1">Intéressant</a></li>
	<li><a href="Courriels.php?EX=deplacer&amp;destination=INBOX.Normal&amp;boite=$boite&amp;numero=$numero" accesskey="2">Normal</a></li>
	<li><a href="Courriels.php?EX=deplacer&amp;destination=INBOX.Unexciting&amp;boite=$boite&amp;numero=$numero" accesskey="3">Inintéressant</a></li>
	<br/>
	<li><a href="Courriels.php?EX=deplacer&amp;destination=INBOX.Trash&amp;boite=$boite&amp;numero=$numero" accesskey="0">Supprimer</a></li>
</ul>

EOT;
	}
	
	public function afficherOutilsListe($numero_page)
	{
		$boite = rawurlencode($GLOBALS['boite']);
		$numero = $this->modele->num_courriel;

echo <<<EOT
<div class="outils_courriel">
<ul class="outils_base">
	<li><a href="?EX=enterrer&amp;boite=$boite&amp;page=$numero_page">Enterrer tout ça</a></li>
EOT;

		if ($boite === 'INBOX.Trash')
		{
			echo "\t<li><a href=\"?EX=detruire_courriels&amp;boite=$boite\">Sortir les poubelles</a></li>\n";
		}

		echo <<<EOT
</ul>
<h3>Changer de boite :</h3>

EOT;
	}

	public function afficherPersonne($objet)
	{
		if ($objet->host === 'SYNTAX-ERROR')
		{
			echo "Adresse invalide";
		}
		else
		{
			if (isset($objet->personal))
			{
				echo htmlspecialchars($objet->personal), ' <em>&lt;';
			}

			echo htmlspecialchars($objet->mailbox);
			
			if ($objet->host !== '')
			{
				echo '@', htmlspecialchars($objet->host);
			}
			
			if (isset($objet->personal))
			{
				echo '&gt;</em>';
			}
		}
	}

	public function afficherListePersonnes($nom, $classe, $texte)
	{
		echo "<tr>\n\t\t\t\t<th>$nom</th>",
					"\n\t\t\t\t<td>\n\t\t\t\t\t<ul class=\"$classe\">\n";

		$adresses = imap_rfc822_parse_adrlist($texte, '');

		if (is_array($adresses))
		{
			foreach ($adresses as $adresse)
			{
				echo "\t\t\t\t\t\t<li>";
				$this->afficherPersonne($adresse);
				echo "</li>\n";
			}
		}

		echo "\t\t\t\t\t</ul>\n\t\t\t\t</td>\n\t\t\t</tr>";
	}

    public function afficherCourriel()
    {
        $structure = $this->modele->structure;
        $numero = $this->modele->num_courriel;

		$courriel = $this->modele->courriel;

		echo "\n<!--\n";
		print_r($courriel);
		echo "-->\n";

		$sujet = $this->mime_to_utf8($courriel->subject);
		$sujet = ($sujet === '') ? 'Pas de sujet' : $sujet;
	
        echo "<div class=\"courriel\">\n\t<div class=\"headers\">\n\t\t<h2>",
					htmlspecialchars($sujet),
					"</h2>\n\t\t<table>\n\t\t\t";
		
		if (isset($courriel->from))
		{
			$this->afficherListePersonnes("Émetteurs", "emetteurs", $courriel->from);
		}
			
		if (isset($courriel->to))
		{
			$this->afficherListePersonnes("Destinataires", "destinataires", $courriel->to);
		}

		if (isset($courriel->date))
		{
			$date = $this->formaterDate($courriel->date);
		}
		else
		{
			$date = 'Inconnue';
		}

		echo "<tr>\n\t\t\t\t<th>Date d'envoi</th>",
				"\n\t\t\t\t<td>$date</td>\n\t\t\t</tr>";

		// C'est pour mes flux rss :-)
		if (isset($courriel->{'x-rss-item-link'}))
		{
			$lien = htmlspecialchars($courriel->{'x-rss-item-link'});
			echo "<tr>\n\t\t\t\t<th>Url de l'article</th>",
				"\n\t\t\t\t<td><a href=\"$lien\">$lien</a></td>\n\t\t\t</tr>";
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
                $texte = $this->modele->recupererPartieTexte($num_section, $structure);
			    
                if ($structure->ifsubtype && $structure->subtype === 'HTML')
                {
					echo '<div class="desole_c_moche">';
					
					$nettoyeur = new CNettoyeurHtml($texte, CONTENU_DISTANT);
					$nettoyeur->nettoyerEtAfficher();

					echo '</div>';
                    //echo '<iframe id="apercu_html" src="?EX=partie&amp;numero='.$numero.'&amp;section='.$num_section.'&amp;boite=',rawurlencode($GLOBALS['boite']),'" sandbox="allow-scripts"></iframe>';

                }
                else
                {
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
