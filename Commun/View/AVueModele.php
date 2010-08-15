<?php

abstract class AVueModele
{
	protected $modele;
	protected $maintenant;
	
	public function __construct($modele)
	{
		$this->modele = $modele;
	}

	public function mime_to_utf8($input)
	{
		$output = '';

		$elements = imap_mime_header_decode($input);

		$nb_elements = count($elements);
		for ($i=0; $i<$nb_elements; ++$i)
		{
			$charset = $elements[$i]->charset;
			$text = $elements[$i]->text;
			
			if ($charset !== 'default')
			{
				$output .= iconv($charset, 'UTF-8', $text);
			}
			else
			{
				$output .= $text;
			}
		}

		return $output;
	}

	public function utf7_to_utf8($texte)
	{
		return utf8_encode(imap_utf7_decode($texte));
	}

	public function formater_date_liste($date)
	{
		// Il est intéressant de noter que ça fonctionne du premier coup
		$t =  strtotime($date);

		// Inutile de demander la date au système 4000 fois…
		if (!isset($this->maintenant))
		{
			$this->maintenant = time();
		}

		$n = $this->maintenant;

		$an = getdate($n);

		$l_jour		= mktime(0, 0, 0, $an['mon'], $an['mday'], $an['year']);
		$l_semaine	= mktime(0, 0, 0, $an['mon'], $an['mday']-6, $an['year']);

		$format = FORMAT_DATE_NORMAL;

		// Si la date n'est pas dans le futur
		if ($t <= $n)
		{
			if ($t > $l_jour)
			{
				$format = FORMAT_DATE_JOUR;
			}
			else if ($t > $l_semaine)
			{
				$format = FORMAT_DATE_SEMAINE;
			}
		}
		else
		{
			$format = 'Futur : '.$format;
		}

		return strftime($format, $t);
	}

}
?>
