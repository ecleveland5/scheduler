<?php
/**
* Interface form for placing/modifying/viewing a reservation
* This file will present a form for a user to
*  make a new reservation or modify/delete an old one.
* It will also allow other users to view this reservation.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Ernie Cleveland <eclevela@umd.edu>
* @version 04-01-09
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2009 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
include_once('lib/Account.class.php');
include_once('templates/account.template.php');
include_once('templates/cpanel.template.php');

if (!Auth::is_logged_in()) {
	Auth::print_login_msg();
}

$t = new Template();
$auth = new Auth();
$pager = CmnFns::getNewPager();
$pager->setTextStyle('font-size: 10px;');

$t->set_title(" Change Log : NanoCenter Scheduler");
$t->printHTMLHeader();
$t->printWelcome();
$t->startMain();

startQuickLinksCol();
showQuickLinks();		// Print out My Quick Links
startDataDisplayCol();

?><!--  -->

<h2>Maryland NanoCenter Scheduler Change Log</h2>

<div id="1.2" name="1.2">
<h4>Version 1.2</h4>
<ul>
	<li>New Billing Account system</li>
	<li>User Lab Preference</li>
	<li></li>
</ul>

</div>
<?php 
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();

?>