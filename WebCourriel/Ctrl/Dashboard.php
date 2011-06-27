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
		$box_mod->listBoxesNbUnread();

		$box_view = new BoxView($box_mod);
		$box_view->showBoxesTree();

		CNavigation::setTitle(BoxMod::getBeautifulName($GLOBALS['box']));
	}
}

?>
