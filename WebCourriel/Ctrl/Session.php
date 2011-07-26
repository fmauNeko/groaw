<?php

define('NO_LOGIN_REQUIRED', true);
define('NO_HEADER_BAR', true);

class Session {

	public function index() {
		$this->login();
	}

	public function login() {
		if (CNavigation::post())
		{
			$email = $_POST['email_webcourriel'];

			// The last @ is the separation
			$n_a = strrpos($email,'@');

			if ($n_a === false && defined('DEFAULT_DOMAIN')) {
				$domain = DEFAULT_DOMAIN;
				$localPart = $email;
				$email .= '@'.$domain;
			} else {
				$localPart = substr($email, 0, $n_a);
				$domain = substr($email, $n_a+1);
			}

			if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				new CMessage(_('The email adress isn\'t valid.'));
				return;	
			};

			global $ACCEPT_ONLY_DOMAINS;
			if (isset($ACCEPT_ONLY_DOMAINS) && is_array($ACCEPT_ONLY_DOMAINS) && !in_array($domain, $ACCEPT_ONLY_DOMAINS)) {
				new CMessage(sprintf(_('The domain %s is not allowed.'), $domain));
				return;
			}

			$isp = new ISP($domain);

			if (!$isp->loadFile()) {
				new CMessage(sprintf(_('No ISP file for the domain %s.'), $domain));
				CNavigation::redirectToApp('IspFactory', 'index', array(
						'domain' => $domain	
							));
			}

			$isp->parseFile();

			$infosImap = $isp->getImapInfos();

			if (!$infosImap) {
				new CMessage(sprintf(_('No imap section for the domain %s.'), $domain));
				return;
			}

			// We want just the fist imap section
			CImap::setServer($infosImap);

			switch ($infosImap->username) {
				case '%EMAILLOCALPART%':
					$login = $localPart;
					break;
				case '%EMAILADDRESS%':
				default:
					$login = $email;
					break;
			}

			$password = $_POST['password_webcourriel'];

			switch (strtolower($infosImap->authentification)) {
				case 'password-cleartext':
				case 'plain':
					break;
				case 'password-encrypted':
				case 'secure':
				case 'none':
					// TODO
					new CMessage('WTF ?');
					break;
			}
			
			try
			{
				CImap::declareIdentity($email, $login, $password, 'INBOX');
				CImap::authentification();
				
				$_SESSION['email'] = $email;
				$_SESSION['login'] = $login;
				$_SESSION['password'] = $password;

				$_SESSION['logged'] = true;

				CNavigation::redirectToApp('Dashboard');
			}
			catch (ErrorException $e)
			{
				global $CONNECTION_FAIL;
				$CONNECTION_FAIL = sprintf(_("Unable to connect : %s"), $e->getMessage());
			}

		}
	
		unset($_SESSION['logged']);

		CNavigation::setTitle(_('Sign In'));
		CHead::addJS('sha1');
		new SessionView();
	}


	public function logout()
	{
		session_destroy();

		new CMessage(_('Successful logout'));

		CNavigation::redirectToApp(null, null, null);
	}
}

?>
