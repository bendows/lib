<?

class object {

    function __construct($varnames = array()) {
				//chassnames is a list of page objects inherrited
        $classnames[] = $parent_class_name = get_class($this);
        while ($parent_class_name = get_parent_class($parent_class_name))
            if ($parent_class_name !== "object")
                $classnames[] = $parent_class_name;
				foreach ($classnames as $classname)
        $mergedvars = $varnames;
				//iterate through all classes and merge their mergedvars 
        foreach ($classnames as $classname) {
            $classvars = get_class_vars($classname);
            foreach ($varnames as $mergedvar) {
                if (!isset($classvars[$mergedvar]))
                    continue;
                //looping through all models, components or helpers
                foreach ($classvars[$mergedvar] as $key => $data) {
                    if (is_array($data))
                        $mergedvars[$mergedvar][$key] = $data;
                    else
                        $mergedvars[$mergedvar][$data] = array();
                }
            }
        }
        unset($classnames);
        // merge models for page
				if (isset($mergedvars['models']))
        foreach ($mergedvars['models'] as $model => $dummy)
            if (!in_array($model, $this->models))
                $this->models[] = $model;
        // merge components for page
        $this->components = $mergedvars['components'];
        // merge helpers for page
				if (isset($mergedvars['helpers']))
        foreach ($mergedvars['helpers'] as $helper => $dummy)
            if (!in_array($helper, $this->helpers))
                $this->helpers[] = $helper;
    }

}

/*

  function dispatchMethod($method, $params = array()) {
  switch (count($params)) {
  case 0:
  return $this->{$method}();
  case 1:
  return $this->{$method}($params[0]);
  case 2:
  return $this->{$method}($params[0], $params[1]);
  case 3:
  return $this->{$method}($params[0], $params[1], $params[2]);
  case 4:
  return $this->{$method}($params[0], $params[1], $params[2], $params[3]);
  case 5:
  return $this->{$method}($params[0], $params[1], $params[2], $params[3], $params[4]);
  default:
  return call_user_func_array(array(&$this, $method), $params);
  break;
  }
  }
  }
 */
?>
