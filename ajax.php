<?php
	include_once('bootstrap.php');
	include_once(BASE_DIR . '/lib/User.class.php');
	global $auth;
	
	$a = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);
	switch ($a) {
		case 'gua' :
			$user_id = filter_input(INPUT_GET,'i', FILTER_SANITIZE_STRING);
			$user = new User($user_id);
			$accts = $user->getAccountsList();
			//header('Content Type: application/json');
			echo json_encode($accts);
			//exit(0);
		case 'addUserResourceFilter' :
			$user_id = filter_input(INPUT_GET,'i', FILTER_SANITIZE_STRING);
			$user = new User($user_id);
			$user->addUserResourceFilter(filter_input(INPUT_GET,'machid', FILTER_SANITIZE_STRING));
			echo 'add completed';
			//exit(0);
		case 'removeUserResourceFilter' :
			$user_id = filter_input(INPUT_GET,'i', FILTER_SANITIZE_STRING);
			$user = new User($user_id);
			$user->removeUserResourceFilter(filter_input(INPUT_GET,'machid', FILTER_SANITIZE_STRING));
			echo 'remove completed';
			//exit(0);
		case 'getUserAccounts' :
			$user_id = filter_input(INPUT_GET,'user_id', FILTER_SANITIZE_STRING);
			$user = new User($user_id);
			$accounts = $user->getAccountsList();
			if (!is_array($accounts)) {
				$accounts = array();
			}
			//header('Content Type: application/json');
			echo json_encode($accounts);
			//exit(0);
		default :
			echo 'No Action Requested';
			//exit(0);
	}