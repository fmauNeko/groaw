<?php

class Session {

	public function index() {
		$this->login();
	}

	public function login() {
		if (CNavigation::post())
		{
			try
			{
				CImap::declareIdentity($_POST['email_webcourriel'], $_POST['password_webcourriel'], 'INBOX');
				CImap::authentification();
				
				$_SESSION['email'] = $_POST['email_webcourriel'];
				$_SESSION['secret_password'] = $_POST['password_webcourriel'];

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
