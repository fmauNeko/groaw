<?php

class CMessage
{
	public $message;

	public function __construct($message)
	{
		$this->message = $message;

		if (!isset($_SESSION['CMessage_list']))
		{
			$_SESSION['CMessage_list'] = array();
		}

		array_push($_SESSION['CMessage_list'], $this);
	}

	public function afficher()
	{
		echo "\t<li>$this->message</li>\n";
	}

	public static function afficherCMessages()
	{
		if (isset($_SESSION['CMessage_list']))
		{
			$t = &$_SESSION['CMessage_list'];

			if (count($t) > 0)
			{
				echo "<ul class=\"CMessage\">\n";
				
				do
				{
					array_pop($t)->afficher();
				} while (count($t) > 0);

				echo "</ul>\n";
			}
		}
	}
}
