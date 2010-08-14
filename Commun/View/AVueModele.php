<?php

abstract class AVueModele
{
	protected $modele;
	
	public function __construct($modele)
	{
		$this->modele = $modele;
	}

	public function mime_to_utf8($input)
	{
		$output = "";

		$elements = imap_mime_header_decode($input);

		$nb_elements = count($elements);
		for ($i=0; $i<$nb_elements; ++$i)
		{
			$charset = $elements[$i]->charset;
			$text = $elements[$i]->text;
			
			if ($charset !== 'default')
			{
				$output .= iconv($charset, "UTF-8", $text);
			}
			else
			{
				$output .= $text;
			}
		}

		return $output;
	}
}
?>
