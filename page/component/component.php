<?

class lib_component_component {

    function __construct($data = array(), &$apage = false) {
        if (is_array($data))
            if (!empty($data))
                foreach ($data as $key => $val)
                    $this->{$key} = $val;
        $this->page = $apage;
    }

}
?>
