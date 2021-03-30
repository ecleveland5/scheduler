<?php
	include_once('bootstrap.php');
	global $auth;
	$t = new Template();
	$t->printHTMLHeader();
	
	if ($auth->isLoggedIn()) {
		CmnFns::redirect('ctrlpnl.php', 1);
	} else {
		$auth->printLoginForm($auth->login_msg);
	}
	
	$t->printHTMLFooter();
	