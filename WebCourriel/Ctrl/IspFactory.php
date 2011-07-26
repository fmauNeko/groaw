<?php

define('NO_LOGIN_REQUIRED', true);

class IspFactory {

	public function index() {
		CNavigation::setTitle(_('Create new ISP File'));
		IspView::showForm(isset($_REQUEST['domain']) ? $_REQUEST['domain'] : '');
	}

	public function submit() {
		$xml = file_get_contents('ISP/template.xml');

		$new_xml = preg_replace_callback('/@(.+?)@/', function($m) {

				$v = strtolower($m[1]);

				if (array_key_exists($v, $_POST)) {
					return htmlspecialchars($_POST[$v]);
				} else {
					return 'CANARD';
				}
			}, $xml);

		groaw($new_xml);
	}
}

?>
