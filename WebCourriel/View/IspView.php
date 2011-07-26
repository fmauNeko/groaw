<?php
class IspView {

	public static function showForm($domain = '') {

		$hdomain = htmlspecialchars($domain);

		$legend_general = _('General informations');
		$label_domain = _('Domain');

		$legend_imap = _('IMAP');
		$label_imap_hostname = _('Hostname');
		$label_imap_port = _('Port');
		$label_imap_sockettype = _('Socket type');
		$label_imap_username = _('Username');
		$radio_localpart = _('Localpart (part before @)');
		$radio_adress = _('Email address');

		$legend_smtp = _('SMTP');
		$label_smtp_hostname = _('Hostname');
		$label_smtp_port = _('Port');
		$label_smtp_username = _('Username');
		$label_smtp_sockettype = _('Socket type');
		
		$url_submit = CNavigation::generateUrlToApp('IspFactory', 'submit');
		$text_submit = _('Create ISP');

		echo <<<END
<form action="$url_submit" name="isp_form" method="post" id="isp_form">
	<fieldset>
		<legend>$legend_general</legend>
		<p>
			<label for="input_domain">$label_domain</label>
			<input name="domain" id="input_domain" type="text" required value="$hdomain" />
		</p>
	</fieldset>
	<fieldset>
		<legend>$legend_imap</legend>
		<p>
			<label for="input_imap_hostname">$label_imap_hostname</label>
			<input name="imap_hostname" id="input_imap_hostname" type="text" value="$hdomain" required />
		</p>
		<p>
			<label for="input_imap_port">$label_imap_port</label>
			<input name="imap_port" id="input_imap_port" type="number" value="993" required />
		</p>

		<p>
			<label for="input_imap_sockettype">$label_imap_sockettype</label> <br/>
			<input type="radio" name="imap_sockettype" value="plain" id="input_imap_sockettype">Plain<br/>
			<input type="radio" name="imap_sockettype" value="ssl" checked>SSL<br/>
			<input type="radio" name="imap_sockettype" value="starttls">StartTLS<br/>
		</p>
		
		<p>
			<label for="input_imap_username">$label_imap_username</label> <br/>
			<input type="radio" name="imap_username" value="%EMAILLOCALPART%" id="input_imap_username">$radio_localpart<br/>
			<input type="radio" name="imap_username" value="%EMAILADDRESS%" checked>$radio_adress<br/>
		</p>
	</fieldset>
	<fieldset>
		<legend>$legend_smtp</legend>
		<p>
			<label for="input_smtp_hostname">$label_smtp_hostname</label>
			<input name="smtp_hostname" id="input_smtp_hostname" type="text" value="$hdomain" equired />
		</p>
		<p>
			<label for="input_smtp_port">$label_smtp_port</label>
			<input name="smtp_port" id="input_smtp_port" type="number" value="25" required />
		</p>

		<p>
			<label for="input_smtp_sockettype">$label_smtp_sockettype</label> <br/>
			<input type="radio" name="smtp_sockettype" value="plain" id="input_smtp_sockettype">Plain<br/>
			<input type="radio" name="smtp_sockettype" value="ssl" checked>SSL<br/>
			<input type="radio" name="smtp_sockettype" value="starttls">StartTLS<br/>
		</p>
		
		<p>
			<label for="input_smtp_username">$label_smtp_username</label> <br/>
			<input type="radio" name="smtp_username" value="%EMAILLOCALPART%" id="input_smtp_username">$radio_localpart<br/>
			<input type="radio" name="smtp_username" value="%EMAILADDRESS%" checked>$radio_adress<br/>
		</p>
	</fieldset>
	<input type="submit" value="$text_submit" />
</form>	
END;
	}
}
?>
