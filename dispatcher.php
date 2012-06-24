<?

class dispatcher {

    public static $_url;
    public static $_pagename;

    public static function make_router() {
        $router = new app_router_default();
				 if ($router->maintenance)
        	$router->seturl($router->maintenance);
				 else
        	$router->seturl(self::$_url);
        // more injection...
        return $router;
    }

    public static function make_page() {
        $page = new self::$_pagename;
        // more injection...
        return $page;
    }

}
?>
