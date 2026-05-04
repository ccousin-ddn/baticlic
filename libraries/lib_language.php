<?php
	// Choix de la langue
	if(isset($_GET['language']) || isset($_POST['language'])){
		$tmp_lang = $_GET['language']??$_POST['language'];
		if (in_array($tmp_lang, array("fr","nl","en"))) {
			$lang = $tmp_lang;
		}else{
			$lang = "fr";
		}
		$_SESSION['language'] = $lang;
	}elseif(!isset($_SESSION['language'])){
		$lang = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$_SESSION['language'] = substr($lang,0,2);
	}else{
		$lang = $_SESSION['language'];
		$uri = $_SERVER['REQUEST_URI'];
		/*
		// check if language in url
		$re = '/\/([a-z]{2})\//i';
		if($uri == SITE_DIRECTORY.DIRECTORY_SEPARATOR){
			header("location:".SITE_DIRECTORY.DIRECTORY_SEPARATOR.$_SESSION['language'].DIRECTORY_SEPARATOR);
		}
		*/
	}
	define('LANG', $_SESSION['language']);
//	switch($lang){
//		case "fr" : case "fr_FR" : case "fr_BE" : $lang = "fr_BE"; define('DEC_POINT',','); define('THOUSANDS_SEP','.'); break;
//		case "nl" : case "nl_NL" : case "nl_BE" : $lang = "nl_BE"; define('DEC_POINT',','); define('THOUSANDS_SEP','.'); break;
//		case "en" : case "en_GB" : case "en_BE" : $lang = "en_GB"; define('DEC_POINT','.'); define('THOUSANDS_SEP',' '); break;
//		default : $lang = "fr_BE"; define('DEC_POINT','.'); define('THOUSANDS_SEP',' '); break;
//	}

    $lang = "fr_BE";
    define('DEC_POINT',',');
    if (!defined('THOUSANDS_SEP')) define('THOUSANDS_SEP',' ');
	define('LOCALE',$lang);
	putenv('LC_ALL='.$lang);
	putenv("LANG={$lang}");
	putenv("LANGUAGE={$lang}");
	setlocale(LC_ALL, $lang.'.utf8');
	setlocale(LC_ALL, $lang.'.UTF8');
	setlocale(LC_ALL, $lang.'.utf-8');
	setlocale(LC_ALL, $lang.'.UTF-8');
	
	// Spécifie la localisation des tables de traduction
	bindtextdomain($lang, PHP_ROOT."language");
	textdomain($lang);
	bind_textdomain_codeset($lang, 'UTF-8');