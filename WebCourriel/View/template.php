<!DOCTYPE html>
<html>
<head>
	<title><?php echo CNavigation::getTitle();?> - WebCourriel</title>
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
			 CNavigation::getTitle(), "</h2>\n";

	$url_logout = CNavigation::generateUrlToApp('Session','logout',null);
	$text_logout = _('Logout');
	echo <<<END
	<nav><ul id="navigation">
		<li><a href="$url_logout">$text_logout</a></li>
	</ul></nav>
</header>

END;
}
?>
<div id="body">
<?php
echo $PAGE_CONTENT;
?>
</div>
</body>
</html>
