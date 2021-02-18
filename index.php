<?php
	include_once('bootstrap.php');
	global $auth;
	$t = new Template();
	$t->printHTMLHeader();
	
	// Print out logoImage if it exists
	echo (!empty($conf['ui']['logoImage']))
		? '<div align="left"><img src="' . $conf['ui']['logoImage'] . '" alt="logo" vspace="5"/></div>'
		: '';
	
	CmnFns::redirect('ctrlpnl.php', 1);
	
	$t->printHTMLFooter();
	