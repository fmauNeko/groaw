<?php

class Dashboard {

	public function index() {
		CNavigation::setTitle(_('Dashboard'));
		$page_num = CNavigation::getPage();

		$mod = new MailMod();
		$mod->loadMails($page_num, NB_MAILS_BY_PAGE);

		$view = new MailView($mod);
		$view->showMails($page_num, NB_MAILS_BY_PAGE);

		$box_mod = new BoxMod();
		$box_mod->listeBoitesNbNonLus();

/*
		$vue = new CVueCourriel($mod);
		$vue->afficherOutilsListe($numero_page);

		$mod_boite = new CModBoite();
		$mod_boite->listeBoitesNbNonLus();

		$vue_boite = new CVueBoite($mod_boite);
		$vue_boite->afficherArbreBoites();
		
		$vue->afficherCourriels($numero_page, COURRIELS_PAR_PAGE);

		CModBoite::nommerBoite($GLOBALS['boite'], false);*/
	}
}

?>
