<?php
	define('BASE_DIR', __DIR__);
	include_once BASE_DIR . '/config/constants.php';
	include_once BASE_DIR . '/lib/Auth.class.php';
	include_once BASE_DIR . '/lib/Template.class.php';
	session_start();
	// Authentication is standard across whole system
	$auth = new Auth();
	global $auth;
	$auth->authenticate();