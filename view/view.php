<?php

class lib_view extends object {

    var $viewfile = false;
    var $layout = false;
    var $input = array();
    var $viewvars = array();
    var $autorender = true;
    var $autolayout = true;
    var $hasrendered = false;
    var $output = false;
    var $pagevars = array('hostname', 'title', 'remoteip', 'here', 'referer', 'viewvars', 'viewfile', 'layout');
    var $scripts = array();

    function __construct(&$page) {
        if (!is_object($page))
            echo "Page[$page] is not a page object";
        //Suck in some variables from the page object
        foreach ($this->pagevars as $var)
            if (isset($page->{$var}))
                $this->{$var} = $page->{$var};
        if (is_array($page->helpers))
            if (count($page->helpers))
                foreach ($page->helpers as $helper) {
                    $classname = "lib_helper_{$helper}";
                    $this->{$helper} = & new $classname($this);
                }
    }

    function addscript($a = '') {
        if (in_array($a, $this->scripts))
            return;
        $this->scripts[] = $a;
    }

    function flash($key = false) {
        if (!$key)
            return;
        if (empty($key))
            return;
        if (!is_scalar($key))
            return;
        if (!array_key_exists($key, $_SESSION))
            return;
        $msg = $_SESSION[$key];
        $_SESSION[$key] = "";
        return $msg;
    }

    function render($layout = null, $file = null) {
        $out = null;
        $viewoutput = $this->_render("app/views/$file", $this->viewvars);
        $pageoutput = $this->renderlayout("app/views/layouts/$layout", $viewoutput);
        $this->hasrendered = true;
        return $pageoutput;
    }

    function renderlayout($layoutfilename = null, $content) {
        $data = array_merge(
            $this->viewvars, array(
                'content' => $content,
                'headers' => implode("\t\r\n", $this->scripts)
            )
        );
        return $this->_render($layoutfilename, $data);
    }

    function _render($filename, $data) {
        $filename = $filename . ".php";
        extract($data, EXTR_SKIP);
        ob_start();
        if (!file_exists($filename))
            echo "filename $filename does not exist";
        else
            include ($filename);
        return ob_get_clean();
    }

    function element($elementfilename, $data = array()) {
        $element = $this->_render("app/views/elements/{$elementfilename}", array_merge($this->viewvars, $data));
        return $element;
    }

}?>
