<?

//singleton for reaading app/conf/settings.php
class settings {

    private $conf = array();

    function &getinstance() {
        static $instance = array();
        if (!$instance) {
            $instance[0] = & new settings();
            if (file_exists('app/conf/settings.php'))
                require_once('app/conf/settings.php');
            else {
                echo "settings.php not included";
								return null;
						}
            $instance[0]->conf = $siteconf;
        }
        return $instance[0];
    }

    function get($key = false) {
        if (!$key)
            return $key;
        $self = &settings::getinstance();
        if (empty($self->conf))
            return "";
        if (!array_key_exists($key, $self->conf))
            return "";
        return $self->conf[$key];
    }
}?>
