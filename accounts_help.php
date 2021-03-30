<?php
/**
* Interface form for placing/modifying/viewing payment accounts
* This file will present a form for a user to
*  make a new account or modify/delete an old one.
* It will also allow other users to view this account.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Ernie Cleveland <eclevela@umd.edu>
* @version 01-29-09
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2009 phpScheduleIt
* License: GPL, see LICENSE
*/
include_once('lib/Template.class.php');
include_once('templates/cpanel.template.php');

$t = new Template();
$t->setTitle("Account Help");
$t->printHTMLHeader();
$t->startMain();

?>

<h1>Account Help</h1>

<div id="toc" class="helpTopic">
	<h2>Topics</h2>
	<ul><li><a href="#reserveHelp">Reserving Equipment</a></li>
		<li><a href="#createHelp">Creating a new account</a></li>
		<li><a href="#editHelp">Editing account information</a></li>
		<li><a href="#manageUsersHelp">Managing authorized users</a></li>
		<!-- <li><a href="#viewBillingHelp">View account billing data</a></li> -->
	</ul>
</div>

<br>

<a name="reserveHelp"></a>
<div id="reserving" class="helpTopic">
	<div style="width: 400px; float: left;">
		<h2>Reserving Equipment</h2>
		<ul>
			<li>When reserving a tool, the user MUST have permission on an <strong>Active</strong> account on which to charge in order to complete the reservation.</li>
			<li>If the account dropdown list is empty, then the user is not authorized to use any account and must be authorized before a reservation can be made.
				<p>An account owner will have to add the account to the system and authorize the user before they can make a reservation.  See <a href="#createHelp"><strong>Create a New Account</strong></a> for more info.</p>
			</li>
		</ul>
	</div>
	<div style="width: 400px; float: right;">
		<h2>Questions?</h2>
        <ul><li><i>What does an <strong>Inactive Account</strong> [<a href="#reserveHelp" onMouseOver="Tip('<img src=\'img/help/inactive_account.gif\' width=579 height=290 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] mean?</i>
				<p>This means that the account cannot be used to charge against until it has been cleared by the NanoCenter staff.</p>
			</li>
			<li><i>The account I need to charge to is <strong>Inactive or Missing</strong>, what do I do?</i>
				<p>
				You will need to contact the <strong>Account Owner</strong> and have them contact <a href="/staff/staff_detail.php?user_id=403" target="_blank">Alice Mobaidin</a>.
				</p>
			</li>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="topLink"><a href="#">top</a></div>
	<div style="width: 800px;"></div>
</div>
	
<br>

<a name="createHelp"></a>
<div id="createAccount" class="helpTopic">
	<h2>Create a New Account</h2>
	<ul>
		<li>Go to the <a href="http://www.nanocenter.umd.edu/intranet/scheduler2/my_accounts.php"><strong>My Accounts</strong></a> page.</li>
		<li>
			Click on the [<strong>Create</strong>] [<a href="#createHelp" onMouseOver="Tip('<img src=\'img/help/create.gif\' width=245 height=245 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] button.
			<br><br>
			Note: Only certain users are permitted to create accounts.  Users with this access will see a [<strong>Create</strong>] button.
		</li>
		<li>A pop-up window [<a href="#createHelp" onMouseOver="Tip('<img src=\'img/help/create_pop-up.gif\' width=200 height=294 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onMouseOut="UnTip()"><i>image</i></a>] will open so you might need to unblock pop-ups in case it does not show.
			Fill out the required information.
			<br><br>
			If you are <strong>not</strong> the Owner/PI (Principal Investigator) of the account you are creating, and you cannot find their name in the dropdown list, please enter their name and email.
		</li>
		<li>After you create the account, it will show up in your <strong>My Accounts</strong> table.  
			Now you have the ability to:
			<ul><li><a href="#editHelp"><strong>Edit information</strong></a> [<a href="#createHelp" onmouseover="javascript: Tip('<img src=\'img/help/edit_link.gif\' width=221 height=221 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>]</li>
				<li><a href="#manageUsersHelp"><strong>Authorize users</strong></a> [<a href="#createHelp" onmouseover="Tip('<img src=\'img/help/users_link.gif\' width=221 height=221 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>]</li>
				<li><a href="#viewBillingHelp"><strong>View billing info</strong></a> [<a href="#createHelp" onmouseover="Tip('<img src=\'img/help/view_info_link.gif\' width=221 height=221 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>]</li>
			</ul>
			Note: Only the users who are Account Admins will see the [<strong>Edit</strong>] and [<strong>Users</strong>] links. 
		</li>
		<li>After the new account is created, it is possible to <strong>authorize users</strong> (who have registered with the scheduler) to charge to the account.  By default, the person who created the account and the Account Owner/PI are already authorized as admins.  
			<p>See the <a href="#manageUsersHelp"><strong>Manage Account Users</strong></a> section for more info.</p>
		</li>
	</ul>
	<div class="topLink"><a href="#">top</a></div>
</div>

<br>

<a name="editHelp"></a>
<div id="editing" class="helpTopic">
	<h2>Edit Account Information</h2>
	<ul>
		<li>To make changes to account information, you must have admin rights on the account.  Go to the <strong>My Accounts</strong> page to see your list of accounts.</li>
		<li>Clicking the [<strong>Edit</strong>] [<a href="#editHelp" onmouseover="Tip('<img src=\'img/help/edit_link.gif\' width=221 height=221 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] 
			link will open a pop-up window [<a href="#editHelp" onMouseOver="Tip('<img src=\'img/help/create_pop-up.gif\' width=200 height=294 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onMouseOut="UnTip()"><i>image</i></a>] with the form to edit the account information.</li>
		<li>The required fields in <font color="red">red</font> still apply when updating.</li>
	</ul>
	<div class="topLink"><a href="#">top</a></div>
</div>

<br>

<a name="manageUsersHelp"></a>
<div id="managing" class="helpTopic">
	<h2>Manage Account Users</h2>
	<ul>
		<li>This page is accessible to Account Admin users only.  From here Admins can authorize users to charge reservations to a particular account.</li>
		<li>The <strong>Unauthorized User List</strong> [<a href="#manageUsersHelp" onmouseover="Tip('<img src=\'img/help/user_list.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>]  is a list of all users who have registered with the NanoCenter Scheduler website.  Each person to be added to the account <strong>must</strong> be registered with the website.  If the user is not in this list, then they are not registered.</li>
		<li>The <strong>Authorized Users</strong> [<a href="#manageUsersHelp" onmouseover="Tip('<img src=\'img/help/auth_users.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] list is just that.  These people can charge to the account when making reservations.</li>
		<li>The <strong>Admin Users</strong> [<a href="#manageUsersHelp" onmouseover="Tip('<img src=\'img/help/admin_users.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] list shows who can make changes to the account information as well as authorize/de-authorize users.</li>
		<li><a name="addUser"></a>
			<h4>Adding a User</h4>
			<ul>
				<li>Adding a user is as simple as finding and selecting the user in the <strong>Unauthorized User List</strong> [<a href="#manageUsersHelp" onmouseover="Tip('<img src=\'img/help/add_user_01.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>].</li> 
				<li>Now click the [<strong> >> </strong>] button [<a href="#manageUsersHelp" onmouseover="Tip('<img src=\'img/help/add_user_02.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>].</li> 
				<li>This will send them to the <strong>Authorized Users</strong> list [<a href="#manageUsersHelp" onmouseover="Tip('<img src=\'img/help/add_user_03.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] and they will now be able to reserve using this account.</li>
				<li>Click the [<strong>Submit</strong>] button to save the user lists.</li>
			</ul>
		</li>
		<li><a name="removeUser"></a>
			<h4>Removing a User</h4>
			<ul>
				<li>Removing a user is similar to adding a user but clicking the [<strong> << </strong>] button [<a href="#removeUser" onmouseover="Tip('<img src=\'img/help/remove_user_01.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] to send them to the <strong>Unauthorized Users List</strong>.</li>
				<li>Start by highlighting the user to remove in the <strong>Authorized Users</strong> list [<a href="#removeUser" onmouseover="Tip('<img src=\'img/help/add_user_03.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>].</li>
				<li>Next, click the [ <strong> << </strong> ] [<a href="#removeUser" onmouseover="Tip('<img src=\'img/help/remove_user_01.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] button to remove the user from the <strong>Authorized User</strong> list.</li>
				<li>Click the [<strong>Submit</strong>] button to save the user lists.</li>
			</ul>
		</li>
		<li><a name="promoteUser"></a>
			<h4>Promote a User to Admin</h4>
			<ul>
				<li>Users who have admin rights on an account will be able to edit account information and add/remove/promote users of the account.</li>
				<li>To make a user an <strong>admin</strong>, the user will need to already be in the <strong>Authorized User</strong> list.</li>
				<li>Highlight the user in the <strong>Authorized Users</strong> list [<a href="#promoteUser" onmouseover="Tip('<img src=\'img/help/add_user_03.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>].</li>
				<li>Next click the [ <strong> >> </strong> ] button [<a href="#promoteUser" onmouseover="Tip('<img src=\'img/help/promote_user_01.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] 
					to send the user to the <strong>Admin Users</strong> list [<a href="#promoteUser" onmouseover="Tip('<img src=\'img/help/promote_user_02.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>].</li>
				<li>Click the [<strong>Submit</strong>] button to save the user lists.</li>
			</ul>
		</li>
		<li><a name="demoteUser"></a>
			<h4>Remove an Admin</h4>
			<ul>
				<li>Removing an admin user is similar to removing a user from the account alltogether.</li>
				<li>Simply highlight the user in the <strong>Admin Users</strong> list [<a href="#demoteUser" onmouseover="Tip('<img src=\'img/help/promote_user_02.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>].</li>
				<li>Click the [ <strong> << </strong> ] button [<a href="#demoteUser" onmouseover="Tip('<img src=\'img/help/demote_user_01.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>] 
					to demote them to the <strong>Authorized Users</strong> list [<a href="#promoteUser" onmouseover="Tip('<img src=\'img/help/add_user_03.gif\' width=759 height=458 style=\'padding:2px;border: 1px solid #D8D8D8;background-color:#000000;\'>')" onmouseout="UnTip()"><i>image</i></a>].
				</li>
				<li>Click the [<strong>Submit</strong>] button to save the user lists.</li>
				<li>To remove them completely from this account, refer to the <a href="#removeUser"><strong>Removing a User</strong></a> section.
			</ul>
		</li>
	</ul>
	<div class="topLink"><a href="#">top</a></div>
</div>

<!-- 
<br>

<a name="viewBillingHelp"></a>
<div id="billing" class="helpTopic">
	<h2>View Account Billing Information</h2>
	<ul>
		<li>Billing information</li>
	</ul>
	<div class="topLink"><a href="#">top</a></div>
</div>
-->

<?php

$t->endMain();
$t->printHTMLFooter();
	
?>