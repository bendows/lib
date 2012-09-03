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
								$docroot = dirname(__FILE__)."/..";
                $tmp = <<<EOT

Create $docroot/app/conf/settings.php with the following content:

<?
    \$siteconf=array(
    "maintenance"=>"/maintain.html",
    "logfile"=>"html/messages.log",
    "mysqlconf"=>"app/conf/sitedb.php",
    "requestcomponent"=>array('request'=> array('POST', 'GET', 'COOKIE', 'FILES', 'SERVER')),
    "sessioninfo"=>array(
    'session_name'=>'your_session_name',
    //max lifetime of a session between page clicks
    'cookie_lifetime'=>86400,
    'model'=>'mysql',
    'table_name'=>'php_session',
    'mysqlfile'=>'app/conf/sessiondb.php'
    )
 );
?>

EOT;
                $tmp = htmlentities($tmp);
                echo "<pre>$tmp</pre>";
                return $instance[0];
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

}
?>
