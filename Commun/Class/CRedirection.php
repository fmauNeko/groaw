<?php
class CRedirection
{
	public function __construct($url)
	{
		$erreurs_imap = imap_errors();

		if ($erreurs_imap)
		{
			groaw($erreurs_imap);
		}
		else
		{
			// Ignore the already printed content
			ob_end_clean();
			
			// HTTP redirection
			header("Location:\t".$url);
			
			// With a link for be nice
			echo 'Move to: <a href="',htmlspecialchars($url),'">,', htmlspecialchars($url),'</a>.';
		}

		// A redirection is terminal
		exit();
	}
}
?>
