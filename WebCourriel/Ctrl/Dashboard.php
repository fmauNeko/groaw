<?php

class Dashboard {

	public function index() {

		$box_mod = new BoxMod();
		$box_mod->listBoxesNbUnread();
		
		$id = MailMod::getId();

		if (!$id) {
			DashboardView::showTools();
		} else {
			$this->show($box_mod, $id);
		}

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


	// Show a mail
	public function show($box_mod = null, $id = null) {
		global $id;

		// Si il n'y a aucun message
		if ($id === null) {
			$id = MailMod::getId();

			if (!$id) {
				new CMessage(_('The box was empty'));
				CNavigation::redirectToApp('Dashboard');
			}
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

	public function allread() {

		$mod = new BoxMod();
		$mod->markAllAsRead();

		$redirect = CNavigation::generateMergedUrl('Dashboard', 'index');
		CNavigation::redirectToURL($redirect);
	}
}

?>
