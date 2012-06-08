<?

class lib_helper_html extends object {

    private $theview = false;

    function __construct(&$aview) {
        $this->theview = $aview;
    }

    function scripts($scripts = array()) {
        if (empty($scripts))
            return;
        foreach ($scripts as $script => $data)
            if (empty($data))
                $this->script($script);
            else
                $this->script($script, $data);
    }

    function script($h = false, $media = "") {
        if (!$h)
            return;
        if ($media)
            $media = "media='screen'";
        if (!is_array($h))
            $h = array($h);
        foreach ($h as $he) {
            switch (true) {
                case (preg_match("/css$/", $he)):
                    $this->theview->scripts[] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$he}\" $media/>";
                    break;
                case (preg_match("/js$/", $he)):
                    $this->theview->scripts[] = "<script type=\"text/javascript\" src=\"{$he}\"></script>";
                    break;
            }
        }
    }

}
?>
