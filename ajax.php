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
			echo json_encode($accts);
			break;
		case 'addUserResourceFilter' :
			$user_id = filter_input(INPUT_GET,'i', FILTER_SANITIZE_STRING);
			$user = new User($user_id);
			$user->addUserResourceFilter(filter_input(INPUT_GET,'machid', FILTER_SANITIZE_STRING));
			echo 'add completed';
			break;
		case 'removeUserResourceFilter' :
			$user_id = filter_input(INPUT_GET,'i', FILTER_SANITIZE_STRING);
			$user = new User($user_id);
			$user->removeUserResourceFilter(filter_input(INPUT_GET,'machid', FILTER_SANITIZE_STRING));
			echo 'remove completed';
			break;
		case 'getUserAccounts' :
			$user_id = filter_input(INPUT_GET,'user_id', FILTER_SANITIZE_STRING);
			$user = new User($user_id);
			$accounts = $user->getAccountsList();
			echo json_encode($accounts);
			break;
		default :
			echo 'No Action Requested';
			break;
	}