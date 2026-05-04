<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	// Define
	define('SESSION_NAME','LPDN');
	define('SITE_DIRECTORY','/lpdn');
	define('SITE_URL',$_SERVER['SERVER_NAME'].SITE_DIRECTORY);
	define('PHP_ROOT',$_SERVER['DOCUMENT_ROOT'].SITE_DIRECTORY.DIRECTORY_SEPARATOR);
	define('SITE_ROOT',dirname(PHP_ROOT).DIRECTORY_SEPARATOR);
	
	// Config
	require_once(SITE_ROOT.'config_'.strtolower(SESSION_NAME).'.php');
	
	// Functions
	require_once('lib_functions.php');
	// Autoloader
	require_once('lib_autoloader.php');
	// Session
	require_once('lib_session.php');
	
	// Debug
	$_SESSION['debug'] = true;
	
	if($_SESSION['debug']){
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		ini_set('xdebug.collect_params', '3');
	}else{
		error_reporting(0);
		ini_set('display_errors', '0');
	}
	
	// Language
	require_once('lib_language.php');
	
	// vendor (composer)
	require_once (dirname(__FILE__)."/../vendor/autoload.php");
	
	// During development
	//$_SESSION['iduser'] = 1;
	//$_SESSION['usr_level'] = 2;
	//$_SESSION['usr_firstname'] = "Laurent";