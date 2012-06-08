<?

#ini_set('include_path', ini_get('include_path').':..');

require_once('lib/functions.php');
require_once('lib/dispatcher.php');
require_once('lib/classloader.php');

dispatcher::$_url = $_SERVER['REQUEST_URI'];

$arouter = dispatcher::make_router();

dispatcher::$_pagename = $arouter->getpagename();

$apage = dispatcher::make_page();

//the page run :)

$apage->initialize();

if ($apage->autorender)
    $apage->render();

$apage->shutdown();
?>
