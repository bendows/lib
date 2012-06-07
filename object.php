<?

class object {

  function __construct ($varnames = array()) {

    $classnames[] = $parent_class_name = get_class($this);

    while($parent_class_name = get_parent_class($parent_class_name))
      if ($parent_class_name !== "object")
        $classnames[]=$parent_class_name;

    $mergedvars = array(
    'models'=>array(),
    'components'=>array(),
    'helpers'=>array()
    );

    foreach ($classnames as $classname) {

      $classvars = get_class_vars ($classname);

      foreach ($varnames as $mergedvar) {

        if (! isset ($classvars[$mergedvar]))
          continue;

        //looping through all models, components or helpers
        foreach ($classvars[$mergedvar] as $key=>$data) {

          if (is_array($data))
            $mergedvars[$mergedvar][$key] = $data;
          else
            $mergedvars[$mergedvar][$data] = array();
        }

      }
    }
    unset ($classnames);

    // merge models for page
    foreach ($mergedvars['models'] as $model=>$dummy)
      if (! in_array($model, $this->models))
        $this->models[]=$model;

    // merge components for page
    $this->components = $mergedvars['components'];

    // merge helpers for page
    foreach ($mergedvars['helpers'] as $helper=>$dummy)
      if (! in_array($helper, $this->helpers))
        $this->helpers[]=$helper;
	}
}
/*

	//require_once ("php.ini.php");
	//require_once ('functions.php');

	class Object {

		function __construct() {
    		$this->name  = get_class($this);
  		}

 		function &loadobjects($path=false, $classnames=array()) {

			if (! $path)
				return false;

			if (empty($classnames))
				return false;

			$objects=array();
    
			foreach (array_keys(array_flip($classnames)) as $classname) {
				if (! app::import("$path", $classname))
        			continue;
				$objects[$classname] =& new $classname();
			}
    		return $objects;
		}

/**
 * Allows setting of multiple properties of the object in a single line of code.  Will only set
 * properties that are part of a class declaration.
 *
 * @param array $properties An associative array containing properties and corresponding values.
 * @return void
 * @access protected
		function _set($properties = array()) {
    		if (is_array($properties) && !empty($properties)) {
      			$vars = get_object_vars($this);
      			foreach ($properties as $key => $val)
        			if (array_key_exists($key, $vars))
          				$this->{$key} = $val;
    		}
  		}

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
