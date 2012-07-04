<?
class lib_page_maintain extends lib_page_page {
	var $autorender = true;
  var $layout = "home_layout";
	var $viewfile = "home_view";

	function beforerender() {
		echo "this is mvplib<br>This is the test the page<br>";
		echo "<a href='http://jonas.nitro.dk/git/quick-reference.html'>jonas</a><br>";
		echo "<a href='http://www.gitguys.com/topics/head-where-are-we-where-were-we/'>gitguys</a><br>";
	}
}
?>
