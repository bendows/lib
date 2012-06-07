<?

	class settings {

		private $conf=array();

  		function &getinstance() {
			static $instance = array();
    		if (!$instance) {
      			$instance[0] =& new settings();
				if (file_exists('app/conf/settings.php'))
					require('app/conf/settings.php');
				else echo "settings.php not included";
				$instance[0]->conf = $siteconf;
		 	}
    	return $instance[0];
		}

		function get($key=false) {
		
			if (! $key)
				return $key;
				
    	$self =&settings::getinstance();
			
			if (empty($self->conf))
				return "no value";
								
			if (! array_key_exists($key, $self->conf))
				return "no value";
				
			return $self->conf[$key];
			
		}
	}

?>
