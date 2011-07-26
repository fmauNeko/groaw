<?php

define('NO_LOGIN_REQUIRED', true);

class IspFactory {

	public function index() {
		CNavigation::setTitle(_('Create new ISP File'));
		IspView::showForm(isset($_REQUEST['domain']) ? $_REQUEST['domain'] : '');
	}

	public function submit() {

		if (!isset($_POST['domain'])) {
			$_POST['domain'] = DEFAULT_DOMAIN;
		}

		$domain = strtr($_POST['domain'], '/:{}', '----');
		$filename = "ISP/$domain.xml";

		$xml = ISP::createFile($_POST);

		if (file_put_contents($filename, $xml) === false) {
			new CMessage(_('ISP directory is not writable. Unable to save your configuration.'));
		}

		new CMessage(_('Successfull creation of ISP configuration file'));

		CNavigation::redirectToApp(null);
	}
}

?>
