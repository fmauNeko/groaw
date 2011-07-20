<?php

class RedactionView {

	public function showForm() {

		global $ROOT_PATH;

		CHead::addJS('jquery-1.6.2.min');
		CHead::addJS('jquery-1.6.2.min');
		CNavigation::setTitle(_('New mail'));
		$label_to = _('To');
		$label_subject = _('Subject');
		$text_submit = _('Send');
		$url_submit = CNavigation::generateUrlToApp('Redaction', 'submit');
		$url_preview = CNavigation::generateUrlToApp('Redaction', 'preview');

		echo <<<END
<form action="$url_submit" name="redaction_form" method="post" id="redaction_form">
	<p>
		<label for="input_to">$label_to</label>
		<input name="to" id="input_to" type="text" autofocus required />
	</p>
	<p>
		<label for="input_subject">$label_subject</label>
		<input name="subject" id="input_subject" type="text" />
	</p>
	<div id="textarea_mail_body" class="simple_text">
		<script type="text/javascript" src="$ROOT_PATH/Tools/markitup/jquery.markitup.js"></script>
		<script type="text/javascript" src="$ROOT_PATH/Tools/markitup/sets/markdown/set.js"></script>
		<link rel="stylesheet" type="text/css" href="$ROOT_PATH/Tools/markitup/skins/markitup/style.css" />
		<link rel="stylesheet" type="text/css" href="$ROOT_PATH/Tools/markitup/sets/markdown/style.css" />
		<script type="text/javascript">var URL_PREVIEW="$url_preview";</script>
		
		<textarea name="body" id="mail_body"></textarea>
	</div>
	<input type="submit" value="$text_submit" id="canard" />
</form>	
END;
	}

	public function showPreview($data) {
		global $ROOT_PATH;
		
		echo <<<END
<html>
<head>
	<title>Preview</title>
	<link href="$ROOT_PATH/Css/application.css" media="screen" rel="Stylesheet" type="text/css" />
	<style type="text/css">
	body { padding:1em;}
	</style>
</head>
<body>
$data
</body>
</html>

END;
	}
}

?>
