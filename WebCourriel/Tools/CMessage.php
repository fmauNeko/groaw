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

		$_SESSION['CMessage_list'][] = $this;
	}

	public function show()
	{
		echo "\t<li>$this->message</li>\n";
	}

	public static function showMessages()
	{
		if (isset($_SESSION['CMessage_list']))
		{
			$t = &$_SESSION['CMessage_list'];

			if (count($t) > 0)
			{
				echo "<ul class=\"CMessage\">\n";
				
				do
				{
					array_pop($t)->show();
				} while (count($t) > 0);

				echo "</ul>\n";
			}
		}
	}
}
?>
