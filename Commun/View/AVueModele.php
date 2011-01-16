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

	public function traduirePeriodeDate($periode, $semaine = null)
	{
		groaw($periode);
		switch($periode)
		{
			case -1:
				return 'dans le futur';
			case 0:
				return 'ce soir';
			case 1:
				return 'en fin d\'après midi';
			case 2:
				return 'cette après midi';
			case 3:
				return 'vers midi';
			case 4:
				return 'ce matin';
			case 5:
				return 'tôt ce matin';
			case 6:
				return 'dans la nuit';
			case 7:
				return 'hier';
			case 8:
				return ($semaine) ? $semaine : 'cette semaine';
			default:
				return 'plus d\'une semaine';
		}
	}

	public function formaterDate($date)
	{
		if (!is_array($date))
		{
			$date = $this->repererDate($date);
		}

		$periode = $date[0];

		if ($periode < 0 || $periode > 8)
		{
			$format = FORMAT_DATE_NORMAL;
		}
		elseif ($periode == 8)
		{
			$format = FORMAT_DATE_SEMAINE;
		}
		else
		{
			$format = FORMAT_DATE_JOUR;
		}

		$format = $this->traduirePeriodeDate($periode).' '.$format;

		return strftime($format, $date[1]);
	}

	public function repererDate($date)
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

		// On commence à la fin de minuit
		$heure_c = mktime(23, 59, 59, $an['mon'], $an['mday'], $an['year']);

		$heures = array(
			23400, // 1 À partir de 18h30
			36000, // 2 À partir de 14h
			45000, // 3 À partir de 11h30
			57600, // 4 À partir de 8h00
			68400, // 5 À partir de 5h00
			86400, // 6 À partir de 0h0
			172800,// 7 Hier
			604800,// 8 Cette semaine
			0
		);
		
		// Si la date n'est pas dans le futur
		if ($t <= $n)
		{
			$i;
			$heure = $heure_c;
			for ($i = 0; $t < $heure && $i < 9 ; ++$i)
			{
				$heure = $heure_c - $heures[$i];
			}
			return array($i, $t, ($i === 8) ? strftime('%A', $t) : null);
		}

		// Si l'on est dans le futur
		return array(-1, $t, null);
	}

}
?>
