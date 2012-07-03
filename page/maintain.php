<?
class lib_page_maintain extends lib_page_page {
	var $autorender = true;
  var $layout = "home_layout";
	var $viewfile = "home_view";

	function beforerender() {
		echo "this site is up for maintenance";
	}
}
?>
