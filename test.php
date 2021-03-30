<?php
	include_once('bootstrap.php');
	global $auth;
	$t = new Template('NanoCenter Scheduler | Testing Authentication', 0);
	$t->printHTMLHeader();
	$t->startMain();
	if ($auth->is_logged_in) {
		echo '<p>Logged In!<p>';
	} else {
		echo '<p>Not Logged In!<p>';
	}
	if (!$auth->isLoggedIn()) {
		Link::doLink($_SERVER['PHP_SELF'] . '?login=login', 'Login');
	}
	$t->endMain();
	$t->printHTMLFooter();
	