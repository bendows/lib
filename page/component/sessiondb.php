<?

class lib_component_sessiondb extends lib_component_component {

    private $gc_maxlifetime = 0;

    function __construct($data) {
        parent::__construct($data);
        $classname = "lib_model_{$this->model}";
        $this->model = & new $classname();
        ini_set('allow_url_fopen', 0);
        ini_set('allow_url_include', 0);
        ini_set('session.entropy_length', 8);
        # 75% default garbage collection (expires sessions)
        ini_set("session.gc_probability", 75);
        ini_set("session.gc_divisor", 100);
        // 0=close session when browser close
        // n = ammount of seconds
        ini_set('session.cookie_lifetime', 0);
        #Do not automatically start sessions
        ini_set('session.auto_start', '0');
        #Use cookies
        ini_set('session.use_cookies', '1');
        #Use only cookies
        ini_set('session.use_only_cookies', '1');
        ini_set('session.hash_function', 1);
        ini_set('session.hash_bits_per_character', 6);
        ini_set('session.name', $this->session_name);
        session_name($this->session_name);
        //$this->cookie_lifetime=900 //15 minutes
        ini_set('session.gc_maxlifetime', $this->cookie_lifetime);
        $this->gc_maxlifetime = ini_get('session.gc_maxlifetime');
        // Sessions last for a day unless otherwise specified.
        if (!$this->gc_maxlifetime) {
            die('this is neverland, the impossible');
            $this->gc_maxlifetime = 900;
        }
        if (!session_set_save_handler(
            array(&$this, 'open'), array(&$this, 'close'), array(&$this, 'read'), array(&$this, 'write'), array(&$this, 'destroy'), array(&$this, 'gc'))) {
            die("Error session_set_save_handler");
        }
        $tt = $_SERVER['HTTP_USER_AGENT'];
        $tt.= ' ASSrtASdfGtewr53 ';
        $tt = preg_replace("/\ /", "Y", $tt);
        $tt = md5($tt);
        session_start();
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id();
            $_SESSION['initiated'] = TRUE;
        }
        if (!isset($_SESSION['userag'])) {
            $_SESSION['userag'] = $tt;
        } else {
            if ((string) $tt !== (string) $_SESSION['userag']) {
                session_regenerate_id();
                $_SESSION['euiid'] = "---";
                ;
                $_SESSION['handle'] = "--";
            }
        }
    }

    function open($save_path, $session_name) {
        $this->model->connect($this->mysqlfile);
        if (!$this->model->ok())
            echo "cannot open session";
        return ($this->model->ok());
    }

    function close() {
        return true;
    }

    function read($id) {
        $delta = time() - $this->gc_maxlifetime;
        $sql = "select data from {$this->table_name} where (id = '%s') and (access >= %d)";
        if (false !== ($ar = $this->model->row($sql, array($id, $delta)))) {
            return $ar['data'];
        }
        return '';
    }

    function write($id, $data) {
        $data = mysql_real_escape_string($data);
        if ($this->model->execute("replace into {$this->table_name} (id, ip, data, access) values ('" .
          "$id', '" . $_SERVER['REMOTE_ADDR'] . "', '$data', " . time() . ")")) {
            return true;
        }
        echo "Session write error";
        return false;
    }

    function destroy($id) {
        if ($this->model->execute("delete from session where id = '$id'")) {
            return true;
        }
        return false;
    }

    function gc($maxlifetime) {
        $delta = time() - $maxlifetime;
        $sql = "delete from session where (access <= $delta)";
        if ($this->model->execute($sql)) {
            return true;
        }
        return false;
    }

}
?>
