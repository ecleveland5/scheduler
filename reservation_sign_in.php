<?php
include_once('lib/Template.class.php');
include_once('lib/User.class.php');
include_once('lib/Reservation.class.php');
include_once('templates/cpanel.template.php');

// Auth included in Template.php
$auth = new Auth();
$t = new Template();
$db = new DBEngine();
$msg = '';
$res = new Reservation($_REQUEST['resid'], $_REQUEST['blackout'], $_REQUEST['pending'], NULL);

$equipment_id = $_REQUEST['equipment_id'];
$user = new User($_REQUEST['user_id']);
$useid = $_REQUEST['useid'];
$password = $_REQUEST['password'];
$frs = $_REQUEST['frs'];
$description = $_REQUEST['description'];
$notes = $_REQUEST['notes'];
$problems = $_REQUEST['problems'];

if($_REQUEST['signout']){
	$signaction = 'signout';
}else{
	$signaction = 'signin';
}

$t->printHTMLHeader($lab->title . '<br>Reservation Sign ' . (($signaction=='signin') ? "In" : "Out") );
$t->startMain();


if (isset($_REQUEST['login'])) {
	
	$res_sign_in = $auth->doReservationSignin($user->getId(), $password, $res_id, $frs, $signaction, &$msg);

	if($res_sign_in){
		//echo $user->get_email() . " " . $password;
		$_SESSION['sessionID'] = $user->getId();
		
		$order = array('start_date', 'name', 'startTime', 'endTime', 'created', 'modified');

		$reservations = $db->get_user_reservations($user->getId(), CmnFns::get_value_order($order), CmnFns::get_vert_order(), true);
		if(!$reservations){
			$msg = $db->get_err();
		}else{
			$msg = showReservationTable($reservations, $db->get_err(), NULL, $user->getId());
		}
	
		if($msg != '') CmnFns::do_message_box($msg);
		
	}else{
		$users = $auth->getUserList();
		$auth->printResourceLoginForm($msg, $users, $equipment_id, $user->getId(), $useid, $signaction);
	}
?>
	<script>
		window.opener.location.reload(true);
	</script>
	<br><br>
	<center>
	<a href="#" onClick="javascript: window.close()">Close</a>
	</center>
<?
}else{
	
	$users = $auth->getUserList();

	$auth->printResourceLoginForm($msg, $users, $equipment_id, $user->getId(), $useid, $signaction);
}
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();
?>