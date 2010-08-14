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
                $elements = imap_mime_header_decode($text);
                for ($i=0; $i<count($elements); $i++) {
                        $output .= iconv($elements[$i]->charset, "UTF-8", $elements[$i]->text);
                }

                return $output;
        }
}
?>
