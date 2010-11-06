<?php
class CImap
{
	protected static $jeton_imap = null;

	protected static $mail;
	protected static $mdp;
	protected static $boite;

	public static function declarerIdentite($mail, $mdp, $boite)
	{
		self::$mail = $mail;
		self::$mdp = $mdp;
		self::$boite = $boite;
	}

	public static function authentification()
	{
		self::$jeton_imap = imap_open(SERVEUR_IMAP.self::$boite, self::$mail, self::$mdp);
	}

	public static function deconnexion()
	{
		if (self::$jeton_imap !== null)
		{
			imap_close(self::$jeton_imap);
			self::$jeton_imap = null;
		}
	}

	public static function __callStatic ($nom, $arguments)
	{
		$fonction = 'imap_'.$nom;
		
		if (function_exists($fonction))
		{
			if (self::$jeton_imap === null)
			{
				self::authentification();
			}

			return call_user_func_array($fonction,
					array_merge(
						(array) self::$jeton_imap,
						$arguments));
		}
		else
		{
			throw new CException("Fonction imap n'existant pas.");
		}
	}
}
?>
