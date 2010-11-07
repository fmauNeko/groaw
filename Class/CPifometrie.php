<?php
class CPifometrie
{
	// Cette fonction renvoi un texte aproximatif
	public static function nbMailsBoites($nombre, $verbe, $phrase_max)
	{
		if ($nombre ===0)
		{
			return "Vous n'avez <strong>aucun</strong> courriel.";
		}
		else if ($nombre === 1)
		{
			return "Vous avez <strong>juste un</strong> courriel à $verbe.";
		}
		else if ($nombre < 4)
		{
			return "Vous devez $verbe <strong>quelques</strong> courriels.";
		}
		else if ($nombre < 10)
		{
			return "Vous devez $verbe <strong>un peu</strong> de courriels.";
		}
		else if ($nombre < 42)
		{
			return "Vous avez <strong>pas mal</strong> de courriels à $verbe.";
		}
		else if ($nombre < 100)
		{
			return "Vous avez <strong>beaucoup</strong> de courriels à $verbe.";
		}

		return $phrase_max;
	}
}
?>
