<?php

class Dashboard {

	public function index() {

		$box_mod = new BoxMod();
		$box_mod->listBoxesNbUnread();
		
		$this->show($box_mod);
		//CNavigation::setTitle(_('Dashboard'));
		$page_num = CNavigation::getPage();

		$mod = new MailMod();
		$mod->loadMails($page_num, NB_MAILS_BY_PAGE);

		$view = new MailView($mod);
		$view->showMails($page_num, NB_MAILS_BY_PAGE);

		$box_view = new BoxView($box_mod);
		$box_view->showBoxesTree();

		$title = BoxMod::getBeautifulName($GLOBALS['box']);
		CNavigation::setTitle(CNavigation::getTitle() . ' ' . $title);
		CNavigation::setBodyTitle($title);

	}

	// Public canard koinkoin


	// Show a mail
	public function show($box_mod = null) {
		global $id;

		$id = MailMod::getId();

		// Si il n'y a aucun message
		if (!$id) {
			//groaw("oh nan");
			//new CMessage("Il n'y avait plus aucun courriel dans la boite.");	
			//new CRedirection("Boites.php");
			return;
		}

		$mod = new MailMod($id);
		$mod->analyse();
		$mod->setSeen($box_mod);

		//$mod->marquerLu($mod_boite);
		//$mod_boite->listeBoitesNbMessages();

		$vue = new MailView($mod);
		//$vue->afficherOutilsMessage();

		/*$vue_boite = new CVueBoite($mod_boite);
		$vue_boite->afficherBoitesDeplacement($numero);

		$vue->afficherBoutonsPrecedentSuivant();	*/
		if (DISTANT_CONTENT && isset($mod->mail->{'x-rss-item-link'}) && $mod->mail->{'x-rss-item-link'}) {
			$vue->showXRssMail();
		} else {
			$vue->showMail();
		}

		CNavigation::setTitle(CTools::mimeToUtf8($mod->mail->subject));
	}

}

?>
