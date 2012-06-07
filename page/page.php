<?
	class lib_page_page extends object {

		var $models=array();
		var $components=array();
		var $helpers=array();
		var $pagename = false;

    var $layout = false;
  	var $viewfile = false;
  	var $viewclass = false;

    var $autorender = false;

    var $viewvars = array();
    var $output = null;

		private $objects=array(
			'models'=>array(),
			'components'=>array(),
			'helpers'=>array()
		);

    function __construct () {
      parent::__construct (array("models", "components", "helpers"));
			$this->pagename = get_class($this);
    }

    function ismodel($amodelname){
      return in_array ($amodelname, $this->models);
    }

    function iscomponent($acomponentname){
      return array_key_exists ($acomponentname, $this->components);
    }

    function model($amodelname='') {
      if (empty($amodelname))
        return;
      if (! in_array($amodelname, $this->models))
        return;
      $classname = "app_model_{$amodelname}";
      if ($this->objects['models'][$amodelname] instanceof $classname)
        return $this->objects['models'][$amodelname];
      $this->objects['models'][$amodelname]=& new $classname();
      return $this->objects['models'][$amodelname];
    }

    function component($acomponentname='', $acomponentsettings=array()) {

      if (empty($acomponentname))
        return;

      if (! array_key_exists($acomponentname, $this->components))
        return;

      $classname = "lib_component_{$acomponentname}";

      if ($this->objects['components'][$acomponentname] instanceof $classname)
        return $this->objects['components'][$acomponentname];

      $this->objects['components'][$acomponentname] =& new $classname($acomponentsettings, $this);
        return $this->objects['components'][$acomponentname];
    }

    function initialize() {
    }

    function beforerender() {
    }

    function render($layout = null, $file = null) {

      $this->beforerender();

      if ($layout == null)
        $layout = $this->layout;

      if ($file == null)
        $file = $this->viewfile;

      $viewclass = "lib_view";

      if ($this->viewclass)
        $viewclass = "app_view_{$this->viewclass}";

      $view =& new $viewclass ($this);
      echo $view->render($layout, $file);
    }

    function shutdown () {
    }
}
?>
