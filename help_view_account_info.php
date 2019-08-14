<?php
/**
* Interface form for placing/modifying/viewing payment accounts
* This file will present a form for a user to
*  make a new account or modify/delete an old one.
* It will also allow other users to view this account.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Ernie Cleveland <eclevela@umd.edu>
* @version 07-29-09
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2009 phpScheduleIt
* License: GPL, see LICENSE
*/

include_once('lib/Template.class.php');
include_once('lib/User.class.php');
include_once('lib/Auth.class.php');
include_once('templates/cpanel.template.php');

$t = new Template();
$auth = new Auth();
$user = new User($auth->getCurrentID());

$t->set_title("Account Billing Information Help");
$t->printHTMLHeader();
$t->startMain();

?>

<h1>Account Billing Information Help</h1>

<div id="toc" class="helpTopic">
	<h2>Topics</h2>
	<ul><li><a href="#overview">Overview</a></li>
		<li><a href="#viewCharges">Viewing Charges</a></li>
		<li><a href="#ncDiscounts">NanoCenter Member Discounts</a></li>
	</ul>
</div>

<br>

<a name="overview"></a>
<div id="reserving" class="helpTopic">
	<div style="width: 500px; float: left;">
		<h2>Overview</h2>
		The Account Billing Info page reports on the billing data that has been processed and invoices sent.  
		This data reflects charges from equipment use, lab technician time, and discounts.  
		
		<h4>Breakdown</h4>
		Currently the information is broken down first by month, user, date, and charge(s).  
		To view the data please see the <strong>Viewing Charges</strong> section.

		<h4>Billing Month</h4>
		The NanoCenter billing month starts on the 16th and end on the 15th of each month.
		So billing data for July 2009 would include reservations from June 16th, 2009 thru July 15th, 2009.  
		Some charges might reflect adjustments from previous billing periods but where credited or debited durring the reported billing month.
	</div>

	<div class="clear"></div>
	<div class="topLink"><a href="#">top</a></div>
	<div style="width: 800px;"></div>
</div>

<br>

<a name="viewCharges"></a>
<div id="reserving" class="helpTopic">
	<div style="width: 500px; float: left;">
		<h2>Viewing Charges</h2>
		Clicking on the billing month link or total will show a list of people who charged to the current account for that billing month.  
		There will be a monthly total for each user.  Clicking the user will show a list of dates when the user made a charge to the account.
		Clicking a date will show a list of charges (debits and credits) for the entire day (may show multiple tools).  Clicking on the tool
		will show a review of the reservation that was made.  Clicking on an opened item will close it.
	</div>

	<div class="clear"></div>
	<div class="topLink"><a href="#">top</a></div>
	<div style="width: 800px;"></div>
</div>

<br>

<a name="ncDiscounts"></a>
<div id="reserving" class="helpTopic">
	<div style="width: 500px; float: left;">
		<h2>NanoCenter Member Discounts</h2>
		<a href="http://www.nanocenter.umd.edu/faculty/MembershipAgreement.php">NanoCenter Membership</a> entitles participating University of Maryland faculty and 
		researchers to the NanoCenter Membership discount.  These discounts are treated as a charge in this report and is highlighted in light blue.  It reflects a 20% discount to the charge listed above it.
	</div>

	<div class="clear"></div>
	<div class="topLink"><a href="#">top</a></div>
	<div style="width: 800px;"></div>
</div>


<?php

$t->endMain();
$t->printHTMLFooter();
	
?>