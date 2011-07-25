<?php
class CImap
{
	protected static $token = null;

	protected static $mail;
	protected static $login;
	protected static $password;
	protected static $box;

	protected static $cache_id;

	protected static $cached_functions = array('body', 'bodystruct', 'status','num_msg','getmailboxes', 'fetchstructure', 'fetchheader', 'fetch_overview', 'fetchbody', 'sort'); 

	public static function setServer($infos) {
		$hostname = strtr($infos->hostname, '{}:', '---');
		groaw($hostname);
		$port = intVal($infos->port);

		$s = '{'.$hostname.':'.$port.'/imap';

		switch (strtoupper($infos->socketType)) {
			case 'SSL':
				$s .= '/ssl';

				if (!VALIDATE_CERT) {
					$s.= '/novalidate-cert';
				}

				break;
			case 'STARTTLS':
				$s .= '/tls';
				break;
		}
		$s .= '}';

		$_SESSION['imap_server'] = $s;

	}

	public static function declareIdentity($mail, $login, $password, $box)
	{
		self::$mail = $mail;
		self::$login = $login;
		self::$password = $password;
		self::$box = $box;

		// The idea is just to don't show the mail in the arborescence
		// md5 is fast for that
		self::$cache_id = md5($mail.$box);
	}

	public static function authentification()
	{
		self::$token = imap_open($_SESSION['imap_server'].self::$box, self::$login, self::$password);
	}

	public static function logout()
	{
		if (self::$token !== null)
		{
			imap_close(self::$token);
			self::$token = null;
		}
	}

	public static function __callStatic ($name, $args)
	{
		if (strncmp('NC_', $name, 3) === 0) {
			$name = substr($name, 3);
			$cached_function = false;
		} else {
			$cached_function = in_array($name, self::$cached_functions);
		}

		$function = 'imap_'.$name;

		if (!function_exists($function)) {
			throw new exception(sprintf(_("The imap function %s does'nt exist."), $function));
		}


		if ($cached_function) {

			$cache_file = 'Cache/'.self::$cache_id.'-'.$name.'-'.md5(serialize($args));

			if (file_exists($cache_file) && time() - filemtime($cache_file) < CACHE_LENGTH)
			{
				$data = file_get_contents($cache_file);

				if ($data === false) {
					groaw(_('Cache data is corrupted'));
				}
				else {
					return unserialize($data);
				}
			}
		}

		if (self::$token === null)
		{
			self::authentification();
		}

		//groaw("CALL: $name");
		$function_value = call_user_func_array($function,
				array_merge(
					(array) self::$token,
					$args));

		if ($cached_function) {
			$data = serialize($function_value);

			if (!file_put_contents($cache_file, $data)) {
				groaw(_('Unable to write cache data. WebCourriel may be slow.'));
			}
		}

		return $function_value;
	}

	public static function getAllBoxes() {
		return CImap::getmailboxes($_SESSION['imap_server'], '*');
	}

	public static function getServer() {
		return $_SESSION['imap_server'];
	}

}
?>
