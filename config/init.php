<?php
	global $conf;
	
	//ini_set('include_path', ( __DIR__ . '/../lib/'));
	//ini_set('session.name', $conf['app']['sessionName']);
	ini_set('session.use_strict_mode', 1);
	ini_set('session.cookie_httponly',1);
	ini_set('session.use_only_cookies',1);
	ini_set('session.gc_probability',1);
	ini_set('session.cookie_secure',1);
	ini_set('session.sid_length',250);
	
	switch ($conf['app']['debugMode']) {
		case 1:
		case 3:
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
			break;
		default:
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
			error_reporting(0);
	}
	
	include_once('constants.php');
	include_once('langs.php');
	include_once(__DIR__ . '/../lib/CmnFns.class.php');

	if ($lang = determine_language()) {
		set_language($lang);
		load_language_file();
	}
	
	CmnFns::setTimezone('America/New_York');
	
