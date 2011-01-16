<?php

class CHead
{
	public static $css = array();
	public static $js = array();

	public static function ajouterCss($n_css)
	{
		self::$css[] = $n_css;
	}
	
	public static function ajouterJs($n_js)
	{
		self::$js[] = $n_js;
	}
}
?>
