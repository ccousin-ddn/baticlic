<?php
	ignore_user_abort(true);
	include_once 'libraries/lib_include.php';
	$user = new usr_user();
	$user->c_disconnected();
	
	$_SESSION = array();
	if (ini_get('session.use_cookies'))
	{
		$p = session_get_cookie_params();
		setcookie(session_name(), '', time() - 31536000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
	}
	session_unset();
	session_destroy();
	delCookie("idcrypt");
	header("location:".$_SERVER['HTTP_REFERER']);
