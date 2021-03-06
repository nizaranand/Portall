<?php

require('config.SECURE.inc.php');
require('functions.php');

session_start();
if ( isset($_GET['clear']) ) { session_destroy(); session_start(); }

// Restore Login and token sessions.
if ( !empty($_SESSION['userid']) && !empty($_SESSION['session']) ) {
	$q = $db->query("select * from `users` where `id` = '".$_SESSION['userid']."' and `session` = '".$_SESSION['session']."'");

	if ( $q->num_rows > 0 ) restoreLogin($q->fetch_assoc());
	else {
		unset($_SESSION['userid']);
		unset($_SESSION['session']);
	}
}

if ( !empty($_COOKIE['portall_session']) ) {
	$q = $db->query("select * from `users` where `session` = '".$_COOKIE['portall_session']."'");

	if ( $q->num_rows > 0 ) restoreLogin($q->fetch_assoc());
	else setcookie("portall_session", "", time()-3600,'/','portall.eu5.org');
}

/* Load Preferences */
$q = $db->query("select * from `prefs` where `id` = '".$_SESSION['userid']."'");
$prefs = $q->fetch_assoc();

if ( !empty($prefs['timezone']) ) date_default_timezone_set($prefs['timezone']);
else date_default_timezone_set('Europe/Amsterdam');

@$page = explode("/",$_SERVER['PATH_INFO']);
if ( empty($page[1]) ) $page[1] = "index";

if ( $maintance && $page[1] !== "maintaince" ) header('Location: /maintaince');

switch ( $page[1] ) {
	case ( file_exists('lib/php/'.$page[1].'.php') ):
		require('lib/php/'.$page[1].'.php');
		break;
	default:
		require('lib/php/notfound.php');
		break;
}

require('lib/php/layout.php');

?>