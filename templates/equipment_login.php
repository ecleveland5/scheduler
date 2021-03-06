<?php
include_once('lib/Template.class.php');
include_once('lib/User.class.php');

// Auth included in Template.php
$auth = new Auth();
$t = new Template();
$msg = '';
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

$t->printHTMLHeader($lab->title . '<br>Resource Sign ' . (($signaction=='signin') ? "In" : "Out") );
$t->startMain();

// Logging user out
if (isset($_REQUEST['login'])) {
	$msg .= $auth->doResourceSignin($useid, $user->get_id(), $password, $equipment_id, $frs, $signaction, $description, $notes, $problems);
	echo $msg;
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
	
	$users = $auth->get_user_list();

	$auth->printResourceLoginForm($msg, $users, $equipment_id, $user->get_id(), $useid, $signaction);
}
$t->endMain();
// Print HTML footer
$t->printHTMLFooter();
?>