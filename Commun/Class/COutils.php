<?php
class COutils
{
	public static function toUtf8($charset, $texte)
	{
		try
		{
			return iconv($charset, 'UTF-8//TRANSLIT//IGNORE', $texte);
		}
		catch (Exception $e)
		{
			try
			{
				// La plupart des boulets utilisent de l'iso…
				return iconv('ISO-8859-15', 'UTF-8//TRANSLIT//IGNORE', $texte);
			}
			catch (Exception $e)
			{
				return $texte;
			}
		}
	}
}
?>
