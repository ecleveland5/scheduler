<?php
include_once('lib/Template.class.php');
include_once('lib/Lab.class.php');
include_once('templates/auth.template.AC.php');

// Auth included in Template.php
$auth = new Auth();
$t = new Template();
$msg = '';
$lab = new Lab($_REQUEST['lab_id']);
$lab = new Lab(1);
$signid = $_REQUEST['signid'];

if(isset($_REQUEST['signin'])){
	$signaction = 'signin';
}else if(isset($_REQUEST['signout'])){
	$signaction = 'signout';
}

// Logging user out
if (isset($_REQUEST['login'])) {
	$msg = $auth->doSignin($_REQUEST['user_id'], $_REQUEST['password'], $lab->lab_id, $signaction, $signid);
}

$t->printHTMLHeader($lab->title . ' Sign In/Out');
//print_lab_list($lab->db->get_lab_list(), $lab->lab_id);

// Print out logoImage if it exists
echo (!empty($conf['ui']['logoImage']))
		? '<div align="left"><img src="' . $conf['ui']['logoImage'] . '" alt="logo" vspace="5"/></div>'
		: '';

$t->startMain();

$users = $auth->get_user_list();
$signed_users = $auth->get_signedin_user_list($lab->lab_id);
$auth->printSigninFormAC($msg, $users, $signed_users, $lab->lab_id);

$t->endMain();
// Print HTML footer
$t->printHTMLFooter();
?>