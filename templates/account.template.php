<?php
/**
* This file provides output functions for my_accounts.php
* No data manipulation is done in this file
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @version 08-18-05
* @package Templates
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/

/**
* Print out the account id
* @param array $rs resource data array
*/
function print_account_title(&$rs) {
	echo "<h3 align=\"center\">{$rs['name']}</h3>\n";
}


/**
* Opens form for account
* @param bool $show_repeat whether to show the repeat box
* @param bool $is_blackout if this is a blackout
*/
function begin_account_form($show_repeat, $is_blackout = false) {
	echo '<form name="account" method="post" action="' . $_SERVER['PHP_SELF'] . '" style="margin: 0px" onsubmit="return ' . (($show_repeat) ? 'check_account_form(this)' : 'check_for_delete(this)') . ';">' . "\n";
}



/**
* Prints out list of current accounts
* @param Object $pager pager object
* @param mixed $accounts array of account data
* @param Object $user current user
*/
function print_account_list($accounts, $pager, $user) {
	global $link;
	$accounts_list = array();
	//echo "sizeof accounts: ". sizeof($accounts);
	//echo "<br>";

	$num = sizeof($accounts);	// Get number of records
	$pager->setTotRecords($num);

	foreach ($accounts as $a) {
		//echo "account_id: " . $accounts[$i]['account_id'] . "<br>";
		$account = new Account($a['account_id']);
		array_push($accounts_list, $account->get_account_data($a['account_id']));
	}
?>
<form name="manageAccount" method="post" action="my_accounts.php">
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td colspan="7" class="tableTitle">&#8250; Manage My Accounts</td>
          <td class="tableTitle" align="right"><a href="help_accounts.php" target="_blank"><strong>Accounts Help</strong></a></td>
        </tr>
        <tr class="rowHeaders">
          <td>Name</td>
          <td>Account ID</td>
          <td>PI</td>
          <td>Last Update</td>
          <td>Edit</td>
          <td>Users</td>
          <td>Billing Info</td>
          <td>Status</td>
        </tr>
        <?php
	if (empty($accounts_list)) {
		echo '<tr class="cellColor0"><td colspan="9" style="text-align: center;">No Accounts Found.</td></tr>' . "\n";
	}
	
	$auth = new Auth();
	$i = 0;
    foreach ($accounts_list as $a) {
		$cur_account = new Account($a['account_id']);
		
        echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\" id=\"tr$i\">\n";

		echo '<td style="text-align:left">' . $a['name'] . "</td>\n"
            . '<td style="text-align:left">' . $a['FRS'] . "</td>\n";
		
		// account PI/Owner
		echo '<td style="text-align:left">';

		if( !$auth->is_user($a['pi']) || $a['pi']==0 || $a['pi']==NULL ){
			echo $a['pi_last_name'];
			if($a['pi_first_name']!='')
				echo ", " . $a['pi_first_name'];
			echo " <font size=1>Not Registered</font>";
		}else{
			$pi = new User($a['pi']);
			echo $pi->get_last_name() . ", " . $pi->get_first_name();
		}
		echo "</td>\n";

		echo '<td style="text-align:left">' . $a['last_update'] . "</td>\n";

		// edit account info
		echo  '<td>';
		if($cur_account->is_admin($user->get_id()) ||
			$user->get_isadmin()) {
			//echo $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/account_id=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . 'account_id=' . $cur['account_id'], 'Edit', '', '','Edit data for' .$cur['name']);
			echo "<a href=\"javascript:account('m',".$cur_account->get_account_id().");\">Edit</a>";
		}
		echo "</td>\n";

		// edit users
      	echo '<td>';
		if($cur_account->is_admin($user->get_id()) ||
			$user->get_isadmin()) {
			echo $link->getLink("my_account_users.php?account_id=" . $a['account_id'], 'Users', '', '', 'Edit this accounts users');
		}
		echo "</td>\n";

		echo "<td>";
		echo $link->getLink('view_account_info.php' . '?' . preg_replace("/account_id=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . 'account_id=' . $a['account_id'], 'View Billing', '', '', 'View information for this account.');
		echo "</td>";

		echo "<td>";
		echo  (($a['status'] == 1) ? '<font color="#009900">Active' : '<font color="#ff0000">Inactive');
		echo "</td>";

		echo  "</tr>\n";
		$i++;
    }
    // Close table
    ?>
      </table>
    </td>
  </tr>
</table>
<br />
<?php
	//echo submit_button(translate('Delete'), 'account_id') . hidden_fn('delAccount');
	//echo submit_button('Toggle', 'account_id') . hidden_fn('togAccount');
	echo '</form>';

	// check if the user can create an account
	$auth = new Auth();
	if($auth->isAdmin() || $auth->canCreateAccount()){
		echo "<form onsubmit=\"javascript:account('c','');\">";
		echo "<input type='submit' name='submit' value='Create' class='button'>";
		echo "</form>";
	}
}


function print_add_account_button() {
	if(Auth::isAdmin() || Auth::canCreateAccount()){
		echo "<input type='button' name='create_account' value='Create New Account' onclick=\"javascript:account('c',NULL);\">";
	}
}

/**
* Interface to add or edit account information
* @param mixed $rs array of account data
* @param boolean $edit whether this is an edit or not
* @param object $pager Pager object
*/
function print_account_edit($rs, $users, $edit=false) {
	global $conf;
	$start = 0;
	$end   = 1440;
	$mins = array(0, 10, 15, 30);
	$disabled = ($edit == 1) ? 'disabled="disabled"' : '';


	if ($edit) {
		$minH = intval($rs['minRes'] / 60);
		$minM = intval($rs['minRes'] % 60);
		$maxH = intval($rs['maxRes'] / 60);
		$maxM = intval($rs['maxRes'] % 60);
	}
	else {
		$maxH = 24;
	}
	$auth = new Auth();
	$user = new User($auth->getCurrentID());
	if($user->get_type_id() == 3 || $user->get_type_id() == 4 || $auth->isAdmin()){
		$isUMD = true;
	}else{
		$isUMD = false;
	}
	//echo "isumd: ".$isUMD;
  	$canCreateAccount = false;
  	$canCreateAccount = $auth->canCreateAccount();
 	//echo "can create: ".$canCreateAccount;
	  if($canCreateAccount){

    ?>
    <span class="required">Items in red are required</span>
<form name="addAccount" method="post" action="<?php echo $_SERVER['PHP_SELF']?>" onsubmit="javascript: return checkAccount();">
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td width="200" class="formNames"><span class="required">Account Nickname</span></td>
          <td class="cellColor"><input type="text" name="name" class="textbox" value="<?php echo (isset($rs['name']) ? $rs['name'] : '')?>" size="50" />
          </td>
        </tr>
<?php
	// If the user is not an external user (Only UMD staff, faculty, researcher)
	if($isUMD) {
?>
        <tr>
          <td class="formNames"><span class="required">Account # (KFS #)</span></td>
          <td class="cellColor"><input type="text" name="FRS" class="textbox" value="<?php echo (isset($rs['FRS']) ? $rs['FRS'] : '')?>" size="20" />
		  </td>
        </tr>
        <tr>
          <td class="formNames"><span class="required">Sub Acount # (Sub-FRS #)</span></td>
          <td class="cellColor"><input type="text" name="sub_FRS" class="textbox" value="<?php echo (isset($rs['sub_FRS']) ? $rs['sub_FRS'] : '')?>" size="20" />
		  </td>
        </tr>
        <tr>
          <td class="formNames"><span class="">Federal ID # (only for commercial accounts)</span></td>
          <td class="cellColor"><input type="text" name="fed_id" class="textbox" value="<?php echo (isset($rs['fed_id']) ? $rs['fed_id'] : '')?>" size="50"  />
          </td>
        </tr>
        <?php
	}
?>
		<tr>
			<td class="formNames"><span class="required">Account Owner (PI)</span></td>
			<td class="cellColor">
				<select name="pi" class="textbox">
					<option value="">-- Select Owner/PI --</option>
		<?php
		if (empty($users))
			echo '<option value="">No Users\'s found</option>';
		else {
			for ($i = 0; $i < count($users); $i++)
				echo '<option value="' . $users[$i]['user_id'] . '"' . (isset($rs['pi']) && $users[$i]['user_id'] == $rs['pi'] ? ' selected="selected"' : '') . '>' . $users[$i]['last_name'] . ", " . $users[$i]['first_name'] . "</option>\n";
		}
		?>
				</select>

				<br /><br />

				If the Owner/PI is not in the above list, please fill the name in the boxes below.<br />
				<table>
					<tr>
						<td class="formNames">First Name</td>
						<td class="cellColor"><input type="text" name="pi_first_name" class="textbox" value="<?php echo (isset($rs['pi_first_name']) ? $rs['pi_first_name'] : '')?>" /></td>
					</tr>
					<tr>
						<td class="formNames">Last Name</td>
						<td class="cellColor"><input type="text" name="pi_last_name" class="textbox" value="<?php echo (isset($rs['pi_last_name']) ? $rs['pi_last_name'] : '')?>" />
						</td>
					</tr>
					<tr>
						<td class="formNames">Email</td>
						<td class="cellColor"><input type="text" name="pi_email" class="textbox" value="<?php echo (isset($rs['pi_email']) ? $rs['pi_email'] : '')?>" />
						</td>
					</tr>
				</table>

			</td>
		</tr>
        <tr>
          <td class="formNames"><span class="required">Organization</span></td>
          <td class="cellColor"><input type="text" name="organization" class="textbox" value="<?php echo (isset($rs['organization']) ? $rs['organization'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames"><span class="required">Billing Address 1</span></td>
          <td class="cellColor"><input type="text" name="billing_address1" class="textbox" value="<?php echo (isset($rs['billing_address1']) ? $rs['billing_address1'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames"><span class="required">Billing Address 2</span></td>
          <td class="cellColor"><input type="text" name="billing_address2" class="textbox" value="<?php echo (isset($rs['billing_address2']) ? $rs['billing_address2'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames"><span class="required">Billing City</span></td>
          <td class="cellColor"><input type="text" name="billing_city" class="textbox" value="<?php echo (isset($rs['billing_city']) ? $rs['billing_city'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames"><span class="required">Billing State</span></td>
          <td class="cellColor"><input type="text" name="billing_state" class="textbox" value="<?php echo (isset($rs['billing_state']) ? $rs['billing_state'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames"><span class="required">Billing Zip</span></td>
          <td class="cellColor"><input type="text" name="billing_zip" class="textbox" value="<?php echo (isset($rs['billing_zip']) ? $rs['billing_zip'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Start Date</td>
          <td class="cellColor"><input type="date" name="start_date" class="textbox" value="<?php echo (isset($rs['start_date']) ? $rs['start_date'] : '')?>" />
<?php
            echo '<input type="hidden" id="hdn_start_date" name="start_date" value="' . date('m' . INTERNAL_DATE_SEPERATOR . 'd' . INTERNAL_DATE_SEPERATOR . 'Y', $start_date) . '" onchange="checkCalendarDates();"/>';
			//if ($allow_multi) {
				echo '<a href="javascript:void(0);"><img src="img/calendar.gif" border="0" id="img_start_date" alt="' . translate('Start') . '"/></a>'
                   . '<br/><br/>';
			//}
?>
          </td>
        </tr>
        <tr>
          <td class="formNames">End Date</td>
          <td class="cellColor"><input type="date" name="end_date" class="textbox" value="<?php echo (isset($rs['end_date']) ? $rs['end_date'] : '')?>" />
          </td>
        </tr>
        <tr>
          <td class="formNames">Admin Unit</td>
          <td class="cellColor"><input type="text" name="admin_unit" class="textbox" value="<?php echo (isset($rs['admin_unit']) ? $rs['admin_unit'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Admin Contact Name</td>
          <td class="cellColor"><input type="text" name="admin_contact_name" class="textbox" value="<?php echo (isset($rs['admin_contact_name']) ? $rs['admin_contact_name'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Admin Contact Email</td>
          <td class="cellColor"><input type="text" name="admin_contact_email" class="textbox" value="<?php echo (isset($rs['admin_contact_email']) ? $rs['admin_contact_email'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Admin Contact Phone</td>
          <td class="cellColor"><input type="text" name="admin_contact_phone" class="textbox" value="<?php echo (isset($rs['admin_contact_phone']) ? $rs['admin_contact_phone'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Business Contact Name</td>
          <td class="cellColor"><input type="text" name="business_contact_name" class="textbox" value="<?php echo (isset($rs['business_contact_name']) ? $rs['business_contact_name'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Business Contact Email</td>
          <td class="cellColor"><input type="text" name="business_contact_email" class="textbox" value="<?php echo (isset($rs['business_contact_email']) ? $rs['business_contact_email'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Business Contact Phone</td>
          <td class="cellColor"><input type="text" name="business_contact_phone" class="textbox" value="<?php echo (isset($rs['business_contact_phone']) ? $rs['business_contact_phone'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Technical Contact Name</td>
          <td class="cellColor"><input type="text" name="technical_contact_name" class="textbox" value="<?php echo (isset($rs['technical_contact_name']) ? $rs['technical_contact_name'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Technical Contact Email</td>
          <td class="cellColor"><input type="text" name="technical_contact_email" class="textbox" value="<?php echo (isset($rs['technical_contact_email']) ? $rs['technical_contact_email'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Technical Contact Phone</td>
          <td class="cellColor"><input type="text" name="technical_contact_phone" class="textbox" value="<?php echo (isset($rs['technical_contact_phone']) ? $rs['technical_contact_phone'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Comments</td>
          <td class="cellColor"><textarea name="comments" class="textbox" rows="8" cols="47" /><?php echo (isset($rs['comments']) ? $rs['comments'] : '')?></textarea>
          </td>
        </tr>
        <tr>
          <td class="formNames">Source</td>
          <td class="cellColor"><input type="text" name="source" class="textbox" value="<?php echo (isset($rs['source']) ? $rs['source'] : '')?>" size="50"  />
          </td>
        </tr>
        <tr>
          <td class="formNames">Agency</td>
          <td class="cellColor"><input type="text" name="agency" class="textbox" value="<?php echo (isset($rs['agency']) ? $rs['agency'] : '')?>" size="50"  />
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />
<?php
        // Print out correct buttons
        if (!$edit) {
            echo  '<input type="submit" name="submit" value="Create" class="button" />' . "\n"
				. '<input type="hidden" name="get" value="' . $get_value  . '" />'
				. '<input type="reset" name="reset" value="' . translate('Clear') . '" class="button" />' . "\n";
        } else {
            echo  '<input type="submit" name="submit" value="' . translate('Modify') . '" class="button">' . "\n"
				. '<input type="button" name="cancel" value="' . translate('Cancel') . '" class="button" onclick="javascript: opener.location.reload();
window.close();">'
				. '<input type="hidden" name="account_id" value="' . $rs['account_id'] . '" />' . "\n";
        	// Unset variables
			unset($rs);
		}

		echo "</form>\n";

	  }else{
	  	echo "You are not authorized to create an account.  Please have your advisor create the account and authorize you to use it.";
	  }
}


function print_manage_account_users($account, $users){

	global $link;
	if ( $account->is_admin(Auth::getCurrentID()) || Auth::isAdmin() ) {


	$account_users = $account->get_account_users();
	?>
<table width="100%"><tr><td class="tableBorder">

	<table width="100%">
		<tr><td class="tableTitle">&#8250; Account Users</td>
			<td class="tableTitle" align="right"><a href="help_accounts.php" target="_blank"><strong>Accounts Help</strong></a></td>
		</tr>
		<tr class="cellColor"><td colspan="2">
			<table width="100%"><tr>
					<td class="rowHeaders" width="20%">Account Name:</td>
					<td class="cellColor0"><?php 	if($account->get_name()!=""){
									echo $account->get_name();
								}else{
									echo "<i>Blank</i>";
								}?>
					</td>
				</tr>
				<tr><td class="rowHeaders">Account Owner:</td>	<td class="cellColor1">
					<?php
							if ($account->get_field('pi_id')==NULL) {
								echo $account->get_field('pi_last_name');

								if($account->get_field('pi_first_name')!=''){
									echo ", " . $account->get_field('pi_first_name');
								}
								echo " <font size=1 color='red'>Not Registered</font>";

							} else {

								$pi = new User($account->get_field('pi_id'));

								echo $pi->get_name(true);

							}
					?>
					</td></tr>
				<tr><td class="rowHeaders">Account ID:</td>		<td class="cellColor0"><?php echo $account->get_field('FRS'); ?></td></tr>
				<tr><td class="rowHeaders">Account Sub-ID:</td>	<td class="cellColor1"><?php echo $account->get_field('sub_FRS'); ?></td></tr>
				<tr><td class="rowHeaders">Current Status:</td>	<td class="cellColor0"><?php echo ($account->get_field('status') ? "Active" : "Inactive"); ?></td></tr>
			</table>
			</td>
		</tr>
	<?php
	$au_array = array();
	foreach ($account_users as $au) {
		array_push($au_array, $au['user_id']);
	}
	?>
		<tr class="cellColor"><td colspan="2">
			<form name="manage_account_users" action="<?php echo ((strstr($_SERVER['PHP_SELF'], "admin.php")) ? "admin_update.php" : $_SERVER['PHP_SELF'])?>" method="post" onsubmit="javascript: selectOptions('account_user_list');selectOptions('account_admin_list');">
			<table>
				<tr><td><center><strong>Unauthorized User List</strong></center><br>
						<select id="all_users_list" size="15" style="width: 30em; font-size: 1.05em;" multiple="multiple">
						<?php foreach($users as $user){ ?>
							<?php if (!in_array($user['user_id'], $au_array)) {?>
							<option value="<?php echo $user['user_id'] ?>"><?php echo $user['last_name']. ", ".$user['first_name'] ?> - <?php echo $user['email']; ?></option>
							<?php } ?>
						<?php } ?>
						</select>
					</td>
					<td>
						<input type="button" name="add_user" value=" >> " onclick="moveOptions('all_users_list','account_user_list')" class="button">
						<br><br>
						<input type="button" name="remove_user" value=" << " onclick="moveOptions('account_user_list','all_users_list')" class="button">
					</td>
					<td><center><strong>Authorized Users</strong></center><br>
						<select name="user_list[]" id="account_user_list" size="15" style="width:30em; font-size: 1.05em;" multiple="multiple">
						<?php
							foreach($account_users as $account_user){
								if(!$account->is_admin($account_user['user_id'])) {
						?>
							<option value="<?php echo $account_user['user_id'] ?>"><?php echo $account_user['last_name']. ", ".$account_user['first_name'] ?> - <?php echo $account_user['email']; ?></option>
						<?php
								}
							 }
						?>
						</select>
					</td>
					<td>
						<input type="button" name="promote_user" value=" >> " onclick="moveOptions('account_user_list', 'account_admin_list')" class="button">
						<br><br>
						<input type="button" name="demote_user" value=" << " onclick="moveOptions('account_admin_list','account_user_list')" class="button">
					</td>
					<td><center><strong>Admin Users</strong></center><br>
						<select name="admin_list[]" id="account_admin_list" size="15" style="width:30em; font-size: 1.05em;" multiple="multiple">
						<?php
							foreach($account_users as $account_user){
								if($account->is_admin($account_user['user_id'])) {
						?>
							<option value="<?php echo $account_user['user_id'] ?>"><?php echo $account_user['last_name']. ", ".$account_user['first_name'] ?> - <?php echo $account_user['email']; ?></option>
						<?php 		}
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td colspan="5">
						<input type="hidden" name="account_id" value="<?php echo $account->get_account_id(); ?>">
						<input type="submit" name="submit" value="Submit" class="button">
						<input type="button" name="cancel" value="Cancel" class="button" onclick="document.location='<?php echo $_SERVER['PHP_SELF']; ?>?account_id=<?php echo $account->get_account_id()?>';" />
						<?php if (strstr($_SERVER['PHP_SELF'], "admin.php")) { ?>
						<input type="hidden" name="fn" value="editAccountUsers">
						<?php } ?>
					</td>
				</tr>
			</table>
			</form>
		</td></tr>
	</table>

<?php

	}else{
		echo "This login is not authorized to view this page.";
	}
?>
</td></tr></table>
<?php
}

/**
* Prints all the buttons and hidden fields
* @param object $res Reservation object to work with
*/
function print_account_buttons_and_hidden(&$res) {
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td>
<?php
	$type = $res->get_type();
      // Print buttons depending on type
    echo '<p>';
	switch($type) {
  	    case 'm' :
            echo '<input type="submit" name="submit" value="' . translate('Modify') . '" class="button" onclick="selectUsers();"/>'
				. '<input type="hidden" name="fn" value="modify" />';
	    break;
        case 'd' :
            echo '<input type="submit" name="submit" value="' . translate('Delete') . '" class="button" />'
					. '<input type="hidden" name="fn" value="delete" />';
	    break;
        case 'v' :
            echo '<input type="button" name="close" value="' . translate('Close Window') . '" class="button" onclick="window.close();" /></p>';
	    break;
        case 'a' :
            echo '<input type="submit" name="submit" value="' . translate('Save') . '" class="button" onclick="selectUsers();"/>'
					. '<input type="hidden" name="fn" value="create" />';
        break;
    }
    // Print cancel button as long as type is not "view"
	if ($type != 'v')
		echo '&nbsp;&nbsp;&nbsp;<input type="button" name="close" value="' . translate('Cancel') . '" class="button" onclick="window.close();" /></p>';

	// print hidden fields
	if ($res->get_type() == 'a') {
        echo '<input type="hidden" name="machid" value="' . $res->get_machid(). '" />' . "\n"
			  . '<input type="hidden" name="lab_id" value="' . $res->sched['lab_id'] . '" />' . "\n"
			  . '<input type="hidden" name="pending" value="' . $res->get_pending(). '" />' . "\n"
			  . '<input type="hidden" name="user_id" value="' . Auth::getCurrentID() . '" />' . "\n";;
    }
    else {
        echo '<input type="hidden" name="resid" value="' . $res->get_id() . '" />' . "\n"
			. '<input type="hidden" name="user_id" value="' . $res->get_user_id() . '" />' . "\n";;
    }
?>
    </td>
  </tr>
</table>
<?php
}

/**
 *
 */
function print_account_details(Account $account) {
	$account_pi = new User($account->get_field('pi_id'));
?>
<table width="100%"><tr><td class="tableBorder">

	<table width="100%">
		<tr><td colspan="2" class="tableTitle">Account Details</td></tr>
		<tr class="cellColor"><td colspan="2">
			<table width="100%"><tr>
					<td class="rowHeaders" width="20%">Account Name:</td>
					<td class="cellColor0">
					    <?php
					    if($account->get_name()!=""){
                            echo $account->get_name();
					    } else {
							echo "<i>Blank</i>";
						}
					    ?>
					</td>
				</tr>
				<tr><td class="rowHeaders">Account Owner:</td>	<td class="cellColor1"><?php echo $account_pi->get_name(); ?></td></tr>
				<tr><td class="rowHeaders">Account ID:</td>		<td class="cellColor0"><?php echo $account->get_field('FRS'); ?></td></tr>
				<tr><td class="rowHeaders">Account Sub-ID:</td>	<td class="cellColor1"><?php echo $account->get_field('sub_FRS'); ?></td></tr>
				<tr><td class="rowHeaders">Current Status:</td>	<td class="cellColor0"><?php echo ($account->get_field('status') ? "Active" : "Inactive"); ?></td></tr>
			</table>
			</td>
		</tr>
	</table>
</td></tr></table>
<?php
}

function printAcctAscLink(&$pager, $order, $text) {
	$text = translate("Sort by ascending $text");
	print_acct_asc_desc_link($pager, $order, $text, 'ASC');
}

/**
* Prints out a link to reorder recordset descending order
* @param Object $pager pager object
* @param string $order order to sort result set by
* @param string $text link text
* @see print_asc_desc_link()
*/
function printAcctDescLink(&$pager, $order, $text) {
	$text = translate("Sort by descending $text");
	print_acct_asc_desc_link($pager, $order, $text, 'DESC');
}

/**
* This function extends the printAscLink and printDescLink, printing out
*  a link to reorder a recordset in a certain order
* This was added to keep the current printAsc/DescLink functions in place, but put
*  all logic into one function
* @param Object $pager pager object
* @param string $order order to sort result set by
* @param string $text link text
* @param string $vert ascending or descending order
*/
function print_acct_asc_desc_link(&$pager, $order, $text, $vert) {
	global $link;

	$page = $pager->getPageNum();

	$plus_minus = ($vert == 'ASC') ? '[+]' : '[&#8211;]';		// Plus or minus box
	$limit_str = $pager->getLimitVar() . '=' . $pager->getLimit();
	$page_str  = '&amp;' . $pager->getPageVar() . '=' . $pager->getPageNum();
	$vert_str  = "&amp;vert=$vert";

	// Fix up the query string
	$query =  $_SERVER['QUERY_STRING'];
	if (preg_match('(\?|&)' . $pager->getLimitVar() . "=[0-9]*/i", $query))
		$query = preg_replace('(\?|&)' . $pager->getLimitVar() . "=[0-9]*/i", $limit_str, $query);
	else
		$query .= $limit_str;

	if (preg_match('(\?|&)' . $pager->getPageVar() . "=[0-9]*/i", $query))
		$query = preg_replace('(\?|&)' . $pager->getPageVar() . "=[0-9]*/i", $page_str, $query);
	else
		$query .= $page_str;

	if (preg_match("(\?|&)vert=[a-zA-Z]*/i", $query))
		$query = preg_replace("(\?|&)vert=[a-zA-Z]*/i", $vert_str, $query);
	else
		$query .= $vert_str;

	if (preg_match("(\?|&)order=[a-zA-Z]*/i", $query))
		$query = preg_replace("(\?|&)order=[a-zA-Z_]*/i", "&amp;order=$order", $query);
	else
		$query .= "&amp;order=$order";

	$link->doLink($_SERVER['PHP_SELF'] . '?' . $query, $plus_minus, '', '', $text);
}
?>