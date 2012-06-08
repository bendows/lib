<?

class lib_component_formfilter extends lib_component_component {

    function __construct($data, &$apage) {
        
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

                case "float":

                    (boolean) $check = isfloat($src[$key]);

                    if ($check) {
                        $ar[$key] = (float) $src[$key];
                    } else {
                        $errors.=" Invalid znumber [$key] ";
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

                    stripleadingtrailing($src[$key]);
                    $src[$key] = replacespaces($src[$key]);

                    (boolean) $check = isdirname($src[$key]);

                    if ($check) {
                        $ar[$key] = $src[$key];
                    } else {
                        $errors.=" Invalid dirname [$key] ";
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

    function isint($n) {
        if (preg_match("/^[1-9][0-9]*$/", $n)) {
            return (boolean) true;
        }
        return false;
    }

    function iszint($n) {
        if (preg_match("/^[0-9]+$/", $n)) {
            return (boolean) true;
        }
        return false;
    }

    function isfloat($v2) {
        if (preg_match("/^[0-9]+\.?[0-9]+$/i", $v2)) {
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

    function isdatetime($v2) {
        if (preg_match("/^2\d{3}\-\d\d\-\d\d\ \d\d\:\d\d\:\d\d$/", $v2))
            return (bool) true;
        return (bool) false;
    }

    function isname(&$v2) {
        $v2 = htmlspecialchars($v2, ENT_QUOTES, 'UTF-8');
        if (empty($v2))
            return false;
        return true;
        if (preg_match("/^([A-Z]|[0-9]|\_)+$/i", $v2)) {
            return true;
        }
        return false;
        //if (preg_match("/^([A-Z]|[0-9]|\.|,|\(|\)|\+|\@|\-|\_|~|\ \.)+$/i", $v2)){ return true; }
    }

    function isuid($v2) {
        if (preg_match("/^([A-Z]|[0-9]|\.|,|\+|\@|\-|\_|~|\.)+$/i", $v2)) {
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

}
