<?

function xml2array($xml) {
    $xmlary = array();

    $reels = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*)<\/\s*\\1\s*>)/s';
    $reattrs = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';

    preg_match_all($reels, $xml, $elements);

    foreach ($elements[1] as $ie => $xx) {
        $xmlary[$ie]["name"] = $elements[1][$ie];

        if ($attributes = trim($elements[2][$ie])) {
            preg_match_all($reattrs, $attributes, $att);
            foreach ($att[1] as $ia => $xx)
                $xmlary[$ie]["attributes"][$att[1][$ia]] = $att[2][$ia];
        }

        $cdend = strpos($elements[3][$ie], "<");
        if ($cdend > 0) {
            $xmlary[$ie]["text"] = substr($elements[3][$ie], 0, $cdend - 1);
        }

        if (preg_match($reels, $elements[3][$ie]))
            $xmlary[$ie]["elements"] = xml2array($elements[3][$ie]);
        else if ($elements[3][$ie]) {
            $xmlary[$ie]["text"] = $elements[3][$ie];
        }
    }
    return $xmlary;
}

function ducurl($url, $xml) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);

    if (curl_errno($ch))
        return curl_error($ch);
    else
        curl_close($ch);

    return (array) xml2array($output);
}

/*
  $foo = is_int($i);
  // 0.0032429695129395 seconds

  $foo = ((int) $i) == $i;
  // 0.0020270347595215 seconds

  $foo = ((int) $i) === $i;
  // 0.001600980758667 seconds
 */

//return (boolean) ((int) $n) === $n;

function replacespaces(&$string = "") {
    $string = preg_replace("/\ +/", " ", $string);
    $string = preg_replace("/\ /", "_", $string);
    return ($string);
}

function pr(&$array) {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function bgoto($a) {
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/$a");
    exit(0);
}

function posted() {
    if (!is_array($_POST)) {
        return false;
    }
    if (empty($_POST)) {
        return false;
    }
    return true;
}

function loggedin() {

    if (!isset($_SESSION['euid'])) {
        return (boolean) false;
    }
    if (empty($_SESSION['euid'])) {
        return (boolean) false;
    }
    if (!$_SESSION['euid']) {
        return (boolean) false;
    }

    return (boolean) true;
}

function htauthenticate($u, $p, $msg) {
    if ((!isset($_SERVER['PHP_AUTH_USER'])) ||
            (!(($_SERVER['PHP_AUTH_USER'] == $u) && ($_SERVER['PHP_AUTH_PW'] == $p)))) {
        echo $msg;
        header('WWW-Authenticate: Basic realm="Test Authentication System"');
        header('HTTP/1.0 401 Unauthorized');
        return (boolean) false;
    }
    return (boolean) true;
}

function isint($n) {
    if (preg_match("/^[1-9][0-9]*$/", $n)) {
        return (boolean) true;
    }
    return false;
}

function iszint($n) {
    if (preg_match("/^[0-9]*$/", $n)) {
        return (boolean) true;
    }
    return false;
}

function iszfloat($v2) {
    if (preg_match("/^[+-]?([0-9]*\.[0-9]+|[0-9]*)$/i", $v2)) {
        return true;
    }
    return false;
}

function isfloat($v2) {
    if (preg_match("/^[+-]?([0-9]*\.[0-9]+|[0-9]+)$/i", $v2)) {
        return true;
    }
    return false;
}
function isdirname($v2) {
    if (preg_match("%^[A-Z0-9_\(\)\+\-\.,]+$%i", $v2)) {
        return true;
    }
    return false;
    //if (preg_match("/^([A-Z]|[0-9]|\.|,|\(|\)|\+|\@|\-|\_|~|\ \.)+$/i", $v2)){ return true; }
}

function istext(&$v2) {
    //if (! preg_match("%^[&A-Z'\"0-9@\ _\(\)\+\-\./\,]+$%i", $v2)){ return false; }
    //$r = preg_split("/\r\n?/", $v2); 
    $r = preg_split("/\r\n?/", $v2);
    foreach ($r as &$line)
        isstr($line);
    $v2 = implode("<br>", $r);
    return true;
}

function isstr(&$v2) {
    //if (! preg_match("%^[&A-Z'\"0-9@\ _\(\)\+\-\./\,]+$%i", $v2)){ return false; }
    $v2 = htmlspecialchars($v2, ENT_QUOTES, 'UTF-8');
    if (empty($v2))
        return false;
    $v2 = preg_replace("%/%", "&#x2F;", $v2);
    return true;
}

function isemptystr(&$v2) {
    $v2 = htmlspecialchars($v2, ENT_QUOTES, 'UTF-8');
    $v2 = preg_replace("%/%", "&#x2F;", $v2);
    return true;
}

function isemptydatetime($v2) {
    if (preg_match("/^(2\d{3}\-\d\d\-\d\d\ \d\d\:\d\d\:\d\d)?$/", $v2))
        return (bool) true;
    return (bool) false;
}

function isdatetime($v2) {
    if (preg_match("/^2\d{3}\-\d\d\-\d\d\ \d\d\:\d\d\:\d\d$/", $v2))
        return (bool) true;
    return (bool) false;
}

function isname(&$v2) {
    $v2 = htmlspecialchars($v2, ENT_QUOTES, 'UTF-8');
    if (empty($v2))
        return false;
    if (preg_match("%^([A-Z]|[0-9]|\_)+$%i", $v2)) {
        return true;
    }
    return false;
    //if (preg_match("/^([A-Z]|[0-9]|\.|,|\(|\)|\+|\@|\-|\_|~|\ \.)+$/i", $v2)){ return true; }
}

function isuid($v2) {
    if (preg_match("/^([A-Z]|[0-9]|\.|,|\+|\@|\-|\_|~)+$/i", $v2)) {
        return true;
    }
    return false;
    //if (preg_match("/^([A-Z]|[0-9]|\.|,|\(|\)|\+|\@|\-|\_|~|\ \.)+$/i", $v2)){ return true; }
}

function ispath($v2) {
    if (preg_match("%^/?[\(\)-_.a-zA-Z0-9-/]+$%", $v2)) {
        return true;
    }
    return false;
}

function isemptypath($v2) {
    if (preg_match("%^/?[-_.a-zA-Z0-9-/]*$%", $v2)) {
        return true;
    }
    return false;
}

function isemptyipaddr($v2) {
    if (preg_match("/^((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))?$/", $v2))
        return (bool) true;
    return (bool) false;
}

function isipaddr($v2) {
    if (preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/", $v2))
        return (bool) true;
    return (bool) false;
}

function ismd5($v2) {
    if (preg_match('#^[0-9a-f]{32}$#i', $v2)) {
        return true;
    }
    return false;
}

function isurl($n) {
    if (preg_match("/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/", $n)) {
        return (boolean) true;
    }
    return (boolean) false;
}

function isnumber($n) {
    if (preg_match("/^[0-9]+$/", $n)) {
        return (boolean) true;
    }
    return false;
}

function isemptyemail($n) {
    if (preg_match("/^([^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,})?$/i", $n))
        return true;
    return false;
}

function isemail($n) {
    if (preg_match("/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i", $n))
        return true;
    return false;
}

function isemailmx($email) {
    if (!isemail($email))
        return (bool) false;
    list($name, $domain) = split('@', $email);
    if (!checkdnsrr($domain, 'MX')) {
        return (bool) false; // No MX record found
    };
    return (bool) true;
}

function parseinput($src = array(), $dst = array()) {

    if (!is_array($src)) {
        return (string) "src not an array";
    }
    if (!is_array($dst)) {
        return (string) "dst not an array";
    }

    if (empty($src)) {
        return (string) "src is empty";
    }
    if (empty($dst)) {
        return (string) "dst is empty";
    }

    $ar = array();
    $errors = "";

    foreach ($dst as $key => &$field) {

        if (!empty($errors))
            continue;

        switch ((string) $field) {

            case "zint":

                (boolean) $check = iszint($src[$key]);

                if ($check) {
                    $ar[$key] = (int) $src[$key];
                } else {
                    $errors.=" Invalid znumber [$key] ";
                }

                break;

            case "int":

                (boolean) $check = isint($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid number [$key] ";
                }

                break;

            case "str":

                (boolean) $check = isstr($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid string [$key] ";
                }

                break;

            case "emptystr":

                (boolean) $check = isemptystr($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid zstring [$key] ";
                }

                break;

            case "zfloat":

                (boolean) $check = iszfloat($src[$key]);

                if ($check) {
                    $ar[$key] = (float) $src[$key];
                } else {
                    $errors.=" Invalid zfloat [$key] ";
                }

                break;

            case "float":

                (boolean) $check = isfloat($src[$key]);

                if ($check) {
                    $ar[$key] = (float) $src[$key];
                } else {
                    $errors.=" Invalid float [$key] ";
                }

                break;

            case "md5":

                (boolean) $check = ismd5($src[$key]);

                if ($check) {
                    $ar[$key] = (string) $src[$key];
                } else {
                    $errors.=" Invalid code [$key] ";
                }

                break;

            case "name":

                (boolean) $check = isname($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid name [$key] ";
                }

                break;

            case "uid":

                (boolean) $check = isuid($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid username [$key] ";
                }

                break;

            case "emptydatetime":

                (boolean) $check = isemptydatetime($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid zdate [$key] ";
                }

                break;

            case "datetime":

                (boolean) $check = isdatetime($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid date [$key] ";
                }

                break;

            case "path":

                (boolean) $check = ispath($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid path [$key] ";
                }

                break;

            case "emptypath":

                (boolean) $check = isemptypath($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid zpath [$key] ";
                }

                break;

            case "dirname":

                $src[$key] = replacespaces(trim($src[$key]));

                (boolean) $check = isdirname($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid dirname [$key] ";
                }

                break;

            case "emptyemail":

                (boolean) $check = isemptyemail($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid zemail [$key] ";
                }

                break;

            case "email":

                (boolean) $check = isemail($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid email [$key] ";
                }

                break;

            case "emailmx":

                (boolean) $check = isemailmx($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid emailmx [$key] ";
                }

                break;

            case "emptyipaddr":

                (boolean) $check = isemptyipaddr($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid zIP [$key] ";
                }

                break;

            case "ipaddr":

                (boolean) $check = isipaddr($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $errors.=" Invalid IP [$key] ";
                }

                break;

            case "html":

                (boolean) $check = true;
                if ($check) {
                    $ar[$key] = preg_replace("/\n\r/i", "\n", $src[$key]);
                    //$ar[$key] = preg_replace ("/\n/","\n\r\n", $ar[$key]);
                } else {
                    $errors.=" Invalid html [$key] ";
                }

                break;

            case "text":

                (boolean) $check = istext($src[$key]);

                if ($check)
                    $ar[$key] = $src[$key];
                else
                    $errors.="invalid text [$key] ";

                break;

            default:

                $errors.=" Unknown datetype [$field] ";

                break;
        }
    }

    if (!empty($errors))
        return (string) "$errors";

    return (array) $ar;
}

function setupdir($base = '', $subdirs = array()) {

    if (!is_dir($base))
        @mkdir($base);

    if (!is_dir($base))
        return (boolean) false;

    foreach ($subdirs as $subdir) {
        if (!is_dir("$base/$subdir"))
            mkdir("$base/$subdir");

        if (!is_dir("$base/$subdir"))
            return (boolean) false;
    }

    return (boolean) true;
}

function md5_hex_to_dec($hex_str) {
    $arr = str_split($hex_str, 4);
    foreach ($arr as $grp) {
        $dec[] = str_pad(hexdec($grp), 5, '0', STR_PAD_LEFT);
    }
    return implode('', $dec);
}

function md5_dec_to_hex($dec_str) {
    $arr = str_split($dec_str, 5);
    foreach ($arr as $grp) {
        $hex[] = str_pad(dechex($grp), 4, '0', STR_PAD_LEFT);
    }
    return implode('', $hex);
}

function selectbox_array($name, $arr, $selectedkey) {
    foreach ($arr as $key => $val)
        if ($key == $selectedkey)
            echo "<option selected value='$key'>" . $val[$name] . "</option>";
        else
            echo "<option value='$key'>" . $val[$name] . "</option>";
}

function selectbox_sql($mc, $sql, $name, $selectedkey) {
    $ar = $mc->rows($sql, array(), "id");
    if (!is_array($ar))
        return;
    foreach ($ar as $key => $val)
        if ($key == $selectedkey)
            echo "<option selected value='$key'>" . $val[$name] . "</option>";
        else
            echo "<option value='$key'>" . $val[$name] . "</option>";
}

function selectbox_list($listboxname, $listboxdescription, $arr, $selectedkey) {
    echo "<select name='$listboxname'>";
    foreach ($arr as $key => $val) {
        if ($arr[$key] == $selectedkey) {
            echo "<option selected value='" . $arr[$key] . "'>" . $arr[$key] . "</option>";
        } else {
            echo "<option value='" . $arr[$key] . "'>" . $arr[$key] . "</option>";
        }
    }
    echo "</select>";
}

function optionarray($arr, $selectedkey) {
    foreach ($arr as $key => $val) {
        if ((int) $key == (int) $selectedkey) {
            echo "<option selected value='$key'>" . $arr[$key] . "</option>";
        } else {
            echo "<option value='" . $key . "'>" . $arr[$key] . "</option>";
        }
    }
}

function selectbox_id_list($listboxname, $listboxdescription, $arr, $selectedid) {
    echo "<select name='$listboxname'>";
    foreach ($arr as $key => $val) {
        if ((int) $key == (int) $selectedid) {
            echo "<option selected value='$key'>" . $arr[$key] . "</option>";
        } else {
            echo "<option value='" . $key . "'>" . $arr[$key] . "</option>";
        }
    }
    echo "</select>";
}

function edittbox_list($arr) {
    foreach ($arr as $key => $val) {
        echo "<input type='text' name='" . $arr[$key] . "'>";
    }
}

//if (false === ($row = $mc->row($sql)))
//exit(__FUNCTION__.'(): not passed 111 arg');
//if (true !== ($ee = sendemail($msg))) { return (string) "e in benemail(): sendemail() returned [$ee]"; }


function remove_uploaded_files($ar) {
    foreach ($ar as $key => $file)
        if (!empty($file['tmp_name']))
            if (@file_exists($file['tmp_name']))
                ;
    @unlink($file['tmp_name']);
}

function cola($x, $y) {
    global $colname;
    if (strtoupper($x[$colname]) == strtoupper("INDEX.HTML"))
        return -1;
    else if (strtoupper($y[$colname]) == strtoupper("INDEX.HTML"))
        return 1;
    else if (strtoupper($x[$colname]) == strtoupper($y[$colname]))
        return 0;
    else if (strtoupper($x[$colname]) < strtoupper($y[$colname]))
        return -1;
    else
        return 1;
}

function cold($x, $y) {
    global $colname;
    if (strtoupper($x[$colname]) == strtoupper($y[$colname]))
        return 0;
    else if (strtoupper($x[$colname]) < strtoupper($y[$colname]))
        return 1;
    else
        return -1;
}

function benlist($rows) {
    echo "<center><table>";
    foreach ($rows as $key => $row) {
        foreach ($row as $k2 => $fields) {
            echo "<tr><td align=right><b>$k2&nbsp;&nbsp;&nbsp;&nbsp;</b></td><td>" .
            $row[$k2] . "</td</tr>";
        }
        echo "</table><br><hr><br><table>";
    }
    echo "</center>";
}

function ifben($m = '') {
    if (!ismd5($_SESSION['euid']))
        return;
    if ((string) $_SESSION['euid'] !== (string) "ef18ceec88e8f0bd8e4ad63f4b59541b")
        return;
    echo $m;
}

/*
  update product set primaryimage = replace(primaryimage, 'img/', '') where id = 57
 */

/*
  // Turn off magic_quotes_runtime
  if (get_magic_quotes_runtime())
  set_magic_quotes_runtime(0);

  if (get_magic_quotes_gpc()) {

  function stripslashes_array($array) {
  return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
  }

  $_GET = stripslashes_array($_GET);
  $_POST = stripslashes_array($_POST);

  }
  /*
 */

function clean_array(&$ar = array()) {
    foreach ($ar as $key => &$el) {
        $el['name'] = strip_tags($el['name']);
        $el['type'] = strip_tags($el['type']);
        $el['descr'] = strip_tags($el['descr']);
        $el['value'] = strip_tags($el['value']);
        $el['name'] = preg_replace("/[^@A-Za-z0-9\ \!\.\,\-]/", "", $el['name']);
        $el['type'] = preg_replace("/[^@A-Za-z0-9\ \!\.\,\-]/", "", $el['type']);
        $el['descr'] = preg_replace("/[^@A-Za-z0-9\ \!\.\,\-]/", "", $el['descr']);
        $el['value'] = preg_replace("/[^@A-Za-z0-9\ \!\.\,\-]/", "", $el['value']);
    }
}

function arraycopy($src = array(), &$dst = array()) {
    foreach ($src as $key => $value)
        if (array_key_exists($key, $dst))
            $dst[$key][value] = $value;
}

function pareinput($src = array(), $dst = array()) {

    if (!is_array($src)) {
        return (string) "src not an array";
    }
    if (!is_array($dst)) {
        return (string) "dst not an array";
    }

    if (empty($src)) {
        return (string) "src is empty";
    }
    if (empty($dst)) {
        return (string) "dst is empty";
    }

    $ar = array();
    (int) $checkcount = 0;

    foreach ($dst as $key => &$field) {

        if ((int) $checkcount > 0)
            continue;

        switch ((string) $field) {

            case "zint":

                (boolean) $check = preg_match("/^[0-9]+$/", (int) $src[$key]);

                if ($check) {
                    $ar[$key] = (int) $src[$key];
                } else {
                    $checkcount+=88;
                }

                break;

            case "int":

                (boolean) $check = preg_match("/^[1-9]+[0-9]*$/", $src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $checkcount+=44;
                }

                break;

            case "md5":

                (boolean) $check = ismd5($src[$key]);

                if ($check) {
                    $ar[$key] = (string) $src[$key];
                } else {
                    $checkcount+=34;
                }

                break;

            case "name":

                (boolean) $check = preg_match("/^[\_A-Za-z0-9\ \.\-]+$/", $src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $checkcount+=22;
                }

                break;

            case "datetime":

                (boolean) $check = preg_match("/^2\d{3}\-\d\d\-\d\d\ \d\d\:\d\d\:\d\d$/", $src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $checkcount+=77;
                }

                break;

            case "path":

                (boolean) $check = preg_match("%^/?[-_.a-zA-Z0-9-/]+$%", $src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $checkcount+=11;
                }

                break;

            case "emptypath":

                (boolean) $check = preg_match("%^/?[-_.a-zA-Z0-9-/]*$%", $src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $checkcount+=11;
                }

                break;

            case "email":

                (boolean) $check = isemail($src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $checkcount+=55;
                }

                break;

            case "ipaddr":

                (boolean) $check =
                        preg_match("/(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/", $src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $checkcount+=33;
                }

                break;

            case "html":

                (boolean) $check = true;

                if ($check) {
                    $ar[$key] = preg_replace("/\n\r/i", "\n", $src[$key]);
                    //$ar[$key] = preg_replace ("/\n/","\n\r\n", $ar[$key]);
                } else {
                    $checkcount+=44;
                }

                break;

            case "text":

                (boolean) $check = true;

                if ($check) {
                    $ar[$key] = htmlspecialchars($src[$key], ENT_QUOTES, 'UTF-8');
                    //$ar[$key] = preg_replace ("%\[(.+)\]%", "<a href=\"$1\">$1</a>",$src[$key]);
                    $ar[$key] = preg_replace("/\n\r/i", "\n", $ar[$key]);
                    //$ar[$key] = preg_replace ("/\n/","<br />", $ar[$key]);
                } else {
                    $checkcount+=66;
                }

                break;

            case "str":

                (boolean) $check = preg_match("%^[A-Z0-9\.]+$%", $src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $checkcount+=999;
                }

                break;

            case "zstr":

                (boolean) $check = preg_match("/^[@\_A-Za-z0-9\/\!\ \.\,\-]*$/", $src[$key]);

                if ($check) {
                    $ar[$key] = $src[$key];
                } else {
                    $checkcount+=99;
                }

                break;

            case "intorfloat":

                (boolean) $check = preg_match("/^[0-9]+\.?[0-9]+$/", $src[$key]);

                if ($check) {
                    $ar[$key] = (float) $src[$key];
                } else {
                    $checkcount+=1;
                }

                break;

            case "zintorfloat":

                (boolean) $check = preg_match("/^[0-9]+\.?[0-9]*$/", $src[$key]);

                if ($check) {
                    $ar[$key] = (float) $src[$key];
                } else {
                    $checkcount+=1;
                }

                break;

            default:

                $checkcount+=10;

                break;
        }
    }

    if ((int) $checkcount > 0)
        return (string) "$checkcount";

    return (array) $ar;
}

function saninput(&$ar = array()) {
    return;
    $ar = striptags($ar);
    $ar = stripchars($ar);
    $ar = specialchars($ar);
}

function mysql_real_escape_string_array($mc, &$arr) {

    if (!is_array($arr))
        return false;
    if (empty($arr))
        return false;
    foreach ($arr as &$a) {
        if (is_array($a))
            return false;
        $a = mysql_real_escape_string($a, $mc);
    }
    return (array) $arr;
}

function specialchars(&$ar) {

    return is_array($ar) ?
            array_map('specialchars', $ar) :
            htmlspecialchars($ar, ENT_QUOTES, 'UTF-8');
}

function stripchars(&$ar) {

    return is_array($ar) ?
            array_map('stripchars', $ar) :
            preg_replace("/[^@\_A-Za-z0-9\!\ \.\,\-]/", "", $ar);
}

function striptags(&$value) {

    return is_array($value) ?
            array_map('striptags', $value) :
            strip_tags($value);
}

function sendemail(&$amsg = NULL) {

    if (!is_array($amsg)) {
        return (string) "no message";
    }

    $emsg = array();
    $smbin = "nothing";

    $files[] = "/bin/sendmai";
    $files[] = "/sbin/sendmai";
    $files[] = "/usr/bin/sendmail";
    $files[] = "/usr/sbin/sendmail";

    foreach ($files as $file) {
        if ($smbin !== "nothing")
            continue;
        switch (@file_exists($file)) {
            case true:
                $smbin = "$file";
                break;
        }
    }

    if (!file_exists("$smbin"))
        return (string) "no sendmail binary found";

    $emsg[] = "MIME-Version: 1.0\n";
    $emsg[] = "Content-type: text/html; charset=iso-8859-1\n";

    foreach ($amsg as $line) {
        $emsg[] = $line . "\n";
    }

    $estr = implode('', $emsg);
    $l = (int) strlen($estr);

    $fp = popen("$smbin -t", "w");

    if (!$fp) {
        return "no pipe for ($smbin)";
    }

    $r = (int) fwrite($fp, $estr);
    pclose($fp);

    if ((int) $r <> (int) $l) {
        return (string) "fwrite() wanted to write ($l) bytes, but only wrote ($r) bytes";
    }

    return (boolean) true;
}

function phpemail($fromname, $fromemail, $toname, $toemail, $subject, $amsg) {

    $headers = array();
    $body = array();

    if (!is_array($amsg)) {
        return (string) "no message";
    }

    if (!isemail($fromemail))
        return "no from email";

    if (!isemail($toemail))
        return "no to email";

    foreach ($amsg as $line) {
        $body[] = $line . "\n";
    }

    $headers[] = "MIME-Version: 1.0\n";
    $headers[] = "Content-type: text/html; charset=iso-8859-1\n";

    $from = "From: $fromname <$fromemail>\n";

    array_push($headers, $from);

    $body = implode('', $body);
    $headers = implode('', $headers);

    mail($toemail, $subject, $body, $headers);
}

function benemail($fromname, $fromemail, $toname, $toemail, $subject, $msg) {

    if (!isemail($fromemail))
        return "no from email";

    if (!isemail($toemail))
        return "no to email";

    $from = "From: $fromname <$fromemail>";
    array_unshift($msg, $from);
    $to = "To: $toemail";
    array_unshift($msg, $to);

    $subject = "Subject: $subject";
    array_unshift($msg, $subject);

    if (true !== ($ee = sendemail($msg))) {
        return (string) "e in benemail(): sendemail() returned [$ee]";
    }

    return (boolean) true;
}

function array_is_safe($arr) {
    foreach ($arr as $key => $val) {
        if (!(preg_match("/^([A-Z]|[0-9]|\.|,|\(|\)|\+|\@|\r|\n|\-|\_|~|\ )*$/i", $arr[$key]))) {
            return false;
        }
    }
    return true;
}

function safe_hyperlink($hyperlink) {
    if (!(preg_match("/^([A-Z]|[0-9]|\.|,|\@|\/|\-|\_|~|\ )*$/i", $hyperlink))) {
        return false;
    }
    return true;
}

function ap(&$array) {
    if (!is_array($array)) {
        echo "<b>No Data</b>";
        return;
    }
    if (empty($array)) {
        echo "<b>No Data</b>";
        return;
    }

    foreach ($array as $a => $v)
        echo $v['block'];
}

function sessioninit($sname, $maxlifetime) {
    ini_set('session.name', $sname);
    session_name($sname);
    ini_set('session.gc_maxlifetime', $maxlifetime);
}

function sr() {
    session_regenerate_id();
}

function replaceimages($afilearr = array()) {

    $s = array();

    foreach ($afilearr as $key => &$file) {

        if ((int) $file['error'] !== 0)
            continue;
        if ((string) $file['name'] === '')
            continue;
        if ((string) $file['tmp_name'] === '')
            continue;
        if (!@file_exists($file['tmp_name']))
            continue;

        $dst = IPATH . "/" . $file['path'] . "/" . $file['filename'];

        //Move image
        if (!@move_uploaded_file($file['tmp_name'], $dst)) {
            @unlink($file['tmp_name']);
            continue;
        }

        $s[$key] = $file;
    }
    return (array) $s;
}

function moveimages($prefixpath, $path, $afilearr = array()) {

    if (!setupdir($prefixpath, array())) {
        foreach ($afileerr as $key => &$file)
            @unlink($file['tmp_name']);
        return array();
    }

    if (!setupdir("$prefixpath/$path", array())) {
        foreach ($afileerr as $key => &$file)
            @unlink($file['tmp_name']);
        return array();
    }

    $ic = 1;
    $a = time();

    $s = array();

    foreach ($afilearr as $key => &$file) {

        if ((int) $file['error'] !== 0)
            continue;
        if ((string) $file['name'] === '')
            continue;
        if ((string) $file['tmp_name'] === '')
            continue;
        if (!@file_exists($file['tmp_name']))
            continue;

        $filename = $a . "_" . (int) $ic . ".jpg";
        $ic++;

        $file['filename'] = $filename;
        $file['path'] = "$path";

        $dst = "$prefixpath/$path/$filename";

        //Move image
        if (!@move_uploaded_file($file['tmp_name'], $dst)) {
            @unlink($file['tmp_name']);
            continue;
        }

        $s[$key] = $file;
    }
    return (array) $s;
}

function resizeimage($prefix, $subdir, $filename, $resizearray = array()) {

    if (!setupdir($prefix, array())) {
        return "$prefix does not exist or is not a directory";
    }

    $srcdir = $prefix . '/' . $subdir;
    if (!setupdir($srcdir, array())) {
        return "$srcdir does not exist or is not a directory";
    }

    $srcfilename = $srcdir . '/' . $filename;
    //$srcfilename = $srcdir.'/0/'.$filename;
    if (!file_exists($srcfilename)) {
        return "$srcfilename does not exist or is not a file";
    }

    $idx = array_keys($resizearray);
    array_shift($idx);
    $idx[] = count($idx) + 1;

    if (!setupdir($srcdir, $idx)) {
        return "Could not setup subdirs inside $srcdir";
    }

    list($width, $height) = getimagesize($srcfilename);

    $i = 1;

    foreach ($resizearray as $dimension) {

        $dstdir = $srcdir . '/' . $i;
        $i++;

        if (!is_dir($dstdir)) {
            echo "$dstdir does not exist or is not a directory\n";
            continue;
        }

        passthru('chmod 755 ' . $dstdir);

        $dst = $dstdir . '/' . $filename;

        $twidth = $dimension;
        $theight = $dimension;

        // Don't overresize
        if ($width <= $twidth) {
            $twidth = $width;
        }
        if ($height <= $theight) {
            $theight = $height;
        }

        // get ratios
        $widthratio = $twidth / $width;
        $heightratio = $theight / $height;

        //defaults to widthratio
        $resizeratio = $widthratio;

        //get resize ratio
        if ($heightratio < $widthratio) {
            $resizeratio = $heightratio;
        }

        $percent = $resizeratio;

        $newwidth = $width * $percent;
        $newheight = $height * $percent;

        echo 'convert ' . $srcfilename . ' -coalesce -thumbnail ' . $newwidth . 'x' . $newheight . ' -quality 100 ' . $dst . "\n";
        passthru('convert ' . $srcfilename . ' -coalesce -thumbnail ' . $newwidth . 'x' . $newheight . ' -quality 100 ' . $dst . ".mid");
        passthru('convert ' . $dst . '.mid -gravity center -background white -extent ' . $dimension . 'x' . $dimension . ' ' . $dst);
        passthru('chmod 755 ' . $dst);
        unlink($dst . '.mid');

        //echo "$dst tnail\n";
    }
    return (boolean) true;
}

function arraytotext(&$array) {
    return (string) implode("<br>", $array);
}

function rstring($rs) {

    $rs = preg_replace("/\ /", "", $rs);
    if (!isuid($rs)) {
        return false;
    }

    $rip = preg_replace("/\./", "", $_SERVER['REMOTE_ADDR']);
    if (!isnumber($rip)) {
        return false;
    }

    $site = preg_replace("/\./", "", $_SERVER['HTTP_HOST']);
    if (!isuid($site)) {
        return false;
    }

    $datething = date("iWsG");
    if (!isnumber($datething)) {
        return false;
    }

    list($usec, $sec) = explode(" ", microtime());

    $usec = (string) $usec;
    $usec = preg_replace("/\./", "", $usec);
    $usec = preg_replace("/^0*/", "", $usec);

    $usec = preg_replace("/0+$/", "", $usec);
    $usec = strrev($usec);
    $usec = $usec . date("BnN");
    if (!isnumber($usec)) {
        return false;
    }

    $bits = ($usec[3] + 244) . ($usec[1] + 2) . $usec[4] . $usec[2];

    return md5((string) ($datething . $rs . $usec . $rip . $bits . $site));
    //exit(__FUNCTION__.'(): not passed 111 arg');
    //return (string) $rs;
}

function red_box($msg, $width) {
    echo "<p style='width:$width; border:1px solid #FF1515; height:60px; padding-left:10px; margin-left:auto; margin-right:auto; background-color:#FFBBBB;'>
 $msg</p>";
}

/*
  function red_box ($msg, $width) {
  echo "<p style='border:1px solid #FF1515; background-color:#FFBBBB; width:$width; padding:5px;'>";
  echo "<font size=25px color=red>X<br></font>$msg</p>";
  }
 */

function gbox($msg, $width) {
    echo "<p style='width:$width; border:1px solid #85BA0B; padding-left:5px; padding-right:5px; margin-left:auto; margin-right:auto; background-color:#B9FFB9;'>
 $msg</p>";
}

function green_box($msg, $width) {
    echo "<p style='width:$width; border:1px solid #85BA0B; margin-left:auto; margin-right:auto; background-color:#B9FFB9;'>
 $msg</p>";
}

function mycaptchaform($c, $c2, $c3, $fields) {
    echo "<table>";
    foreach ($fields as $key => $field) {
        echo "<tr><td align='right'>" . $field[value] . "</td>";
        echo "<td><input type='" . $field[type] . "' value='' name = '$key'></td></tr>";
    }
}

function myform($fields, $values) {

    if (((array) $fields) !== $fields) {
        echo "no field data";
        return;
    }

    if (((array) $values) !== $values) {
        echo "no value data";
        return;
    }

    foreach ($fields as $key => $field) {

        $value = "";
        if (array_key_exists($key, $values))
            $value = $values[$key];

        switch ($field['type']) {
            case "hidden":
                echo "<input type='hidden' maxlength=" .
                $field['ln'] . " name='$key' value='$value'>\n";
                break;
        }
    }
    echo "<table>";
    foreach ($fields as $key => $field) {

        $value = "";
        if (array_key_exists($key, $values))
            $value = $values[$key];

        switch ($field['type']) {

            case "hidden":
                break;

            case "text":
                echo "<tr><td align='right'><label>" . $field['descr'] . "</label></td>";
                echo "<td><input type='text' size=" . ((int) ($field['ln']) + 2) .
                " value='$value' name = '$key' maxlength=" . $field['ln'] . "></td></tr>";
                break;

            case "nothing":
                echo "<tr><td colspan='2'>&nbsp;</td></tr>";
                break;

            case "label":
                echo "<tr><td colspan='2' align='center'><label>" . $field['descr'] .
                "</label></td></tr>";
                break;

            case "image":
                echo "<tr><td colspan='2' align='center'><img src='" . IMGPATH .
                "$value'></td></tr>";
                break;

            case "static":
                echo "<tr><td align='right'><label>" . $field['descr'] . "</label></td>";
                echo "<td>$value</td></tr>";
                break;

            case "checkboxmakethesame":
                echo "<tr><td colspan='2'><input type='checkbox'>Make the same</td></tr>";
                break;

            case "checkbox":
                echo "<tr><td valign='top' align='right'>" . $field['descr'] . "</td>";
                if ((int) $value === 1) {
                    echo "<td><input type='checkbox' name ='$key' value='1' checked>&nbsp;</td></tr>";
                } else {
                    echo "<td><input type='checkbox' name ='$key' value='1'>&nbsp;</td></tr>";
                }
                break;

            case "textarea":
                echo "<tr><td valign='top' align='right'><label>" . $field['descr'] . "</label></td>";
                echo "<td><textarea cols=" . $field[col] . " rows=" . $field['row'] . " name='$key'>$value</textarea></td></tr>";
                break;

            case "password":
                echo "<tr><td align='right'><label>" . $field['descr'] . "</label></td>";
                echo "<td><input type='" . $field['type'] . "' size=" . ((int) ($field['ln']) + 2) . " value='$value' name = '$key' maxlength=" . $field['ln'] . "></td></tr>";
                break;
            /*
              if ($field[type] == "checkbox") {
              echo "<tr><td valign='top' align='right'><font color='#00D30F'><b>".$field[descr]."</b></font></td>";
              if ($field[value] === "1")      {
              echo "<td><input style='border-left:1px solid #D1D1D1; border-top:1px solid #D1D1D1; border-bottom:1px solid #D1D1D1; padding-left:4px; border-right:1px solid #A6FFAE;' type='checkbox' name ='$key' value='1' checked>&nbsp;</td></tr>";
              }       else {
              echo "<td><input type='checkbox' style='border-left:1px solid #D1D1D1; border-top:1px solid #D1D1D1; border-bottom:1px solid #D1D1D1; padding-left:4px; border-right:1px solid #A6FFAE;' name ='$key' value='1'>&nbsp;</td></tr>";
              }
              continue;
              }

              if ($field[type] == "textarea") {
              echo "<tr><td valign='top' align='right'><font color='#00D30F'><b>".$field[descr]."</b></font></td><td><textarea style='border-left:1px solid #D1D1D1; border-top:1px solid #D1D1D1; border-bottom:1px solid #D1D1D1; padding-left:4px; border-right:1px solid #A6FFAE;' cols=60 name='$key'>".$field[value]."</textarea></td></tr>";
              continue;
              }

              if ($field[type]==="password") {
              echo "<tr><td align='right'><font color='#00D30F'><b>".$field[descr]."</b></font></td>";
              echo "<td><input style='border-left:1px solid #D1D1D1; border-top:1px solid #D1D1D1; border-bottom:1px solid #D1D1D1; padding-left:4px; border-right:1px solid #A6FFAE;' type='".$field[type];
              echo "' size=75 value='".$field[value]."' name = '$key' maxlength=$ln></td></tr>";
              }

              if (preg_match ("/^(str)\d+$/", $field[type])) {

              $ln = preg_replace ("/[^\d]/", "", $field[type]);

              echo "<tr><td align='right'><font color='#00D30F'><b>".$field[descr]."</b></font></td>";
              echo "<td><input style='border-left:1px solid #D1D1D1; border-top:1px solid #D1D1D1; border-bottom:1px solid #D1D1D1; padding-left:4px; border-right:1px solid #A6FFAE;' type='".$field[type];
              echo "' size=75 value='".$field[value]."' name = '$key' maxlength=$ln></td></tr>";
             */
        }
    }
}

function buttons($colcount, $array) {
    $i = 0;
    foreach ($array as $key => $page) {
        if ($i == 0) {
            echo "<tr>";
        } else {
            if ($i % $colcount == 0) {
                echo "</tr><tr>";
            }
        }
        ?>
        <form action='<?= $_SERVER['PHP_SELF']; ?>' method='POST'>

            <?
            echo "<td><input type='submit' style='border-style:none; margin:0px; width:100%; padding:10px;' id='$key' name = 'btncat' value='" . $page['name'] . "'>";
            echo "</td>";
            echo "<input type='hidden' name='catid' value='$key'>";
            echo "</form>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
    }

    function displaybuttons($checkboxname, $colcount, $array, $field, $selectedids) {
        $i = 0;
        echo "<table>";
        foreach ($array as $key => $page) {
            if ($i == 0) {
                echo "<tr>";
            } else {
                if ($i % $colcount == 0) {
                    echo "</tr><tr>";
                }
            }
            echo "<td><input type='button' style='border-style:none; margin:0px; width:100%; padding:10px;' onclick='toggle(this);' id='$key' name = '$checkboxname' value='" . $page['name'] . "'>";
            echo "</td>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

    function checkboxes($checkboxname, $colcount, $array, $field, $selectedids) {
        $i = 0;
        echo "<table>";
        foreach ($array as $key => $page) {
            if ($i == 0) {
                echo "<tr>";
            } else {
                if ($i % $colcount == 0) {
                    echo "</tr><tr>";
                }
            }

            if (is_array($selectedids) && array_key_exists($key, $selectedids)) {
                echo "<td bgcolor=#EEEEEE><input id='checker$checkboxname$key' type = 'checkbox' checked name = '$checkboxname' value='$key'>";
            } else {
                echo "<td><input id='checker$checkboxname$key' type = 'checkbox' name = '$checkboxname' value='$key'>";
            }

            echo "<label for='checker$checkboxname$key'>" . $page[$field] . "</label></td>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

    function productswithimages($checkboxname, $colcount, $array, $field, $selectedids, $imagesar) {

        die(arrayprint($array));
        $i = 0;
        echo "<table cellspacing=6 cellpadding=10>";
        foreach ($array as $key => $page) {
            if ($i == 0) {
                echo "<tr>";
            } else {
                if ($i % $colcount == 0) {
                    echo "</tr><tr>";
                }
            }

            if (is_array($selectedids) && array_key_exists($key, $selectedids)) {
                echo "<td style='border:1px solid silver;'>";
                echo $key;
                echo "<img src='" . $page['primaryimage'] . "'>";
                echo "<input id='checker$checkboxname$key' type = 'checkbox' checked name = '$checkboxname' value='$key'>";
            } else {
                echo "<td style='border:1px solid silver;' valign='top'>";
                echo $key;
                echo "<table width='100%'>";
                echo "<tr><td align=center valign='top' style='border-bottom:1px solid green;'><b><font color=black>" .
                $page[$field] . "</font></b></td></tr>";
                echo "<tr><td align='center' valign=top><img src='" . $page['photo'] . "' onclick=\"document.getElementById('prody$key').checked=(! document.getElementById('prody$key').checked);\"></td></tr>";
                echo "<tr><td align='center'><input name = '$checkboxname' type = checkbox id='prody$key' value='$key'></td></tr>";
                echo "</tr>";
                echo "</table>";
                //echo "<img src='".$page['photo']."'><input id='checker$checkboxname$key' type = 'checkbox' name = '$checkboxname' value='$key'>";
            }
            echo "</td>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

    function displayimages($colcount, $array, $version, $primary = 0) {
        if (!is_array($array))
            return;
        if (empty($array))
            return;
        $i = 0;
        echo "<table>";
        foreach ($array as $key => $page) {
            if ($i == 0) {
                echo "<tr>";
            } else {
                if ($i % $colcount == 0) {
                    echo "</tr><tr>";
                }
            }

            $style = '';
            if ($key == $primary)
                echo "<td><img style='padding:1px; border:3px solid orange;' onclick=\"document.getElementById('checker$checkboxname$key').checked=(! document.getElementById('checker$checkboxname$key').checked);\" src='" . IPATH . "/" . $page['path'] . "/$version/" . $page['filename'] . "'></td>";
            else
                echo "<td><img style='padding:1px; border:0px solid #021a40;' onclick=\"document.getElementById('checker$checkboxname$key').checked=(! document.getElementById('checker$checkboxname$key').checked);\" src='" . IPATH . "/" . $page['path'] . "/$version/" . $page['filename'] . "'></td>";
            //echo "<label for='checker$checkboxname$key'><img src='".$page['path']."/".$page['filename']."'></label></td>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

    function checkboxesimages($checkboxname, $colcount, $version, $array) {
        $i = 0;
        echo "<table>";
        foreach ($array as $key => $page) {
            if ($i == 0) {
                echo "<tr>";
            } else {
                if ($i % $colcount == 0) {
                    echo "</tr><tr>";
                }
            }

            /* if (is_array($selectedids) && in_array ($key, $selectedids)) {
              echo "<td><input id='checker$checkboxname$key' type = 'checkbox' checked name = '$checkboxname' value='$key'>".
              "<img onclick=\"document.getElementById('checker$checkboxname$key').checked=(! document.getElementById('checker$checkboxname$key').checked);\" src='".$page['path']."/".$page['filename']."'></td>";
              } else  {
             */
            echo "<td><input id='checker$checkboxname$key' type = 'checkbox' name = '$checkboxname' value='$key'>" .
            "<img onclick=\"document.getElementById('checker$checkboxname$key').checked=(! document.getElementById('checker$checkboxname$key').checked);\" src='" . IPATH . '/' . $page['path'] . "/$version/" . $page['filename'] . "'></td>";
            //echo "<label for='checker$checkboxname$key'><img src='".$page['path']."/".$page['filename']."'></label></td>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

    function checkboxesimagesprimary($checkboxname, $colcount, $array, $primaryimage) {
        $i = 0;
        echo "<table border=1>";
        foreach ($array as $key => $page) {
            if ($i == 0) {
                echo "<tr>";
            } else {
                if ($i % $colcount == 0) {
                    echo "</tr><tr>";
                }
            }

            if (((int) $primaryimage > 0) && ( (int) $primaryimage === (int) $page['id'])) {
                echo "<td bgcolor='blue'><input type=radio id='primary$checkboxname$key' value='$key' name='imageid' checked>Primary Image";
            } else {
                echo "<td><input type=radio id='primary$checkboxname$key' value='$key' name='imageid'>Primary Image";
            }
            echo "<br><label for='checker$checkboxname$key'>Unlink</label><input id='checker$checkboxname$key' type = 'checkbox' name = '$checkboxname' value='$key'>" .
            "<img onclick=\"document.getElementById('primary$checkboxname$key').checked= true;\" src='../" . IPATH . '/' . $page['path'] . "/5/" . $page['filename'] . "'>";
            //"<img onclick=\"document.getElementById('checker$checkboxname$key').checked=(! document.getElementById('checker$checkboxname$key').checked);\" src='".$page['path']."/".$page['filename']."'>";

            echo "</td>";
            //echo "<label for='checker$checkboxname$key'><img src='".IMGPATH."$page['path']."/".$page['filename']."'></label></td>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

    function checkboxeswithid($array, $colcount, $checkboxname, $selectedids) {
        $i = 0;
        echo "<table>";
        foreach ($array as $key => $page) {
            if ($i == 0) {
                echo "<tr>";
            } else {
                if ($i % $colcount == 0) {
                    echo "</tr><tr>";
                }
            }

            if (array_key_exists($key, $selectedids)) {
                echo "<td><input id='checker$key' type = 'checkbox' checked name = '$checkboxname' value='$key'>";
            } else {
                echo "<td><input id='checker$key' type = 'checkbox' name = '$checkboxname' value='$key'>";
            }

            echo "<label for='checker$key'>$page</label></td>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

    function checkboxeswithkey($array, $colcount, $idfieldinarray, $displayfieldinarray, $checkboxname, $selectedid) {
        $i = 0;
        echo "<table border=0 cellspacing=10>";
        foreach ($array as $key => $page) {
            if ($i == 0) {
                echo "<tr>";
            } else {
                if ($i % $colcount == 0) {
                    echo "</tr><tr>";
                }
            }
            echo "<td><input id='checker" . $page['id'] . "' type = 'checkbox' name = '$checkboxname' value='" . $page['id'] . "'>" .
            "<label for='checker" . $page['id'] . "'>$key</label></td>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

    function display_products_images_list(&$site, $products = array(), $images = array(), $version) {
        ?>
        <table border=0>
    <? foreach ($products as $key => &$aproduct) { ?>
                <tr>
                    <td valign=top align=left>
        <? if (array_key_exists($aproduct['imageid'], $images)) { ?>
                            <img src='<?= '/' . IPATH . '/' . $images[$aproduct['imageid']]['path'] . "/$version/" . $images[$aproduct['imageid']]['filename']; ?>'>
        <?
        } else {
            echo "&nbsp;";
        }
        ?>
                    </td>
                    <td align=left valign=top>
                        <table border=0>
                            <tr>
        <? $did = $site->pages->pbyid[$aproduct['pageid']]['directoryid'];
        $pp = replacespaces($aproduct['name']);
        ?>
                                <td><b><a style='font-size:15px; text-decoration:underline;' 
                                          href='http://<?= $_SERVER['HTTP_HOST'] . $site->dirs->path($did) . "$pp'>$pp</a></b>&nbsp;"; ?></td>
                                          </tr>
                                          <tr><td valign=top><?= $aproduct['info']; ?></td></tr>
                                          </table>
                                          </td>
                                          <td valign=top align=right><b><font color=black size=4>R&nbsp;<?= $aproduct['price']; ?></font></b></td>
                                          </tr>
    <? } ?>
                                      </table>
<?
}

function buttonsdiv($colcount, $array) {
    $i = 0;
    foreach ($array as $key => $page) {
        if ($i == 0) {
            echo "<div style='position:relative; clear: both border:1px solid blue;'>";
        } else {
            if ($i % $colcount == 0) {
                echo "</div><div>ben";
            }
        }
        ?>
        <?
        echo "<div style='position: relative; float: left; background-color: pink; border:2px solid red;'>" . $page['name'] . "</div>";
        $i++;
    }
    echo "</div>";
}

function display_products_images_divgrid(&$site, $colcount, $products = array(), $images = array(), $version) {
    if (!is_array($products))
        return;
    if (empty($products))
        return;
    $i = 0;

    foreach ($products as $key => $product) {
        if ($i == 0) {
            ?>
                                              <div style='padding:10px; border=0px solid yellow;' align="center">
                                              <div style='border:1px solid red;padding:0px;'>
        <?
        } else {
            if ($i % $colcount == 0)
                echo "</div>";
            //echo "</tr><tr><td style='border-bottom:1px solid lightgray;font-size:4px;' colspan=$colcount>&nbsp;</td></tr><tr><td colspan=$colcount>&nbsp;</td><tr>";
        }

        $style = '';
        if ((int) $product['imageid'] > 0)
            if (array_key_exists($product['imageid'], $images)) {
                echo "<div style='position: relative; float: left; width: 240px; height: 150px; background-color: pink;'>";
                echo "<table cellpadding=0 border=0 cellspacing=0>";
                echo "<tr><td>";
                echo "<img onclick=\"alert (getimgsize('img" . $product['imageid'] . "'));\" id='img" . $product['imageid'] . "' src='/" . IPATH . "/" . $images[$product['imageid']]['path'] . "/$version/" . $images[$product['imageid']]['filename'] . "'></td></tr>";
                echo "<tr><td><div style='width:160px;'><b><a href=#>" . $product['name'] . "</a><br>R&nbsp;" . $product['price'] . "</b></div></td></tr>";
                echo "</table></div>";
            } else {
                echo "<td><img style='padding:1px; border:0px solid #021a40;' onclick=\"document.getElementById('checker$checkboxname$key').checked=(! document.getElementById('checker$checkboxname$key').checked);\" src='" . IPATH . "/" . $page['path'] . "/$version/" . $page['filename'] . "'></td>";
            }
        //echo "<label for='checker$checkboxname$key'><img src='".$page['path']."/".$page['filename']."'></label></td>";
        $i++;
    }
    $emptytds = $colcount - (int) ($i % $colcount);
    if ((int) ($i % $colcount) > 0) {
        echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
    }
    echo "</tr>";
}

function display_products_images_grid(&$site, $colcount, $products = array(), $images = array(), $version) {
    if (!is_array($products))
        return;
    if (empty($products))
        return;
    $i = 0;
    echo "<table border=0>";
    foreach ($products as $key => $product) {
        if ($i == 0) {
            echo "<tr>";
        } else {
            if ($i % $colcount == 0)
                echo "</tr><tr><td style='border-bottom:2px solid lightgray;' colspan=$colcount>&nbsp;</td></tr>";
        }
        $did = $site->pages->pbyid[$product['pageid']]['directoryid'];
        $style = '';
        if ((int) $product['imageid'] > 0)
            if (array_key_exists($product['imageid'], $images)) {
                echo "<td valign=top>";
                echo "<table cellpadding=0 border=0 cellspacing=0>";
                echo "<tr><td>";
                ?>
                                                        <a href='http://<?= $_SERVER['HTTP_HOST'] . $site->dirs->path($did); ?>'><?=
                "<img onclick=\"alert (getimgsize('img" . $product['imageid'] . "'));\" id='img" . $product['imageid'] . "' src='/" . IPATH . "/" . $images[$product['imageid']]['path'] . "/$version/" . $images[$product['imageid']]['filename'] . "'></a></td></tr>";
                echo "<tr><td><div style='width:160px; margin:0; padding:0;'><b><a href=# style='margin:0; padding:0;'>" . $product['name'] . "</a><br>R&nbsp;" . $product['price'] . "</b></div></td></tr>";
                echo "</table></td>";
            } else {
                echo "<td><img style='padding:1px; border:0px solid #021a40;' onclick=\"document.getElementById('checker$checkboxname$key').checked=(! document.getElementById('checker$checkboxname$key').checked);\" src='" . IPATH . "/" . $page['path'] . "/$version/" . $page['filename'] . "'></td>";
            }
        //echo "<label for='checker$checkboxname$key'><img src='".$page['path']."/".$page['filename']."'></label></td>";
        $i++;
    }
    $emptytds = $colcount - (int) ($i % $colcount);
    if ((int) ($i % $colcount) > 0) {
        echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
    }
    echo "</tr>";
    echo "</table>";
}

function display_product_images($aproduct = array(), $aimages = array()) {
    ?>
                                                <table border=0 cellspacing=7>
                                                    <tr>
                                                        <td valign=top align=left>
                                                            <img src='<?= IPATH . '/' . $aimagesge['path'] . '/' . $firstnewimage['filename']; ?>'?></b></td>
                                                        <td align=left valign=top>
                                                            <table>
                                                                <tr><td><b><?= $aproduct['name'] . "</b>&nbsp;<i>(" . $aproduct['datetimef'] . ")</i>"; ?></td></tr>
                                                                <tr><td><b>R&nbsp;</b><?= $aproduct['price']; ?></td></tr>
                                                                <tr><td><pre><?= $aproduct['info']; ?></pre></td></tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
<?
}

function checkboxeswithoutkey($array, $colcount, $idfield, $displayfield, $checkboxname, $selectedcid) {
    $i = 0;
    echo "<table border=0 cellspacing=10>";
    foreach ($array as $page) {
        if ($i == 0) {
            echo "<tr>";
        } else {
            if ($i % $colcount == 0) {
                echo "</tr><tr>";
            }
        }
        //if ($page[$idfield] == $selectedcid) {
        echo "<td><input type = 'checkbox' name = '$checkboxname' value='" . $page[$idfield] . "'>" .
        $page[$displayfield] . "</td>";
        $i++;
    }
    $emptytds = $colcount - (int) ($i % $colcount);
    if ((int) ($i % $colcount) > 0) {
        echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
    }
    echo "</tr>";
    echo "</table>";
}
