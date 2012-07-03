<?

class lib_router_default {

    var $aurl = '';

    function __construct() {
        
    }

    public function seturl($aurl = '') {
        $this->url = substr(preg_replace("/\?.*$/", "", $aurl), 1);
        $this->url = preg_replace("/\/+/", "/", $this->url);
    }

}

?>
