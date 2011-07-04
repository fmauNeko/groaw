<?php
class MailView extends AbstractView {
	
	public function showMails($page_num, $nb_by_page) {
		
		global $id, $box;

		$id_ul = md5($box).$page_num;

		echo "<ul class=\"messages\" id=\"message_list_$id_ul\">\n";	
		
		if (count($this->model->mails) === 0) {
			echo "\t<h3>", _('The box is empty'), "</h3>\n</ul>";
			return;
		}

		$period_name = null;

		foreach ($this->model->mails as $mail) {

			//groaw($mail);

			$subject = isset($mail->subject) ? CTools::mimeToUtf8($mail->subject) : false;
			$subject = !$subject ? _('No subject') : $subject;


			$date = isset($mail->date) ? $this->locateDate($mail->date) : null;

			// ucfirst is just for to uppercase the first letter
			$new_period_name = ucfirst($this->translateTimePeriod($date[0], $date[2]));

			if ($new_period_name !== $period_name)
			{
				$period_name = $new_period_name;
				echo "\t<li class=\"section\">$period_name</li>\n";
			}

			$url = CNavigation::generateMergedUrl('Dashboard', 'index', array(
						'id' => $mail->uid));

			$class = $mail->seen ? 'mail_read' : 'mail_unread';

			if ($mail->uid == $id) {
				$class .= ' selected_mail';
			}

			echo "\t<li class=\"$class\" >\n\t\t<a href=\"$url\">\n\t\t\t<h4>",
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

		$pagination = false;// CNavigation::pagination(
		//		$this->model->nb_mails, $page_num, $nb_by_page);

		if ($pagination)
		{
			global $box;

			function afficherPage($boite, $num_page, $texte)
			{
				if ($num_page !== false)
				{
			$url = CNavigation::generateUrlToApp('Dashboard', 'show', array(
						'box' => $box,
						'id' => $mail->uid));
					echo "<a href=\"?EX=liste&amp;boite=$boite&amp;page=$num_page\">$texte</a> ";
				}
			}
			
			echo "<p class=\"pagination\">Pages:<br/>\n";

			afficherPage($box, $pagination['directions']['previous'], _('Previous'));

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

			afficherPage($box, $pagination['directions']['next'], _('Next'));
			
			echo "\n</p>\n";
		}

	}

	/*public function afficherOutilsMessage()
	{
		$boite = rawurlencode($GLOBALS['boite']);
		$id = $this->model->num_courriel;
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
	}*/
	
	/*public function afficherOutilsListe($page_num)
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
	}*/

	public function showContact($contact)
	{
		$contact = preg_replace_callback('/<?(.+)@(.+)>?/', function($t) {
			$r = filter_var($t[1].'@'.$t[2], FILTER_VALIDATE_EMAIL);

			if ($r === false) {
				return $t[0];
			}	

			return '<em>&lt;'.$t[1].'@'.$t[2].'&gt;</em>';

		}, htmlspecialchars($contact));


		echo $contact;
	}

	public function showContactsList($name, $class, $text)
	{
		echo "<tr>\n\t\t\t\t<th>$name</th>",
					"\n\t\t\t\t<td>\n\t\t\t\t\t<ul class=\"$class\">\n";

		//groaw($text);
		//$address = imap_rfc822_parse_adrlist($text, '');
		$address = explode(',', $text);

		foreach ($address as $address)
		{
			echo "\t\t\t\t\t\t<li>";
			$this->showContact($address);
			echo "</li>\n";
		}

		echo "\t\t\t\t\t</ul>\n\t\t\t\t</td>\n\t\t\t</tr>";
	}

	/*public function afficherBoutonsPrecedentSuivant()
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
	}*/

    public function showMail() {
        $structure = $this->model->structure;
        $id = $this->model->id;

		$mail = $this->model->mail;

		/*echo "\n<!--\n";
		print_r($mail);
		echo "-->\n";*/

		$subject = CTools::mimeToUtf8($mail->subject);
		$subject = ($subject === '') ? _('No subject') : $subject;
	
        echo "<div class=\"mail\" id=\"mail_$id\">\n\t<div class=\"headers\">\n\t\t<h2>",
					htmlspecialchars($subject),
					"</h2>\n\t\t<table>\n\t\t\t";

		echo <<<END

			<a href="javascript:byId('mail_$id').className += ' maximized';">Op</a>

END;

		if (isset($mail->from)) {
			$this->showContactsList(_('From'), 'from',
					CTools::mimeToUtf8($mail->from));
		}
			
		if (isset($mail->to)) {
			$this->showContactsList(_('To'), 'to',
					CTools::mimeToUtf8($mail->to));
		}

		if (isset($mail->date)) {
			$date = $this->formateDate($mail->date);
		}
		else {
			$date = _('Inconnue');
		}

		echo "<tr>\n\t\t\t\t<th>", _('Send date'),
				"</th>\n\t\t\t\t<td>$date</td>\n\t\t\t</tr>";

		// For RSS in mails (dev love it)
		if (isset($mail->{'x-rss-item-link'})) {
			$url = htmlspecialchars($mail->{'x-rss-item-link'});
			echo "<tr>\n\t\t\t\t<th>", _('Article url'),
				"</th>\n\t\t\t\t<td><a href=\"$url\">$url</a></td>\n\t\t\t</tr>";
		}

		echo "\n\t\t</table>\n\t</div>\n\t<div class=\"mail_body\">\n";

        // Si c'est un beau mail de plusieurs parties
		if ($structure->type === TYPEMULTIPART && count($structure->parts) > 1) {
			$this->recursiveDisplay($id, $structure);
		}
		else {
			$this->recursiveDisplay($id,$structure,'1');
		}

        echo "\n\t</div>\n</div>\n";//<!-- Structure du courriel\n";
       /* print_r($structure);
        echo "-->\n";*/
	}

	private function recursiveDisplayMultipart($id, $structure, $section_id=null) {
		$treat_subpart = true;

		if ($structure->ifsubtype) {
			$subtype = strtoupper($structure->subtype);

			if ($subtype === 'ALTERNATIVE') {
				//groaw("alternative");
			   
				// Recherche de chaque type que l'on préfère
				global $MIME_ORDER;
				foreach ($MIME_ORDER as $mime) {
					$c = 1;
					foreach ($structure->parts as $part) {
						if (strtoupper($part->subtype) === $mime) {
							// Gestion du numéro de section
							if ($section_id === null) {
								$section = $c;
							}
							else {
								$section = $section_id.'.'.$c;
							}
							//groaw($partie->subtype);
							$this->recursiveDisplay($id, $part, $section);
							return;
						}
						++$c;
					}
				}

				// Si on est là, c'est que l'on ne préfère rien du tout,
				// on prends donc le premier de la liste
				if (count($structure->parts) > 0) {
					// Gestion du numéro de section
					if ($section_id === null) {
						$section = '1';
					}
					else {
						$section = $section_id.'.1';
					}
					$this->recursiveDisplay($id, $structure->parts[0], $section);
				}
				$treat_subpart = false;
			}
			elseif ($subtype === 'MIXED') {
				if ($section_id === '1') {
					$section_id = null;
				}
			}
		}

		if ($treat_subpart) {
			// Compteur pour les sections
			$c = 1;
			//groaw("multipart");
			foreach ($structure->parts as $part) {
				// Gestion du numéro de section
				if ($section_id === null) {
					$section = $c++;
				}
				else {
					$section = $section_id.'.'.$c++;
				}
				
				// Oh mon DIEU de la récursivité !
				// Oh my god, recursivity !
				$this->recursiveDisplay($id, $part, $section);
			}
		}
	}

	private function recursiveDisplayText($id, $structure, $section_id=null)
	{
		//groaw("ok c'est du texte");
		$text = $this->model->loadTextSection($section_id, $structure);

		if ($structure->ifsubtype && $structure->subtype === 'HTML')
		{

			$nettoyeur = new CNettoyeurHtml($text, DISTANT_CONTENT);
			$text = $nettoyeur->recupererHtmlNettoye();

			$html_filename = 'Cache/mail-'.md5($text).'.html';
			file_put_contents($html_filename, $text);
		
			global $ROOT_PATH;

			echo '<iframe id="html_view" src="',$ROOT_PATH, '/', $html_filename, '"></iframe>';
			// TODO op op op
			CHead::addJS('adjustFrame');

		}
		else
		{
			$text = htmlspecialchars($text);

			// Make url as links
			$text = preg_replace('/(\s)(https?|ftp)\:\/\/(.+?)(\s)/', '$1<a href="$2://$3">$2://$3</a>$4',' '.$text.' ');

			echo nl2br($text), "\n<br/>\n";
		}
	}

	private function recursiveDisplayImage($id, $structure, $section_id=null)
	{

		if (!$structure->ifsubtype) {
			throw new exception(_('A mail part doesn\'t have know type'));
		}

		$extentions = array('jpeg','png','gif');
		$extention = array_search(strtolower($structure->subtype), $extentions);

		if ($extention === false) {
			$this->recursiveDisplayFile($id, $structure, $section_id);
			return;
		}

		$extention = $extentions[$extention];

		$name = MailMod::getAttachmentName($structure);

		$path = 'Cache/attachment-'.md5($GLOBALS['box'].$id.$section_id.$structure->bytes.$name);
		$path_ext = "$path.$extention";

		if (file_exists($path)) {
			$image = file_get_contents($path_ext);
		}
		else {
			$image = $this->model->loadSection($section_id, $structure);
			file_put_contents($path_ext, $image);
		}

		$vignette = CVignette::cheminVignette($path,$extention,VIGNETTE_SIZE,VIGNETTE_SIZE);

		if ($vignette) {
			global $ROOT_PATH;
			echo "<a href=\"$path_ext\"><img src=\"$ROOT_PATH/$vignette\" alt=\"\" /></a>\n";
		}

		$this->recursiveDisplayFile($id, $structure, $section_id);

		/*$image = base64_encode($image);

		echo "<img src=\"data:image/jpeg;base64,$image\" alt=\"\"/>";*/
	}
	
	private function recursiveDisplayFile($id, $structure, $section_id=null)
	{
		$mimetype = MailMod::getMimeType($structure);

		$name = MailMod::getAttachmentName($structure);
		$size = intval($structure->bytes);

		//$lien = 'Courriels.php?EX=partie&amp;boite='.rawurlencode($GLOBALS['boite'])."&amp;numero=$numero&amp;section=$section_id"; 
		// TODO
		$url = '?';

		self::showFileIcon($mimetype, $name, $size, $url);
	}

	public static function showFileIcon($mimetype, $name, $size, $url = '#')
	{
		$file = self::getMimeIcone($mimetype);
		$size = CTools::nbBytesToKibis($size);

		$path = $name['filename'];
		//$message_danger = '<br/>';

		if (isset($name['extension']))
		{
			$path .= '.'.$name['extension'];

		/*	if (in_array($name['extension'], $GLOBALS['EXTENSIONS_DANGEREUSES']))
			{
				$message_danger = "<p class=\"danger\">Ce file d'extension exécutable (.".htmlspecialchars($name['extension']).") est probablement un programme malveillant.</p>";
			}*/
		}

		global $ROOT_PATH;
		echo "<div class=\"piece_jointe\"><a href=\"$url\">\n\t<img src=\"$ROOT_PATH/Img/mimes/$file.png\" alt=\"",
			htmlspecialchars($mimetype), "\" />\n\t",
			"\n\t<strong>",
			htmlspecialchars($path), "</strong>\n\t<em class=\"size\">",
			number_format($size[0], (fmod($size[0], 1) == 0.0) ? 0 : 2), ' ', $size[1], "</em>\n</a></div>\n";
	}

	private static function getMimeIcone($mimetype)
	{

		$fichier = str_replace('/', '-', $mimetype);

        if (file_exists("Img/mimes/$fichier.png"))
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

    public function recursiveDisplay($id, $structure, $section_id=null)
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


		//groaw($section_id);
		switch($structure->type)
		{
			case TYPETEXT:
				$this->recursiveDisplayText($id, $structure, $section_id);
				break;
			case TYPEMULTIPART:
				$this->recursiveDisplayMultipart($id, $structure, $section_id);
				break;
			case TYPEMESSAGE:
				groaw("ATTENTION : Mode non définitif");
				$this->recursiveDisplayFile($id, $structure, $section_id);
				//groaw($structure);
				break;
			case TYPEIMAGE:
				$this->recursiveDisplayImage($id, $structure, $section_id);
				break;
			case TYPEAPPLICATION:
			case TYPEAUDIO:
			case TYPEVIDEO:
			case TYPEMODEL:
			case TYPEOTHER:
				$this->recursiveDisplayFile($id, $structure, $section_id);
				break;
			default:
                throw new Exception(_('A part of the mail is unknown'));
		}
	}

}
?>
