<?php

class Session {

	public function index() {
		$this->login();
	}

	public function login() {
		if (CNavigation::post())
		{
			$email = $_POST['email_webcourriel'];
			if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				new CMessage(_('The email adress isn\'t valid.'));
				return;	
			};

			// The last @ is the separation
			$n_a = strrpos($email,'@');

			$localPart = substr($email, 0, $n_a);
			$domain = substr($email, $n_a+1);

			$filename = "ISP/$domain.xml";

			if (file_exists($filename)) {
				$xml = file_get_contents($filename);
			} else {
				$url = 'https://live.mozillamessaging.com/autoconfig/v1.1/'.$domain;
				try {
					$xml = file_get_contents($url, false, NULL, 0, 32768);
					if (file_put_contents($filename, $xml) === false) {
						new CMessage(_('ISP directory is not writable. Mozilla ISPDB is called each time.'));
					}
				// On fait tourner les serviettes
				} catch (ErrorException $e) {}
			}

			if (!$xml) {
				new CMessage(sprintf(_('No ISP file for the domain %s.'), $domain));
				return;
			}

			$tree = new SimpleXMLElement($xml);

			$infosImap = $tree->xpath('emailProvider/incomingServer[@type=\'imap\']');

			if (!$infosImap) {
				new CMessage(sprintf(_('No imap section for the domain %s.'), $domain));
				return;
			}

			// We want just the fist imap section
			$infosImap = $infosImap[0];

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
