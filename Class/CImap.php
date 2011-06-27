<?php
class CImap
{
	protected static $token = null;

	protected static $mail;
	protected static $password;
	protected static $box;

	public static function declareIdentity($mail, $password, $box)
	{
		self::$mail = $mail;
		self::$password = $password;
		self::$box = $box;
	}

	public static function authentification()
	{
		self::$token = imap_open(IMAP_SERVER.self::$box, self::$mail, self::$password);
	}

	public static function logout()
	{
		if (self::$token !== null)
		{
			imap_close(self::$token);
			self::$token = null;
		}
	}

	public static function __callStatic ($nom, $arguments)
	{
		$fonction = 'imap_'.$nom;

		if (function_exists($fonction))
		{
			if (self::$token === null)
			{
				self::authentification();
			}

			return call_user_func_array($fonction,
					array_merge(
						(array) self::$token,
						$arguments));
		}
		else
		{
			throw new CException(sprintf(_("The imap function %s does'nt exist."), $nom));
		}
	}
	
}
?>
