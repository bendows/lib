<?
class lib_component_request extends lib_component_component {
	function __construct ($data, &$apage) {
		parent::__construct($data);
		foreach ($this->request as $val) {
			switch ($val) {
				case "COOKIE":
					$this->cookie = $_COOKIE;
					break;
				case "SERVER":
					$this->server = $_SERVER;

					$apage->here = $this->server['SCRIPT_URL'];
					$apage->remoteip = $this->remoteip();
					$apage->referer = $this->referer();
					$apage->hostname = $this->server['HTTP_HOST'];

					break;
				case "GET":
					$this->get = $_GET;
					break;
				case "POST":
					$this->post = $_POST;
					break;
				case "FILES":
					$this->files = $_FILES;
					break;
			}
		}
	}

	function referer() {
		if ($this->server['HTTP_HOST'] != null) {
			$sessHost = $this->server['HTTP_HOST'];
		}

		if ($this->server['HTTP_X_FORWARDED_HOST'] != null) {
			$sessHost = $this->server['HTTP_X_FORWARDED_HOST'];
		}
		return trim(preg_replace('/(?:\:.*)/', '', $sessHost));
	}

	function remoteip($safe = true) {
		if (!$safe && $this->server['HTTP_X_FORWARDED_FOR'] != null) {
			$ipaddr = preg_replace('/(?:,.*)/', '', $this->server['HTTP_X_FORWARDED_FOR']);
		} else {
			if ($this->server['HTTP_CLIENT_IP'] != null) {
				$ipaddr = $this->server['HTTP_CLIENT_IP'];
			} else {
				$ipaddr = $this->server['REMOTE_ADDR'];
			}
		}

		if ($this->server['HTTP_CLIENTADDRESS'] != null) {
			$tmpipaddr = $this->server['HTTP_CLIENTADDRESS'];

			if (!empty($tmpipaddr)) {
				$ipaddr = preg_replace('/(?:,.*)/', '', $tmpipaddr);
			}
		}
		return trim($ipaddr);
	}

	function isget () {
		return ($this->server['REQUEST_METHOD'] === 'GET');
	}

	function ispost () {
		return ($this->server['REQUEST_METHOD'] === 'POST');
	}
}
?>
