<?php

class CHead
{
	public static $css = array();
	public static $js = array();

	public static function addCSS($n_css)
	{
		self::$css[] = $n_css;
	}
	
	public static function addJS($n_js)
	{
		self::$js[] = $n_js;
	}
}
?>
