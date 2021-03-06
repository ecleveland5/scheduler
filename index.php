<?php
/**
* This file is the login page for the system
* It provides a login form and will automatically
* forward any users who have cookies set to ctrlpnl.php
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 06-25-04
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Include Template class
*/
include_once('lib/Template.class.php');

// Auth included in Template.php
$auth = new Auth();
$t = new Template();
$msg = '';

$resume = (isset($_POST['resume'])) ? $_POST['resume'] : '/scheduler/ctrlpnl.php';
$_SESSION['resume'] = $resume;
// Logging user out
if (isset($_GET['logout'])) {
    $auth->doLogout();   
}
else if (isset($_POST['login'])) {
	$msg = $auth->doLogin($_POST['email'], $_POST['password'], (isset($_POST['setCookie']) ? 'y' : null), false, $resume, (isset($_POST['language']) ? 'y' : null));
}
else if (isset($_COOKIE['nanocenterID'])) {
    $msg = $auth->doLogin('', '', 'y', $_COOKIE['nanocenterID'], $resume);  	// Check if user has cookies set up. If so, log them in automatically 
}

$t->printHTMLHeader();

// Print out logoImage if it exists
echo (!empty($conf['ui']['logoImage']))
		? '<div align="left"><img src="' . $conf['ui']['logoImage'] . '" alt="logo" vspace="5"/></div>'
		: '';

$t->startMain();

if (isset($_GET['auth'])) {
	$auth->printLoginForm(translate('You are not logged in!'), $_GET['resume']);
}
else {
	$auth->printLoginForm($msg);
}

$t->endMain();
// Print HTML footer
$t->printHTMLFooter();
?>