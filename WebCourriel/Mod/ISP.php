<?php
class ISP
{
	public $domain;
	protected $xml;
	protected $tree;

	public function __construct($domain) {
		$this->domain = $domain;
	}

	public function loadFile() {
		$filename = "ISP/$this->domain.xml";

		if (file_exists($filename)) {
			$this->xml = file_get_contents($filename);
			return true;
		}
		
		$url = 'https://live.mozillamessaging.com/autoconfig/v1.1/'.$this->domain;

		try {
			$this->xml = file_get_contents($url, false, NULL, 0, 32768);
			
			if (file_put_contents($filename, $this->xml) === false) {
				new CMessage(_('ISP directory is not writable. Mozilla ISPDB is called each time.'));
			}

			return true;
		// On fait tourner les serviettes
		} catch (ErrorException $e) {}

		return false;
	}

	public function parseFile() {
		$this->tree = new SimpleXMLElement($this->xml);
	}

	public function getImapInfos() {
		$infos = $this->tree->xpath('emailProvider/incomingServer[@type=\'imap\']');

		if (is_array($infos)) {
			return $infos[0];
		}

		return false;
	}

	public static function createFile($data) {
		$xml = file_get_contents('ISP/template.xml');

		return preg_replace_callback('/@(.+?)@/', function($m) use($data) {

				$v = strtolower($m[1]);

				if (array_key_exists($v, $data)) {
					return htmlspecialchars($data[$v]);
				} else {
					return 'CANARD';
				}
			}, $xml);
	}
}
?>
