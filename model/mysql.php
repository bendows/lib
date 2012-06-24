<?

class lib_model_mysql {

    private $fmsg = "[empty]";
    private $fmmsg = '[empty]';
    public $fno = '';
    private $fields = array();
    private $fsql = '';
    private $cols = array();
    private $vals = array();
    private $con = false;

    public function connect($mysqlconf = false) {
        $this->msg = "";
        $this->emsg = "nothing done to mysql";
        if (!$mysqlconf)
            return false;
        if (is_scalar($mysqlconf)) {
            $this->emsg = "$mysqlconf did not exist as a file and was not included";
            if (file_exists("$mysqlconf")) {
                require("$mysqlconf");
                $this->emsg = "$mysqlconf did exist as a file and was included";
            } else
                return false;
        }
        if (is_array($mysqlconf))
            $lar = $mysqlconf;
        $this->ffhost = $lar['dbhost'];
        $this->ffdb = $lar['dbname'];
        $this->ffuid = $lar['dbuser'];
        $this->ffpwd = $lar['dbpwd'];
        if (!$this->con = @mysql_connect($this->ffhost, $this->ffuid, $this->ffpwd, TRUE)) {
            $this->emsg = "[".@mysql_errno($this->con)."]". mysql_error($this->con);
            return false;
        }
        if (!$this->selectdb()) {
            $this->emsg = "[".@mysql_errno($this->con)."]". mysql_error($this->con);
            return false;
        }
        $this->msg = 'Connected [' . join('', mysql_fetch_row(mysql_query('SELECT NOW(), VERSION();', $this->con))) . "]";
        return (boolean) true;
    }

    public function disconnect() {
        
    }

    final public function ok() {
        return (boolean) (is_array(@mysql_fetch_row(mysql_query('SELECT VERSION();', $this->con))));
    }

    final public function selectdb() {
        $this->msg = $this->ffdb;
        $this->emsg = "";
        if (!@mysql_select_db($this->ffdb, $this->con)) {
            $this->emsg = "[".@mysql_errno($this->con)."]". mysql_error($this->con);
            return false;
        }
        return true;
    }

    final private function executesql($sql) {
        $this->msg = $sql;
        $this->emsg = "";
        if (!@mysql_query($sql, $this->con)) {
            $this->emsg = "[".@mysql_errno($this->con)."]". mysql_error($this->con);
            return false;
        }
        return true;
    }

    final private function setcolsvals($afields = array(), $avalues = array()) {
				$this->msg = "setcols";
				$this->emsg = "setcols error";
        $this->cols = array();
        $this->vals = array();
        if (!is_array($afields))
            return false;
        if (empty($afields))
            return false;
        if (!is_array($avalues))
            return false;
        if (empty($avalues))
            return false;
				$this->emsg = "";
        foreach ($afields as $i => $fieldname) {
            if (!array_key_exists($i, $avalues))
                continue;
            $this->cols[] = $fieldname;
            $s = "";
            $s = mysql_real_escape_string($avalues[$i]);
            $this->vals[] = "'$s'";
        }
        return (boolean) true;
    }

    final private function insert_update_delete_prepare ($afields=array(), $avalues=array()) {
				if (!$this->selectdb()) 
            return (boolean) false;
        if (!$this->setcolsvals((array) $afields, (array) $avalues))
            return (boolean) false;
				return (bool) true;
	  }

    public function update($atablename = '', $afields = array(), $avalues = array(), $where = '') {
				$where = trim($where);
				if (true !== (bool) $this->insert_update_delete_prepare($afields, $avalues))
					return -1;
				if (empty($where)) {
					$this->emsg = "empty where clause";
					return -100;
				}
        $tmp = array();
        foreach ($this->cols as $index => &$col)
            $tmp[] = "$col=" . $this->vals[$index];
        $s = "update " . $atablename . " set " . implode(',', $tmp) . " where ($where)";
				$this->msg = $s;
				if (!$this->executesql($s))
            return -2;
        return mysql_affected_rows($this->con);
    }

    public function insert($atablename = '', $afields = array(), $avalues = array()) {
				if (true !== (bool) $this->insert_update_delete_prepare($afields, $avalues))
					return -1;
        $s = "insert into $atablename (" . implode(",", $this->cols) . ") values (" .  implode(",", $this->vals) . ")";
        if (!$this->executesql($s))
            return -2;
        $nid = mysql_insert_id($this->con);
        return (int) $nid;
    }

    public function delete($atablename = '', $where = '', $values=array()) {
				if (!$this->selectdb())
            return -1;
				$this->emsg = "where tablename values or fields mismatch";
				$where = trim($where);
				if (empty($where))
            return -2;
				$atablename=trim($atablename);	
				if (empty($atablename))
            return -3;
				if (empty($values))
						return -4;
				$this->emsg = "mysql escape error";
        foreach ($values as &$value)
            if (false === ($value = mysql_real_escape_string($value)))
                return false;
        $s = "delete from $atablename where ".vsprintf($where, $values);
        if (!$this->executesql($s))
            return -5;
        $r = mysql_affected_rows($this->con);
        return (int) $r;
    }

    public function row($s = '', $values = array()) {
				$this->msg = "function row";
				$this->emsg = "";
				$s = trim($s);
				if (empty($s)) {
					$this->emsg = "empty sql string";
					return (bool) false;
				}
				if (empty($values)){
					$this->emsg = "empty values";
					return (bool) false;
				}

			/*foreach ($values as &$value) {
         if (preg_match("/^([A-Z]|[0-9]|\.|,|\+|\@|\-|\_|~|\.)+$/i", $value))
         continue;
      return false;
            }
		 */
        if (!$this->selectdb())
						return (bool) false;
				$this->emsg = "mysql escape error";
        foreach ($values as &$value) {
            if (false === ($value = mysql_real_escape_string($value, $this->con))) {
                return false;
						}
        }
				$this->emsg = "";
        $s = vsprintf($s, $values);
        $this->msg = $s;
        if (!$arow = mysql_query($s, $this->con)) {
			      $this->emsg = "[".@mysql_errno($this->con)."]". mysql_error($this->con);
            return (bool) false;
        }
        if ((int) mysql_num_rows($arow) === 1) {
            $er = (array) mysql_fetch_assoc($arow);
            return (array) $er;
        }
        if ((int) mysql_num_rows($arow) > 1) {
					$this->emsg = "to many rows returned";
        	return (boolean) false;
				}
				return array();
    }

    public function rows($s = '', $values = array(), $key = 'id') {
        if (empty($values))
            $this->fsql = $s;
        else {
            if (!is_array($values)) {
                return false;
            }
            foreach ($values as &$value) {
                if (!isuid($value))
                    continue;
                return false;
            }
            $this->fsql = vsprintf($s, $values);
        }
        if (!$this->selectdb()) {
            return false;
        }
        if (!$myresult = mysql_query($this->fsql, $this->con)) {
            $this->fmsg = "Could not execute qry";
            $this->fmmsg = mysql_error($this->con);
            $this->fno = mysql_errno();
            return false;
        }
        $link = array();
        $this->fmsg = "[" . (string) mysql_num_rows($myresult) . "] rows found";
        $this->fmmsg = "[" . (string) mysql_num_rows($myresult) . "] rows found";
        if (!empty($key)) {
            while ($row = mysql_fetch_assoc($myresult)) {
                $link[$row[$key]] = $row;
            }
            return (array) $link;
        }
        $i = 0;
        while ($row = mysql_fetch_assoc($myresult)) {
            $link[] = $row;
        }
        return (array) $link;
    }

    public function rowsa($s = '', $values = array(), $key = 'id') {
        if (empty($values)) {
            $this->fsql = $s;
        } else {
            if (!is_array($values)) {
                return false;
            }
            foreach ($values as &$value) {
                if (isuid($value))
                    continue;
                return false;
            }
            $this->fsql = vsprintf($s, $values);
        }
        if (!$this->selectdb()) {
            return false;
        }
        if (!$myresult = mysql_query($this->fsql, $this->con)) {
            $this->fmsg = "Could not execute qry";
            $this->fmmsg = mysql_error($this->con);
            $this->fno = mysql_errno();
            return false;
        }
        $link = array();
        $this->fmsg = "[" . (string) mysql_num_rows($myresult) . "] rows found";
        $this->fmmsg = "[" . (string) mysql_num_rows($myresult) . "] rows found";
        if (!empty($key)) {
            while ($row = mysql_fetch_assoc($myresult)) {
                $link[$row[$key]][$row['id']] = $row;
            }
            return (array) $link;
        }
    }

}
?>
