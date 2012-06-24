<?

class lib_router_default {

    var $aurl = '';
		var $maintenance = '';
    function __construct() {
	     $this->maintenance = settings::get('maintenance');
    }

    public function seturl($aurl = '') {
        $this->url = substr(preg_replace("/\?.*$/", "", $aurl), 1);
        $this->url = preg_replace("/\/+/", "/", $this->url);
    }

}
?>
