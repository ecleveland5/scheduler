<?php
include_once('lib/Template.class.php');
include_once('lib/db/ResDB.class.php');
include_once('lib/db/UserDB.class.php');
include_once('lib/Lab.class.php');

// Auth included in Template.php
$auth = new Auth();
$t = new Template();
$msg = '';
$lab = new Lab($_REQUEST['lab_id']);
$user = new User($_REQUEST['user_id']);

if(isset($_REQUEST['signout'])){
	$signaction = 'signout';
}else{
	$signaction = 'signin';
}

if (isset($_REQUEST['login'])) {
	$msg = $auth->doSignin($_REQUEST['user_id'], $_REQUEST['password'], $_REQUEST['lab_id'], $signaction);
}

$t->printHTMLHeader($lab->title . '<br>Equipment Sign In/Out');

print_lab_list($lab->db->get_lab_list(), $lab->lab_id);

$t->startMain();

echo "<center><table width=300><tr><td><font color='#cc0000'>Warning:</font> Please check the <a href='http://www.nanocenter.umd.edu/intranet/scheduler/roschedule.php?lab_id=" . $_REQUEST['lab_id'] . "' target='_blank'>Lab Schedule</a> for upcoming reservations before signing in to a tool to make sure you will not overlap someone's reservation.</td></tr></table></center>";
$resources_users = $auth->get_equipment_signedin_user_list();

$resources = $auth->get_equipment_list($lab->lab_id);

$auth->printResourceSigninForm($msg, $resources, $resources_users, $lab->lab_id, $signaction, $user->user_id);

$t->endMain();
// Print HTML footer
$t->printHTMLFooter();
?>