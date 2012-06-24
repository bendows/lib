<?

class lib_component_sessiondb extends lib_component_component {

    private $gc_maxlifetime = 0;

    function __construct($data) {
        parent::__construct($data);
        $classname = "lib_model_{$this->model}";
        $this->model = & new $classname();
        ini_set('allow_url_fopen', 0);
        ini_set('allow_url_include', 0);
        ini_set('session.auto_start', '0');
        # 75% default garbage collection (expires sessions)
        ini_set("session.gc_probability", 1);
        ini_set("session.gc_divisor", 100);
        ini_set('session.gc_maxlifetime', $this->cookie_lifetime);
        ini_set('session.referer_check', '');
        ini_set('session.entropy_length', 16);
        ini_set('session.cookie_lifetime', 0);
        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');
				ini_set('session.use_trans_sid', 0);
        ini_set('session.hash_function', 1);
        ini_set('session.hash_bits_per_character', 5);
        ini_set('session.name', $this->session_name);
        session_name($this->session_name);
        //$this->cookie_lifetime=900 //15 minutes
        $this->gc_maxlifetime = ini_get('session.gc_maxlifetime');
        // Sessions last for a day unless otherwise specified.
        if (!$this->gc_maxlifetime) {
            die('this is neverland, the impossible');
            $this->gc_maxlifetime = 900;
        }
        if (!session_set_save_handler(
								array(&$this, 'open'), 
								array(&$this, 'close'), 
								array(&$this, 'read'), 
								array(&$this, 'write'), 
								array(&$this, 'destroy'), 
								array(&$this, 'gc'))) {
            			die("Error session_set_save_handler");
        				}
        $tt = $_SERVER['HTTP_USER_AGENT'];
        $tt.= ' ASSrtASdfGtewr53 ';
        $tt = preg_replace("/\ /", "Y", $tt);
        $tt = md5($tt);
        session_start();
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id();
            $_SESSION['userag'] = $tt;
            $_SESSION['initiated'] = true;
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
      //if ((string) $tt !== (string) $_SESSION['userag'])
			//	return '';
			$delta = time()-$this->gc_maxlifetime;
      $k = $this->model->delete($this->table_name, "(http_host = '%s') and (updated < %d)", array($_SERVER['HTTP_HOST'], $delta));
      $sql = "select data from {$this->table_name} where (sessid = '%s')";
      if (false === ($ar = $this->model->row($sql, array($id))))
				return '';

				if (empty($ar))
					return '';

        return $ar['data'];
    }

    function write($id, $data) {
        $sql = "select data from {$this->table_name} where (sessid = '%s')";
        if (false === ($ar = $this->model->row($sql, array($id)))) {
					echo "session write error 1";
					return false;
				}
        if (empty($ar)) {
					$r = $this->model->insert ($this->table_name, 
							array('sessid', 'sessname', 'http_host', 'ipaddr', 'cipaddr', 'data', 'created', 'updated'), 
							array($id, ini_get('session.name'), $_SERVER['HTTP_HOST'], $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_ADDR'], $data, time(), time())
					);
					if ($r < 1) {
						echo "session write error 2";
						return false;
					}
					return true;
				}
				//Session already exists in db, just update some fields
				$r = $this->model->update ($this->table_name, array('cipaddr', 'data', 'updated'), array($_SERVER['REMOTE_ADDR'], $data, time()), "sessid = '$id'");
				if ((int) $r <= 0) {
					echo "session write error 3";
        	return false;
				}
				return true;
    }

    function destroy($id) {
       $k = $this->model->delete($this->table_name, "sessid = '%s'", array($id));
			 if ((int) $k > 0)
					return true;
        return false;
    }

    function gc($maxlifetime) {
		  	return true;
    }
}
?>
