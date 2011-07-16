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

	$url_logout = CNavigation::generateUrlToApp('Session','logout',null);
	$text_logout = _('Logout');
	echo <<<END
	<nav><ul id="navigation">
		<li><a href="$url_logout">$text_logout</a></li>
	</ul></nav>
</header>

END;
}

if (DEBUG && isset($groaw_array)) {

        echo "\n<pre class=\"groaw\">";

		$c_groaw_array = count($groaw_array);

		for($i = 0; $i < $c_groaw_array; ++$i) {
			$groaw = $groaw_array[$i];
			$groaw ? print_r($groaw) : var_dump($groaw);
			echo (($i < $c_groaw_array - 1) ? "<hr/>" : '' );
		}
        echo "</pre>\n";
}
?>
<div id="body">
<?php
echo $PAGE_CONTENT;
?>
</div>
</body>
</html>
