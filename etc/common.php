<?php
	// Database stuff
	require_once(__DIR__ . "/connect.php");

	// Force HTTPS, also works with Cloudflare
	if($_SERVER["SERVER_PORT"] == 80){
		if($_SERVER['HTTP_X_FORWARDED_PROTO'] == "http"){
			header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
			exit;
		}
	} else{
		if($_SERVER['HTTPS'] != "on"){
			header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
			exit;
		}
	}

	define("DEBUG", (isset($_COOKIE['debug'])));

	// Find client language
	if(!isset($_COOKIE['lang']))
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	else
		$lang = $_COOKIE['lang'];
	if(!in_array($lang, ['da', 'en']))
		$lang = "en";

	if(DEBUG){
		error_log("POST: ".var_export($_POST, true));
		error_log("GET: ".var_export($_GET, true));
		error_log("SESSION: ".var_export($_SESSION, true));
	}

	// Plugins
	require_once __DIR__ . "/plugins/Html2Text/Html2Text.php";
	require_once __DIR__ . "/plugins/Mobile-Detect-2.8.24/Mobile_Detect.php";
	require_once __DIR__ . "/plugins/PHPMailer/PHPMailerAutoload.php";

	// Wrappers
	require_once __DIR__ . "/wrappers/email.php";

	// Wrappers
	require_once __DIR__ . "/classes/Content.php";
	$Content = new Content($con, $lang);


	// Henter variabler fra URL
	if(!empty($_GET['domain'])){
		$d = $Content->clean($_GET['domain']);
		if(!empty($_GET['mail'])){
			$m = $Content->clean($_GET['mail']);
		}
	}


	// Constants
	define('HOME', '/');
	define('ASSETS', HOME.'assets/');
	define('CSS', ASSETS.'css/');
	define('JS', ASSETS.'js/');
	define('PLUGINS', ASSETS.'plugins/');
	define('IMG', ASSETS.'img/');
	define('SCRIPTS', HOME.'scripts/');
	define('ROOT', $_SERVER['DOCUMENT_ROOT']);
	if(!isset($_SESSION['login']))
		$_SESSION['login'] = false;

	// Check device type
	$mobile = new Mobile_Detect();
	define("IOS", 			$mobile->isiOS() ? true : false);
	define("ANDROID", 		$mobile->isAndroidOS() ? true : false);
	define("MOBILE", 		$mobile->isMobile() ? true : false);
	define("TABLET", 		$mobile->isTablet() ? true : false);
	define("onlyScreen",	(TABLET || MOBILE || IOS) ? false : true);

	// Check client browser
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == false){
		define("CHROME", (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false || strpos($_SERVER['HTTP_USER_AGENT'], "Google Page Speed") !== false) ? true : false);
		define("FIREFOX", (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false) ? true : false);
	} else{
		define("CHROME", false);
		define("FIREFOX", false);
	}
?>