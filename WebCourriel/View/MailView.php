<?php
class MailView extends AbstractView {
	
	public function showMails($page_num, $nb_by_page) {
		if (count($this->model->mails) === 0) {
			echo '<h3>', _('The box is empty'), '</h3>';
			return;
		}
		
		echo "<ul class=\"messages\">\n";	

		$period_name = null;

		foreach ($this->model->mails as $mail) {
			$subject = isset($mail->subject) ? CTools::mimeToUtf8($mail->subject) : false;
			$subject = !$subject ? _('No subject') : $subject;


			$date = $this->locateDate($mail->date);

			// ucfirst is just for to uppercase the first letter
			$new_period_name = ucfirst($this->translateTimePeriod($date[0], $date[2]));

			if ($new_period_name !== $period_name)
			{
				$period_name = $new_period_name;
				echo "\t<li class=\"section\">$period_name</li>\n";
			}

			$url = CNavigation::generateMergedUrl('Dashboard', 'show', array(
						'msgno' => $mail->msgno));

			echo "\t<li class=\"", $mail->seen ? "mail_read" : "mail_unread",
				"\" >\n\t\t<a href=\"$url\">\n\t\t\t<h4>",
				htmlspecialchars($subject),
				"</h4>\n\t\t\t<p>",
				$mail->seen ? _('Read') : _('Unread'), ', ',
				$this->formateDate($date), ' ';
		
			printf(_('by <strong>%s</strong>'), 
					htmlspecialchars(
						str_replace('@MISSING_DOMAIN','',preg_replace('/\s<.+>$/','',CTools::mimeToUtf8($mail->from)))));
				echo ".</p>\n\t\t</a>\n\t</li>\n";
		}

		echo "</ul>";

		$pagination = CNavigation::pagination(
				$this->model->nb_mails, $page_num, $nb_by_page);

		if ($pagination)
		{
			function afficherPage($boite, $num_page, $texte)
			{
				if ($num_page !== false)
				{
			$url = CNavigation::generateUrlToApp('Dashboard', 'show', array(
						'box' => $GLOBALS['box'],
						'msgno' => $mail->msgno));
					echo "<a href=\"?EX=liste&amp;boite=$boite&amp;page=$num_page\">$texte</a> ";
				}
			}
			
			echo "<p class=\"pagination\">Pages:<br/>\n";

			afficherPage($box, $pagination['directions']['precedent'], 'Précedent');

			$difference = -1;
			foreach ($pagination['pages'] as $pagin)
			{
				// Si il y a un décalage supérieur à 1
				// C'est qu'il y a eu un saut dans la pagination
				if ($pagin-$difference > 1)
				{
					echo '… ';
				}

				if ($pagin === $page_num)
				{
					echo "<strong>";
					afficherPage($box, $pagin, $pagin+1);
					echo "</strong>";
				} else {
					afficherPage($box, $pagin, $pagin+1);
				}
				
				$difference = $pagin;
			}

			afficherPage($box, $pagination['directions']['suivant'], 'Suivant');
			
			echo "\n</p>\n";
		}

	}

	public function afficherOutilsMessage()
	{
		$boite = rawurlencode($GLOBALS['boite']);
		$numero = $this->model->num_courriel;
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
	
	public function afficherOutilsListe($page_num)
	{
		$boite = rawurlencode($GLOBALS['boite']);
		$numero = $this->model->num_courriel;

echo <<<EOT
<div class="outils_courriel">
<ul class="outils_base">
	<!--<li><a href="?EX=enterrer&amp;boite=$boite&amp;page=$page_num">Enterrer tout ça</a></li>-->
	<li><a href="?EX=marquer_tout_lu&amp;boite=$boite&amp;page=$page_num">C'est bon, j'ai tout lu</a></li>
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
		if (isset($objet->host) && $objet->host === 'SYNTAX-ERROR')
		{
			echo "Adresse invalide";
		}
		else
		{
			if (isset($objet->personal))
			{
				echo htmlspecialchars($objet->personal), ' <em>&lt;';
			}

			if (isset($objet->mailbox))
			{
				echo htmlspecialchars($objet->mailbox);
			}
			
			if (isset($objet->host) && $objet->host !== '')
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

	public function afficherBoutonsPrecedentSuivant()
	{
		echo "<ul class=\"boutons_navigation\">\n";

		$numero = $this->model->num_courriel;
		$boite = rawurlencode($GLOBALS['boite']);

		if ($numero > 1)
		{
			echo "\t<li><a href=\"Courriels.php?EX=afficher&amp;boite=$boite&amp;numero=",$numero-1,"\">Prédecent</a></li>\n";
		}
		
		if ($numero < CImap::num_msg())
		{
			echo "\t<li><a href=\"Courriels.php?EX=afficher&amp;boite=$boite&amp;numero=",$numero+1,"\">Suivant</a></li>\n";
		}

		echo "</ul>\n";
	}

    public function afficherCourriel()
    {
        $structure = $this->model->structure;
        $numero = $this->model->num_courriel;

		$courriel = $this->model->courriel;

		echo "\n<!--\n";
		print_r($courriel);
		echo "-->\n";

		$subject = CTools::mimeToUtf8($courriel->subject);
		$subject = ($subject === '') ? 'Pas de subject' : $subject;
	
        echo "<div class=\"courriel\">\n\t<div class=\"headers\">\n\t\t<h2>",
					htmlspecialchars($subject),
					"</h2>\n\t\t<table>\n\t\t\t";
		
		if (isset($courriel->from))
		{
			$this->afficherListePersonnes("Émetteurs", "emetteurs",
					CTools::mimeToUtf8($courriel->from));
		}
			
		if (isset($courriel->to))
		{
			$this->afficherListePersonnes("Destinataires", "destinataires",
					CTools::mimeToUtf8($courriel->to));
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

	private function affichageRecursifMultipart($numero, $structure, $num_section=null)
	{
		$traiter_sous_parties = true;

		if ($structure->ifsubtype)
		{
			$subtype = strtoupper($structure->subtype);
			if ($subtype === 'ALTERNATIVE')
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
				$traiter_sous_parties = false;
			}
			elseif ($subtype === 'MIXED')
			{
				if ($num_section === '1')
				{
					$num_section = null;
				}
			}
		}

		if ($traiter_sous_parties)
		{
			// Compteur pour les sections
			$c = 1;
			//groaw("multipart");
			foreach ($structure->parts as $partie)
			{
				// Gestion du numéro de section
				if ($num_section === null)
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
	}

	private function affichageRecursifText($numero, $structure, $num_section=null)
	{
		//groaw("ok c'est du texte");
		$texte = $this->model->recupererPartieTexte($num_section, $structure);

		if ($structure->ifsubtype && $structure->subtype === 'HTML')
		{

			$nettoyeur = new CNettoyeurHtml($texte, CONTENU_DISTANT);
			$texte = $nettoyeur->recupererHtmlNettoye();

			$chemin = '../Cache/mail-'.md5($texte).'.html';
			file_put_contents($chemin, $texte);
			
			echo '<iframe id="apercu_html" src="',$chemin, '"></iframe>';
			CHead::ajouterJs('ajusterFrame');

		}
		else
		{
			$texte = htmlspecialchars($texte);

			$texte = preg_replace('/(\s)(https?|ftp)\:\/\/(.+?)(\s)/', '$1<a href="$2://$3">$2://$3</a>$4',' '.$texte.' ');

			echo nl2br($texte), "\n<br/>\n";
		}
	}

	private function affichageRecursifImage($numero, $structure, $num_section=null)
	{

		if (!$structure->ifsubtype)
		{
			throw new exception("canard");
		}

		$extentions = array('jpeg','png','gif');
		$extention = array_search(strtolower($structure->subtype), $extentions);

		if ($extention === false)
		{
			throw new exception("type non supporté");
		}

		$extention = $extentions[$extention];

		$nom = CModCourriel::getNomAttachment($structure);

		$chemin = '../Cache/attachment-'.md5($GLOBALS['boite'].$numero.$num_section.$structure->bytes.$nom);
		$chemin_ext = "$chemin.$extention";

		if (file_exists($chemin_ext))
		{
			groaw("cache");
			$image = file_get_contents($chemin_ext);
		}
		else
		{
			$image = $this->model->recupererPartie($num_section, $structure);
			file_put_contents($chemin_ext, $image);
		}

		$vignette = CVignette::cheminVignette($chemin,$extention,TAILLE_VIGNETTES,TAILLE_VIGNETTES);

		if ($vignette)
		{
			echo "<a href=\"$chemin_ext\"><img src=\"$vignette\" alt=\"\" /></a>\n";
		}

		$this->affichageRecursifFichier($numero, $structure, $num_section);

		/*$image = base64_encode($image);

		echo "<img src=\"data:image/jpeg;base64,$image\" alt=\"\"/>";*/

		//groaw($image);
	}
	
	private function affichageRecursifFichier($numero, $structure, $num_section=null)
	{
		$mimetype = CModCourriel::getMimeType($structure);

		$nom = CModCourriel::getNomAttachment($structure);
		$taille = intval($structure->bytes);

		$lien = 'Courriels.php?EX=partie&amp;boite='.rawurlencode($GLOBALS['boite'])."&amp;numero=$numero&amp;section=$num_section"; 

		self::afficherVignetteFichier($mimetype, $nom, $taille, $lien);

		//groaw($structure);
	}

	public static function afficherVignetteFichier($mimetype, $nom, $taille, $lien = '#')
	{
		$fichier = self::getMimeIcone($mimetype);
		$taille = CTools::nbBytesToKibis($taille);

		$chemin = $nom['filename'];
		$message_danger = '<br/>';

		if (isset($nom['extension']))
		{
			$chemin .= '.'.$nom['extension'];

			if (in_array($nom['extension'], $GLOBALS['EXTENSIONS_DANGEREUSES']))
			{
				$message_danger = "<p class=\"danger\">Ce fichier d'extension exécutable (.".htmlspecialchars($nom['extension']).") est probablement un programme malveillant.</p>";
			}
		}

		echo "<div class=\"piece_jointe\"><a href=\"$lien\">\n\t<img src=\"../Img/mimes/$fichier.png\" alt=\"",
			htmlspecialchars($mimetype), "\" />\n\t",
			$message_danger,"\n\t<strong>",
			htmlspecialchars($chemin), "</strong>\n\t<em class=\"taille\">",
			number_format($taille[0], (fmod($taille[0], 1) == 0.0) ? 0 : 2), ' ', $taille[1], "</em>\n</a></div>\n";
	}

	private static function getMimeIcone($mimetype)
	{

		$fichier = str_replace('/', '-', $mimetype);

        if (file_exists("../Img/mimes/$fichier.png"))
        {
                return $fichier;
        }

        $generics = array('image', 'audio', 'text', 'video', 'package', 'message');

        foreach($generics as $id => $generic)
		{
                if (strpos($mimetype, $generic) !== FALSE)
                {
                        return "$generic-x-generic";
                }
        }
        return 'unknown';
	}

    public function affichageRecursif($numero, $structure, $num_section=null)
    {
		/*define('TYPETEXT', 0);
		define('TYPEMULTIPART', 1);
		define('TYPEMESSAGE', 2);
		define('TYPEAPPLICATION', 3);
		define('TYPEAUDIO', 4);
		define('TYPEIMAGE', 5);
		define('TYPEVIDEO', 6);
		define('TYPEMODEL', 7);
		define('TYPEOTHER', 8);*/


		//groaw($num_section);
		switch($structure->type)
		{
			case TYPETEXT:
				$this->affichageRecursifText($numero, $structure, $num_section);
				break;
			case TYPEMULTIPART:
				$this->affichageRecursifMultipart($numero, $structure, $num_section);
				break;
			case TYPEMESSAGE:
				groaw("ATTENTION : Mode non définitif");
				$this->affichageRecursifFichier($numero, $structure, $num_section);
				//groaw($structure);
				break;
			case TYPEIMAGE:
				$this->affichageRecursifImage($numero, $structure, $num_section);
				break;
			case TYPEAPPLICATION:
			case TYPEAUDIO:
			case TYPEVIDEO:
			case TYPEMODEL:
			case TYPEOTHER:
				$this->affichageRecursifFichier($numero, $structure, $num_section);
				break;
			default:
                throw new Exception("Une partie du mail est d'un type inconnu : ".$structure->type);
		}
	}

}
?>
