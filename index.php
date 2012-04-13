<?php

//error_reporting(E_WARNING);

require('config.SECURE.inc.php');

session_start();
if ( isset($_GET['clear']) ) { session_destroy(); session_start(); }
$db = new Mysqli($db['server'],$db['user'],$db['pass'],$db['db'],$db['port']);

// Restore Login and token sessions.
if ( !empty($_SESSION['userid']) && !empty($_SESSION['session']) ) {
	$q = $db->query("select * from `users` where `id` = '".$_SESSION['userid']."' and `session` = '".$_SESSION['session']."'");
	
	if ( $q->num_rows > 0 ) {
		$data = $q->fetch_assoc();
		$_SESSION['access_token'] = 
		array( 
			"oauth_token" => $data['tw_token'],
			"oauth_token_secret" => $data['tw_secret']
		);
	}
	else {
		unset($_SESSION['userid']);
		unset($_SESSION['session']);
	}
}

if ( !empty($_COOKIE['portall_session']) ) {
	$q = $db->query("select * from `users` where `session` = '".$_COOKIE['portall_session']."'");

	if ( $q->num_rows > 0 ) {
		$data = $q->fetch_assoc();
		$_SESSION['access_token'] = 
		array( 
			"oauth_token" => $data['tw_token'],
			"oauth_token_secret" => $data['tw_secret']
		);
		$_SESSION['userid'] = $data['id'];
		$_SESSION['session'] = $_COOKIE['portall_session'];
		setcookie("portall_session",$_SESSION['session'],time()+60*60*24,'/','portall.eu5.org');
	}
	else {
		setcookie("portall_session", "", time()-3600,'/','portall.eu5.org');
	}
}

@$page = explode("/",$_SERVER['PATH_INFO']);
if ( empty($page[1]) ) $page[1] = "index";

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