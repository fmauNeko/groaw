<?php
class SessionView {
	public function __construct() {
		$label_mail = _('Email');
		$label_password = _('Password');
		$text_submit = _('Login');

		global $CONNECTION_FAIL;
		if (isset($CONNECTION_FAIL)) {
			$class = 'class="connection_error" ';
			echo '<div class="exception">',$CONNECTION_FAIL,'</div>';
		}
		else {
			$class = '';
		}


		$url = CNavigation::generateUrlToApp('Session', 'login', null);
		echo <<<END
<form action="$url" name="login" method="post" $class>
	<p>
		<label for="input_mail">$label_mail</label>
		<input name="email_webcourriel" id="input_mail" type="email"
			autofocus required />
	</p>
	<p>
		<label for="input_pass">$label_password</label>
		<input name="password_webcourriel" id="input_pass" type="password"
			required />
	</p>
	<p>
		<input type="submit" value="$text_submit" />
	</p>
</form>	
END;
	}
}
?>
