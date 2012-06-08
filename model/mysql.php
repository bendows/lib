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

    final public function info($level = 0) {
        switch ($level) {      
            case 0:
                $info = array(
                    dbname => $this->ffdb,
                    dbuser => $this->ffuid,
                    msg => $this->fmsg
                );
                break;           
            case 1:
                $info = array(
                    'dbhost' => $this->ffhost,
                    'dbname' => $this->ffdb,
                    'dbuser' => $this->ffuid,
                    'sql' => $this->fsql,
                    'msg' => $this->fmsg,
                    'mmsg' => $this->fmmsg,
                    'no' => $this->fno,
                    'theclass' => get_class($this)
                );
                break;
        }
        return (array) $info;
    }

    final protected function setsql($afields = array(), $avalues = array()) {
        $this->cols = array();
        $this->vals = array();
        if (!is_array($afields)) {
            return false;
        } if (empty($afields)) {
            return false;
        }
        if (!is_array($avalues)) {
            return false;
        } if (empty($avalues)) {
            return false;
        }
        foreach ($afields as $key => $type) {
            if (!array_key_exists($key, $avalues))
                continue;
            $this->cols[] = $key;
            $s = "";
            $s = mysql_escape_string($avalues[$key]);
            switch ($type) {
                case "intorfloat":
                    $this->vals[] = (float) $s;
                    break;
                case "int":
                case "zint":
                    $this->vals[] = "$s";
                    break;
                default:
                    $this->vals[] = "'$s'";
                    break;
                /*
                  case "str":
                  case "emptystr":
                  case "name":
                  case "text":
                  case "path":
                  case "emptypath":
                  case "dirname":
                  case "emptydirname":
                  case "email":
                  case "emailmx":
                  case "ipaddr":
                  case "md5":
                  case "html":
                 */
            }
        }
        return (boolean) true;
    }

    final public function sql() {
        return (string) ($this->fsql);
    }

    public function connect($mysqlconf = false) {
        $this->fmmsg = "nothing done yet !";
        if (!$mysqlconf)
            return false;
        if (is_scalar($mysqlconf)) {
            $this->fmmsg = "$mysqlconf did not exist as a file and was not included";
            if (file_exists("$mysqlconf")) {
                require("$mysqlconf");
                $this->fmmsg = "$mysqlconf did exist as a file and was included";
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
            $this->fmsg = "could not connect to mysql server";
            $this->fmmsg = mysql_error();
            $this->fno = mysql_errno();
            return false;
        }
        if (!$this->selectdb()) {
            return false;
        }
        $this->fmsg = 'Connected[' . $this->fmsg . ']';
        $this->fmmsg = 'Connected [' . join('', mysql_fetch_row(mysql_query('SELECT NOW(), VERSION();', $this->con))) . "]";
        return (boolean) true;
    }

    final public function disconnect() {
        
    }

    final public function ok() {
        return (boolean) (is_array(@mysql_fetch_row(mysql_query('SELECT VERSION();', $this->con))));
    }

    final public function execute($s = '') {
        $this->fsql = "";
        $this->fsql = $s;
        $this->fmsg = "";
        $this->fno = "";
        $this->fmmsg = "";
        if (!$this->ok()) {
            return false;
        }
        if (empty($s)) {
            return false;
        }
        if (!@mysql_query($s, $this->con)) {
            $this->fmsg = "Could not execute query";
            $this->fno = mysql_errno();
            $this->fmmsg = (string) mysql_error($this->con);
            return false;
        }
        $this->fmsg = "SQL executed successfully";
        $this->fmmsg = "effected rows [" . mysql_affected_rows($this->con) . "]";
        $this->fno = "0";
        return true;
    }

    final public function executesql() {
        $this->fmsg = "";
        $this->fno = "";
        $this->fmmsg = "";
        if (!@mysql_query($this->fsql, $this->con)) {
            $this->fmsg = "Could not execute query";
            $this->fno = mysql_errno();
            $this->fmmsg = (string) mysql_error($this->con);
            /*
              switch (mysql_errno()) {
              case 1062:
              $this->fmmsg.=" [similar entry not allowed]";
              break;
              }
             */
            return false;
        }
        return true;
    }

    final public function selectdb() {
        if (!@mysql_select_db($this->ffdb, $this->con)) {
            $this->fmsg = "Could not select db";
            $this->fmmsg = @mysql_error($this->con);
            $this->fno = mysql_errno();
            return false;
        }
        return true;
    }

    final public function update($atablename = '', $afields = array(), $avalues = array(), $where = '') {
        if (!$this->setsql((array) $afields, (array) $avalues)) {
            return (boolean) false;
        }
        $tmp = array();
        foreach ($this->cols as $index => &$col) {
            $tmp[] = "$col=" . $this->vals[$index];
        }
        $this->fsql = "update " . $atablename . " set " . implode(',', $tmp) . " where ($where)";
        if (!$this->selectdb()) {
            return (boolean) false;
        }
        if (!$this->executesql()) {
            return (boolean) false;
        }
        $this->fmsg = "Update [" . mysql_affected_rows($this->con) . "]";
        $this->fmmsg = "Update [" . mysql_affected_rows($this->con) . "]";
        $r = mysql_affected_rows($this->con);
        $this->fno = $r;
        return (boolean) true;
    }

    final public function insert($atablename = '', $afields = array(), $avalues = array()) {
        if (!$this->setsql((array) $afields, (array) $avalues)) {
            return (boolean) false;
        }
        $this->fsql = "insert into $atablename (" .
                implode(",", $this->cols) . ") values (" .
                implode(",", $this->vals) . ")";
        //echo $this->fsql; echo "<br>";
        if (!$this->selectdb()) {
            return (boolean) false;
        }
        if (!$this->executesql()) {
            return (boolean) false;
        }
        $nid = mysql_insert_id($this->con);
        if ((int) $nid < 1)
            return (bool) false;
        $this->fmsg = "record successfully inserted";
        $this->fmmmsg = "[" . (string) mysql_affected_rows() . "] row(s) inserted";
        $this->fno = $nid;
        return (int) $nid;
    }

    final public function delete($atablename = '', $where = '') {
        $this->fsql = "delete from $atablename where $where";
        if (!$this->selectdb()) {
            return false;
        }
        if (!$this->executesql()) {
            return (boolean) false;
        }
        $r = mysql_affected_rows($this->con);
        $this->fmsg = "deleted[$r]";
        $this->fmmmsg = "[" . (string) mysql_affected_rows() . "] row(s) deleted";
        $this->fno = $r;
        return (int) $r;
    }

    final public function row($s = '', $values = array()) {
        if (empty($values)) {
            $this->fsql = $s;
        } else {
            foreach ($values as &$value) {
                if (preg_match("/^([A-Z]|[0-9]|\.|,|\+|\@|\-|\_|~|\.)+$/i", $value))
                    continue;
                return false;
            }
        }
        if (!$this->selectdb()) {
            return false;
        }
        foreach ($values as &$value) {
            if (false === ($value = mysql_real_escape_string($value)))
                return false;
        }
        $this->fsql = vsprintf($s, $values);
        if (!$arow = mysql_query($this->fsql, $this->con)) {
            $this->fmsg = "Could not execute qry";
            $this->fmmsg = mysql_error($this->con);
            $this->fno = mysql_errno();
            return false;
        }
        if ((int) mysql_num_rows($arow) === 1) {
            $this->fmsg = "1 row returned";
            $this->fmmsg = (string) mysql_num_rows($arow) . " returned";
            $er = (array) mysql_fetch_assoc($arow);
            if (!is_array($er)) {
                return false;
            }
            if (empty($er)) {
                return false;
            }
            if (!isset($er)) {
                return false;
            }
            return (array) $er;
        }
        $this->fmsg = "Query executed, but too many or few matches";
        $this->fmmsg = mysql_error($this->con);
        $this->fno = mysql_errno();
        return (boolean) false;
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

    final public function rowsa($s = '', $values = array(), $key = 'id') {
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
