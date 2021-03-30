<?php
/**
* Interface form for accepting/declining reservations
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 03-16-05
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
include_once('lib/Reservation.class.php');

$t = new Template('Manage Invites');

if (Auth::isLoggedIn() && (isset($_POST['y']) || isset($_POST['n'])) ) {
	CmnFns::redirect('ctrlpnl.php', 1, false);
}

$t->printHTMLHeader();

if (Auth::isLoggedIn()) {
	$t->printWelcome();
}
$t->startMain();

if (isset($_POST['y'])) {
	// Process the reservation 
	if (isset($_GET['id']) && isset($_GET['user_id']) && isset($_GET['action'])) {
		global $conf;
		$user_id = $_GET['user_id'];
		$resid = $_GET['id'];
		$action = $_GET['action'];
		$accept_code = $_GET['accept_code'];
		
		// Get the user
		$user = new User($user_id);
		if ($user == null) {
			CmnFns::do_error_box(translate('Sorry, we could not find that user in the database.'), '', false);
		}
		else {
			// Get the Reservation
			$res = new Reservation($resid);
	
			$found_user = false;
			$owner_id = false;
			for ($i = 0; $i < count($res->users); $i++) {
				if ($res->users[$i]['user_id'] == $user_id && $res->users[$i]['accept_code'] == $accept_code) { $found_user = true; }
				if ($res->users[$i]['owner'] == 1) { $owner_id = $res->users[$i]['user_id']; }
			}
	
			if ($found_user && $owner_id !== false) {
				$translate_index = 'reservation ' . (($action == INVITE_ACCEPT) ? 'accepted' : 'declined');
				// Update the invite record
				$res->updateUsers($user_id, $action, isset($_POST['update_all']));
				// Let the owner know the user accepted/declined
				$owner = new User($owner_id);
				
				$mailer = new PHPMailer();		
				$mailer->From = $conf['app']['adminEmail'];
				$mailer->FromName = $conf['app']['title'];
				$mailer->Subject =  translate($translate_index, array($user->getFullName(), CmnFns::formatDate($res->start_date)));
				$mailer->IsHTML(false);
				
				$mailer->AddAddress($owner->getEmail());
				$mailer->Body = translate($translate_index, array($user->getFullName(), CmnFns::formatDate($res->start_date)));
				$mailer->Send();
				
				$msg = '';
				$msg .= translate($translate_index, array($user->getFullName(), CmnFns::formatDate($res->start_date))) . '<br/>';
				if (Auth::isLoggedIn()) {
					$msg .= Link::getLink('ctrlpnl.php', translate('Return to My Control Panel'));
				}	
				else {
					$msg .= Link::getLink('index.php', translate('Login to manage all of your invitiations'));
				}
				
				CmnFns::do_message_box($msg);	
			}
			else {
				CmnFns::do_error_box(translate('That record could not be found.'), '', false);
			}
		}
	}
	else {
		CmnFns::do_error_box(translate('No invite was selected'), '', false);
	}
}
else if (isset($_POST['n'])) {
	if (Auth::isLoggedIn()) {
		$msg = Link::getLink('ctrlpnl.php', translate('Return to My Control Panel'));
	}	
	else {
		$msg = Link::getLink('index.php', translate('Login to manage all of your invitiations'));
	}
	CmnFns::do_message_box($msg);
}
else {
	$resid = $_GET['id'];
	$res = new Reservation($resid);
	$msg = '<h5>' . translate('Confirm reservation participation') . '</h5><br/>';
	$word = ($_GET['action'] == INVITE_ACCEPT) ? 'Accept' : 'Decline';
	$msg .= '<input type="submit" class="button" name="y" value="' . translate($word) . '"/>';
	$msg .= ' ';
	$msg .= '<input type="submit" class="button" name="n" value="' . translate('Cancel') . '"/>';
	if ($res->isRepeat()) {
		$msg .= '<br/><input type="checkbox" name="update_all" value="yes"/> '. translate('Do for all reservations in the group?');
	}
	echo '<form name="inv_mgmt" action="' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '" method="post">';
	CmnFns::do_message_box($msg);
	echo '</form>';
}

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();
?>