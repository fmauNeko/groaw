<!DOCTYPE html>
<html>
<head>
	<title><?php echo htmlspecialchars(CNavigation::getTitle()); ?> - WebCourriel</title>
<?php foreach (CHead::$css as $css)
{
	echo "\t<link href=\"$ROOT_PATH/Css/$css.css\" media=\"screen\" rel=\"Stylesheet\" type=\"text/css\" />\n";
}
foreach (CHead::$js as $js)
{
	echo "\t<script type=\"text/javascript\" src=\"$ROOT_PATH/Js/$js.js\"></script>\n";
}
?>
</head>
<body>
<?php

if (isset($_SESSION['logged']))
{
	echo "<header>\n\t",'<h2 id="title" ',
		 (isset($BOX_NAME) ? 'class="'.htmlspecialchars($BOX_NAME).'"' : ''),'>',
			 htmlspecialchars(CNavigation::getBodyTitle()), "</h2>\n";

	$url_redaction = CNavigation::generateUrlToApp('Redaction',null,null);
	$url_logout = CNavigation::generateUrlToApp('Session','logout',null);
	$text_logout = _('Logout');
	$text_redaction = _('New mail');
	echo <<<END
	<nav><ul id="navigation">
		<li><a href="$url_redaction">$text_redaction</a></li>
		<li><a href="$url_logout">$text_logout</a></li>
	</ul></nav>
</header>

END;
}

if (DEBUG) {
	showGroaw();
}
?>
<div id="body">
<?php
echo $PAGE_CONTENT;
?>
</div>
</body>
</html>
