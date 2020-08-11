<?php
/**
* This file provides output functions for reserve.php
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
* Print out the resource name
* @param array $rs resource data array
*/
function print_title(&$rs) {
	echo "<h3 align=\"center\">{$rs['name']}</h3>\n";
}

/**
* Opens form for reserve
* @param bool $show_repeat whether to show the repeat box
* @param bool $is_blackout if this is a blackout
*/
function begin_reserve_form($show_repeat, $is_blackout = false) {
	echo '<form name="reserve" method="post" action="' . $_SERVER['PHP_SELF'] . '?is_blackout=' . intval($is_blackout) . '" style="margin: 0px" onsubmit="return ' . (($show_repeat) ? 'check_reservation_form(this)' : 'check_for_delete(this)') . ';">' . "\n";
}

/**
 * Begins the outer reservation table.  This prints out the tabs for basic/advanced
 * and switches between them
 *
 */
function begin_container() {
?>
<!-- begin_container() -->
<table width="100%" cellspacing="0" cellpadding="0" border="0" id="tab-container">
<tr class="tab-row">
<!--
<td class="tab-selected" id="tab_basic" onclick="javacript: clickTab(this, 'pnl_basic');"><a href="javascript:void(0);"><?php echo translate('Basic');?></a></td>
<td class="tab-not-selected" id="tab_advanced" onclick="javacript: clickTab(this, 'pnl_advanced');" style="border-left-width:0px;"><a href="javascript:void(0);"><?php echo translate('Participants')?></a></td>
-->
<td class="tab-filler">&nbsp;</td>
</tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="tab-main">
  <tr>
    <td id="main-tab-panel" style="padding:7px;">
<?php
}

/**
* Prints the basic reservation form elements
* This contains: resource data, time information/select, user info, create/modify times, recurring selection, pending info
* @param object $res Reservation object to work with
* @param array $rs resource data array
* @param bool $is_private if the privacy mode is on and we should hide personal data
*/
function print_basic_panel($res, $rs, $is_private) {
?>
	<!-- Begin basic panel -->
      <div id="pnl_basic" style="display:table; width:100%; position: relative;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td width="330">
			<!-- Content begin -->
<?php
    // Print resource info
	print_equipment_data($rs);

	print_time_info($res, $rs, !$res->is_blackout, (isset($rs['allow_multi']) && $rs['allow_multi'] == 1));

	$user = new User($res->user_id);
	// will need to change to get list of permitted accounts.

	if (!$res->is_blackout && !$is_private)
		print_user_info($res->type, $user);	// Print user info

	if ($res->user_id == Auth::getCurrentID() || Auth::isAdmin())
		print_account_info($res, $user);

	if (!empty($res->id))			// Print created/modified times (if applicable)
		print_create_modify($res->created, $res->modified, $res->deleted, $res->deleted_by, $res->deleted_by_email);

	print_summary($res->summary, $res->type);

	if ($res->signin != ""){
		print_signout();
	}

	$user = new User($res->user_id);
	if ($user->get_isadmin()){
		if (!empty($res->parentid) && ($res->type == RES_TYPE_MODIFY || $res->type == RES_TYPE_DELETE || $res->type == RES_TYPE_APPROVE))
			print_recur_checkbox($res->parentid);

		if ($res->type == RES_TYPE_MODIFY)
			print_del_checkbox();

	}

	if ($res->type == RES_TYPE_ADD) {		// Print out repeat reservation box, if applicable
		divide_table();
		$user = new User($res->user_id);

		if ($user->get_isadmin()){
			print_repeat_box(date('m', $res->start_date), date('Y', $res->start_date));
		}
		unset($user);
		if( $res->is_pending )
			 print_pending_approval_msg();

	}
?>
			<!-- Content end -->
			</td>
          </tr>
        </table>
      </div>
	  <!-- End basic panel -->
<?php
}

/**
* Prints out the advanced reservation functions
* @param Object $res Reservation object that is being printed out
* @param array $users array of all users first_name, last_name, user_id
* @param bool $is_owner if the current user is the reservations owner
* @param bool $viewable if the advanced panel shows anything
*/
function print_advanced_panel(&$res, $users, $is_owner, $viewable = true) {
?>
	<!-- Begin advanced panel -->
     <div id="pnl_advanced" style="width:100%; position: relative;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
		  <!-- Begin content -->
		  <?php
		    if (!$viewable) {
				echo '<td>' . translate('No advanced options available') . '</td>';
			}
			else {
				$user_info = $res->users;
				if ($is_owner && $res->type != RES_TYPE_APPROVE && $res->type != RES_TYPE_DELETE) {
					print_invite_selectboxes($res, $users, $user_info);
					print_participating_users($user_info);
				}
				else {
					print_invited_particpating_users($user_info);
				}
			}
		  ?>
			<!-- End content -->
          </tr>
        </table>
      </div>
	  <!-- End advanced panel -->
<?php
}

/**
* Prints out select boxes so that the reservation owner/creator can
*  invite or uninvite users
* @param Object $res Reservation object of current reservation
* @param array $users array of all users in the database
* @param array $user_info the users array of the Reservation object
*/
function print_invite_selectboxes(&$res, $users, $user_info) {
	?>
	<td colspan="3"><p align="center" style="font-weight: bold;">
	<?php
	echo translate('Invite Users');
	?>
	</p>
	</td>
	</tr>
	<tr>
	<td width="200" align="center">
	<?php echo translate('All Users')?><br/>
	<select name="all_users[]" id="all_users" class="textbox" multiple="multiple" size="10" style="width:195px;">
	<?php
    $iMax = count($users);
	for ($i = 0;  $i < $iMax; $i++) {
		$found = ($users[$i]['user_id'] === $res->user_id);		// The owner never gets printed
		for ($j = 0; $j < count($user_info) && !$found; $j++) {
			if ( $user_info[$j]['user_id'] === $users[$i]['user_id'] ) {// && ($user_info[$i]['invited'] == 1) ) {
				$found = true;
				break;
			}
		}
		// We dont print out the users that are already invited
		if (!$found) {
		    echo "<option value=\"{$users[$i]['user_id']}|{$users[$i]['email']}\">{$users[$i]['last_name']}, {$users[$i]['first_name']} - {$users[$i]['email']}</option>";
        }
	}
	?>
	</select>
	</td>
	<td valign="middle" align="center">
	<button type="button" id="add_to_invite" class="button" onclick="javascript: moveSelectItems('all_users','invited_users');" style="width:75px;font-size:12px;">&raquo;&raquo;</button>
	<br/><br/>
	<?php echo translate('Hold CTRL to select multiple')?>
	<br/><br/>
	<button type="button" id="remove_from_invite" class="button" onclick="javascript: moveSelectItems('invited_users','all_users');" style="width:75px;font-size:12px;">&laquo;&laquo;</button>
	</td>
	<td width="200" align="center">
	<?php echo translate('Invited Users')?><br/>
	<select name="invited_users[]" id="invited_users" class="textbox" multiple="multiple" size="10" style="width:195px;">
	<?php
    $iMax = count($user_info);
	for ($i = 0; $i < $iMax; $i++) {
		if ($user_info[$i]['invited'] === 1) {
			echo "<option value=\"{$user_info[$i]['user_id']}|{$user_info[$i]['email']}\">{$user_info[$i]['last_name']}, {$user_info[$i]['first_name']} - {$user_info[$i]['email']}</option>";
		}
	}
	?>
	</select>
	</td>
	<?php
}

/**
* Prints out select boxes so that the reservation owner/creator can
*  remove users from participating in this reservation
* @param array $user_info the users array of the Reservation object
*/
function print_participating_users($user_info) {
	?>
	</tr><tr><td colspan="3"><p align="center" style="font-weight: bold;padding-top:10px;">
	<?php
	echo translate('Remove Participants');
	?>
	</p>
	</td>
	</tr><tr>
	<td width="200" align="center">
	<?php echo translate('All Users')?><br/>
	<select name="removed_users[]" id="removed_users" class="textbox" multiple="multiple" size="10" style="width:195px;">
	</select>
	</td>
	<td valign="middle" align="center">
	<button type="button" id="add_to_participate" class="button" onclick="javascript: moveSelectItems('removed_users','participating_users');" style="width:75px;font-size:12px;">&raquo;&raquo;</button>
	<br/><br/>
	<?php echo translate('Hold CTRL to select multiple')?>
	<br/><br/>
	<button type="button" id="remove_from_participate" class="button" onclick="javascript: moveSelectItems('participating_users','removed_users');" style="width:75px;font-size:12px;">&laquo;&laquo;</button>
	</td>
	<td width="200" align="center">
	<?php echo translate('Particpating Users')?><br/>
	<select name="participating_users[]" id="participating_users" class="textbox" multiple="multiple" size="10" style="width:195px;">
	<?php
	for ($i = 0; $i < count($user_info); $i++) {
		if ($user_info[$i]['invited'] != 1 && $user_info[$i]['owner'] != 1) {
			echo "<option value=\"{$user_info[$i]['user_id']}|{$user_info[$i]['email']}\">{$user_info[$i]['last_name']}, {$user_info[$i]['first_name']}</option>";
		}
	}
	?>
	</select>
	</td>
	<?php
}


/**
* Prints out lists of all of the invited and all of the participating users
* @param array $user_info users array from the Reservation object
*/
function print_invited_particpating_users($user_info){
	$invited = $participating = '';
	for ($i = 0; $i < count($user_info); $i++) {
		if ($user_info[$i]['invited'] == 1) {
			$invited .= "<p>{$user_info[$i]['last_name']} , {$user_info[$i]['first_name']}</p>";
		}
		else if ($user_info[$i]['owner'] != 1){
			$participating .= "<p>{$user_info[$i]['last_name']} , {$user_info[$i]['first_name']}</p>";
		}
	}
?>
	<td style="width:48%; vertical-align:top;">
		<p style="font-weight:bold;"><?php echo translate('Invited Users')?></p>
		<?php echo$invited?>
	</td>
	<td>&nbsp;</td>
	<td style="width:48%; vertical-align:top;">
		<p style="font-weight:bold;"><?php echo translate('Particpating Users')?></p>
		<?php echo$participating?>
	</td>
<?php
}

/**
* Closes all tags opened by begin_container()
* @param none
*/
function end_container() {
?>
	<!-- end_container() -->
    </td>
  </tr>
</table>
<?php
}

/**
* Prints all the buttons and hidden fields
* @param object $res Reservation object to work with
*/
function print_buttons_and_hidden(&$res) {
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td>
<?php
	$type = $res->get_type();
      // Print buttons depending on type
    echo '<p>';
	switch($type) {
		case RES_TYPE_MODIFY :
			if (!empty($_SESSION['sessionID']) && $res->check_horizon($_SESSION['sessionID'])) {
				echo '<input type="submit" name="submit" value="' . translate('Modify') . '" class="button" onclick="selectUsers();"/>'
					 . '<input type="hidden" name="fn" value="'.RES_TYPE_MODIFY.'" />';
  	  }
	    break;
    case RES_TYPE_DELETE :
      echo '<input type="submit" name="submit" value="' . translate('Delete') . '" class="button" />'
				. '<input type="hidden" name="fn" value="'.RES_TYPE_DELETE.'" />';
	    break;
    case RES_TYPE_VIEW :
      echo '<input type="button" name="close" value="' . translate('Close Window') . '" class="button" onclick="window.close();" /></p>';
	    break;
    case RES_TYPE_ADD :
      echo '<input type="submit" name="submit" value="' . translate('Save') . '" class="button" onclick="selectUsers();"/>'
			   . '<input type="hidden" name="fn" value="'.RES_TYPE_ADD.'" />';
      break;
    case RES_TYPE_SIGNIN :
      echo '<input type="submit" name="submit" value="Sign In" class="button"/>'
				 . '<input type="hidden" name="fn" value="'.RES_TYPE_SIGNIN.'" />';
      break;
    case RES_TYPE_SIGNOUT :
      echo '<input type="submit" name="submit" value="Sign Out" class="button"/>'
		     . '<input type="hidden" name="fn" value="'.RES_TYPE_SIGNOUT.'" />';
      break;
		case RES_TYPE_APPROVE :
			echo '<input type="submit" name="submit" value="' . translate('Approve') . '" class="button"/>'
				 . '<input type="hidden" name="fn" value="'.RES_TYPE_APPROVE.'" />';
  }

  if ($type == RES_TYPE_MODIFY && Auth::isAdmin()) {
  	echo '&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value="' . translate('Delete') . '" class="button" onclick="setFNtype(\'delete\');" />';
  }

  // Print cancel button as long as type is not "view"
	if ($type != RES_TYPE_VIEW)
		echo '&nbsp;&nbsp;&nbsp;<input type="button" name="close" value="' . translate('Cancel') . '" class="button" onclick="window.close();" /></p>';

	// print hidden fields
	if ($res->get_type() == RES_TYPE_ADD) {
        echo '<input type="hidden" name="machid" value="' . $res->get_machid(). '" />' . "\n"
			  . '<input type="hidden" name="lab_id" value="' . $res->lab_data['lab_id'] . '" />' . "\n"
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
* Print out information about this resource
* This function prints out a table containing
*  all information about a given resource
* @param array $rs array of resource information
*/
function print_equipment_data(&$rs, $colspan = 1) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr class="tableBorder">
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td width="100" class="formNames"><?php echo translate('Location')?></td>
          <td class="cellColor"><?php echo $rs['location']?>
          </td>
        </tr>
        <tr>
          <td width="100" class="formNames"><?php echo translate('Phone')?></td>
          <td class="cellColor"><?php echo $rs['rphone']?>
          </td>
        </tr>
        <tr>
          <td width="100" class="formNames"><?php echo translate('Notes')?></td>
          <td class="cellColor"><?php echo $rs['notes']?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
<?php
}


/**
* Print out available times or current reservation's time
* This function will print out all available times to make
*  a reservation or will print out the selected reservation's time
*  (if this is a view).
* @param object $res resource object
* @param object $rs reservation object
* @param bool $print_min_max bool whether to print the min_max cells
* @param bool $allow_multi bool if multiple day reseravtions are allowed
* @global $conf
*/
function print_time_info($res, $rs, $print_min_max = true, $allow_multi = false) {

	$type = $res->get_type();
	$interval = $res->lab_data['timeSpan'];
	$startDay = $res->lab_data['dayStart'];
	$endDay	  = $res->lab_data['dayEnd'];
?>
    <div style="display:none;"><?php var_dump($res);?></div>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
     <tr class="tableBorder">
      <td>
       <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
         <td colspan="2" class="cellColor">
         <h5 align='center'>
<?php
         // Print message depending on viewing type
         switch($type) {
            case RES_TYPE_ADD : $msg = translate('Please select the starting and ending times');
                break;
            case RES_TYPE_MODIFY : $msg = translate('Please change the starting and ending times');
                break;
            default : $msg = translate('Reserved time');
                break;
        }
        echo $msg;
?>
        </h5>
        </td>
       </tr>
	   <tr>
	   <td class="formNames"><?php echo translate('Start')?></td>
	   <td class="formNames"><?php echo translate('End')?></td>
	   </tr>
      <tr>
<?php
		$start_date = $res->get_start_date();
		$end_date = $res->get_end_date();
        // Show reserved time or select boxes depending on type
        if ( ($type == RES_TYPE_ADD) || ($type == RES_TYPE_MODIFY) || Auth::isAdmin()) {


            // Start time select box
            echo '<td class="formNames" width="50%">';
            //echo '<td class="formNames" width="50%"><div id="div_start_date" style="float:left;width:86px;">' . CmnFns::formatDate($start_date) . '</div>';
            //echo '<input type="hidden" id="hdn_start_date" name="start_date" value="' . date('m' . INTERNAL_DATE_SEPERATOR . 'd' . INTERNAL_DATE_SEPERATOR . 'Y', $start_date) . '" onchange="checkCalendarDates();"/>';

            // Using jquery datepicker as standard across all browsers.
            global $dates;
            echo '
                <script>
                $(document).ready(function() {
                    /*if ( $(\'[type="date"]\').prop(\'type\') != \'date\' ) {*/
                        $(\'input.date\').datepicker({
                            dateFormat: \''.$dates['javascript_date'].'\'
                        });
                    /*}*/
                });
                </script>
                ';

            echo '<input type="text" name="start_date" id="hdn_start_date" value="' . date($dates['general_date_format'], $start_date) . '" class="date" required ';
            if ($allow_multi) {
                echo 'onchange="updateMultiDayReservationEndDate(this.value);">';
            } else {
                echo 'onchange="updateReservationEndDate(this.value);">';
            }

            echo '<br/>';

            echo "<select name=\"startTime\" class=\"textbox\">\n";
            // Start at startDay time, end 30 min before endDay
            //var_dump($res);
            //echo $endDay;//+$interval-(int)$rs['minRes'];
            for ($i = $startDay; $i < $endDay+$interval-$rs['minRes']; $i+=$interval) {
                echo '<option value="' . $i . '"';
                // If this is a modification, select current time
                if ( ($res->get_start() == $i) )
                    echo ' selected="selected" ';
                echo '>' . CmnFns::formatTime($i) . '</option>';
            }
            echo "</select>\n</td>\n";


            // End time select box
            echo '<td class="formNames">';
            //echo '<td class="formNames"><div id="div_end_date" style="float:left;width:86px;">' . CmnFns::formatDate($end_date) . '</div>';
            //echo '<input type="hidden" id="hdn_end_date" name="end_date" value="' . date('m' . INTERNAL_DATE_SEPERATOR . 'd' . INTERNAL_DATE_SEPERATOR . 'Y', $end_date) . '" onchange="checkCalendarDates();"/>';
			if ($allow_multi) {
                // todo: localize the date input format
                echo '<input type="text" name="end_date" id="hdn_end_date" value="' . date($dates['general_date_format'], $end_date) . '" class="date" required>';
            } else {
                echo '<input type="hidden" id="hdn_end_date" name="end_date" value="' . date('m' . INTERNAL_DATE_SEPERATOR . 'd' . INTERNAL_DATE_SEPERATOR . 'Y', $end_date) . '" onchange="checkCalendarDates();"/>';
                echo '<span id="end_date_text">' . date($dates['general_date_format'], $end_date) . '</span>';
            }

            echo '<br/>';

            echo "<select name=\"endTime\" class=\"textbox\">\n";
			// Start at 30 after startDay time, end 30 at endDay time
            for ($i = $startDay; $i < $endDay+$interval; $i+=$interval) {
                echo "<option value=\"$i\"";

                // set end time to greater of previous end time or min reservation length.
                if ($res->get_end() >= $res->get_start()+$rs['minRes']) {
                    if ($res->get_end() == $i) {
                        echo ' selected="selected" ';
                    }
                } else {
                    if ((int)$rs['minRes']+$res->get_start() == $i) {
                        echo ' selected="selected" ';
                    }
                }
                                echo '>' . CmnFns::formatTime($i) . "</option>\n";
            }
            echo "</select>\n</td>\n";
			if ($print_min_max & !$allow_multi) {
				echo '</tr><tr class="cellColor">'
						. '<td colspan="2">' . translate('Minimum Reservation Length') . ' ' . CmnFns::minutes_to_hours($rs['minRes'])
						. '<input type="hidden" name="minRes" value="' . $rs['minRes'] . '" />'
						. '</td></tr>'
						. '<tr class="cellColor">'
						. '<td colspan="2">' . translate('Maximum Reservation Length') . ' ' . CmnFns::minutes_to_hours($rs['maxRes'])
						. '<input type="hidden" name="maxRes" value="' . $rs['maxRes'] . '" />'
						. '</td>';
			}
        }
        else {
            echo '<td class="formNames" width="50%"><div id="div_start_date" style="float:left;width:86px;">' . CmnFns::formatDate($start_date) . '</div>' . CmnFns::formatTime($res->get_start()) . "</td>\n"
			      . '<td class="formNames"><div id="div_end_date" style="float:left;width:86px;">' . CmnFns::formatDate($end_date) . '</div>' . CmnFns::formatTime($res->get_end()) . "</td>\n";

        }
        // Close off table
        echo "</tr>\n</table>\n</td>\n</tr>\n</table>\n<p>&nbsp;</p>\n";
}

/**
* Print out information about reservation's owner
* This function will print out information about
*  the selected reservation's user.
* @param string $type viewing type
* @param Object $user User object of this user
*/
function print_user_info($type, $user) {
	if (!$user->is_valid()) {
		$user->get_error();
	}
	$user = $user->get_user_data();
?>
   <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr class="tableBorder">
     <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
       <tr>
        <td colspan="2" class="cellColor"><h5 align="center"><?php echo ($type=='v' || $type=='d') ? translate('Reserved for') : translate('Will be reserved for')?></h5></td></tr>
       <tr>
        <td width="100" class="formNames"><?php echo translate('Name')?></td>
         <td class="cellColor"><div id="name" style="position: relative;float:left;"><?php echo $user['first_name'] . ' ' . $user['last_name']?></div><?php if (Auth::isAdmin() && ($type == RES_TYPE_MODIFY || $type == RES_TYPE_ADD)) { echo "&nbsp;&nbsp;<a href=\"javascript:window.open('user_select.php','selectuser','height=430,width=570,resizable');void(0);\">" . translate('Change') . '</a>'; } ?></td>
          </tr>
          <tr>
           <td width="100" class="formNames"><?php echo translate('Phone')?></td>
           <td class="cellColor"><div id="phone" style="position: relative;"><?php if (array_key_exists('phone', $user)) echo $user['phone']?></div></td>
          </tr>
          <tr>
           <td width="100" class="formNames"><?php echo translate('Email')?></td>
           <td class="cellColor"><div id="email" style="position: relative;"><?php echo $user['email']?></div></td>
          </tr>
        </table>
      </td>
     </tr>
    </table>
    <p>&nbsp;</p>
    <?php
	//unset($user);
}


/**
* Print out information about reservation's billed account
* This function will print out information about
*  the selected reservation's billed account.
* @param Object $res Reservation object of this reservation
*/
function print_account_info($res, $user) {
	$type = $res->get_type();
	//echo $res->get_account_id();

	//$account = Account::get_account_data($res->get_account_id());
	//$accounts = $res->db->get_table_data('accounts', array('account_id', 'FRS', 'sub_FRS', 'name', 'pi_last_name'), array('FRS', 'account_id'), NULL, NULL, NULL, NULL);

	//	if admin get all accounts
	if(Auth::isAdmin()){
		$accounts = $user->db->get_table_data('admin_accounts', array('account_id', 'FRS', 'sub_FRS', 'name', 'pi_last_name', 'status'), array('FRS'), NULL, NULL, NULL, NULL);
	}else{
		//echo "here";
		$accounts = $user->get_accounts_list();
	}
	//var_dump($accounts);
?>
   <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr class="tableBorder">
     <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
       <tr>
        <td colspan="2" class="cellColor"><h5 align="center"><?php echo ($type==RES_TYPE_VIEW || $type==RES_TYPE_DELETE) ? 'Charge to' : 'Will be charged to'?></h5></td></tr>
       <tr>
        <td width="100" class="formNames" nowrap="nowrap">Account<br><br><a href="help_accounts.php#reserveHelp" target="_blank"><strong>Need Help?</strong></a>
        </td>
         <td class="cellColor">
		<?php
			//var_dump($accounts);
			if (($type == RES_TYPE_MODIFY || $type == RES_TYPE_ADD)) {
		?>
		 	<select name="account_id" id="account_id_box" class="textbox">
			<option value="0">[Account # : Sub #] (Owner/PI Last Name, Account Name)</option>
		<?php
			foreach($accounts as $account) {
				//echo $accounts[$i]['account_id'];
				//$account = new Account($accounts[$i]['account_id']);
			
				//$data = $account->get_account_data($account->get_account_id());
				//var_dump($data);
			
				// Option tag
				echo '<option value="' . $account['account_id'] . '"';
				if ($res->get_account_id() == $account['account_id']) {
			
					// Selected attribute
					echo ' selected="selected" ';
				}
			
				// Disabled
				if ($account['status'] == 0)
					echo ' disabled="disabled" ';
			
				// PI name and account name info
				echo '>[' . $account['FRS'];
			
				if (array_key_exists('sub_FRS', $account) && $account['sub_FRS']!='') echo ' : ' . $account['sub_FRS'];
			
				echo ']';
				
				if (!empty($account['pi_last_name']) || !empty($account['name'])) { 
					echo ' (' ;
					if (!empty($account['pi_last_name'])) {
						echo $account['pi_last_name'];
					}
					if (!empty($account['pi_last_name']) && !empty($account['name'])) {
						echo ", " . $account['name'];
					} else if (!empty($account['name'])) {
						echo  $account['name'];
					}
					echo ')';
				}
			/*
				if ( ($account['pi_last_name']!='' || $account['pi'] != 0) || $account['name']!=''){
					echo ' (' ;
					if ($account['pi_last_name'] != '')
						echo $account['pi_last_name'];
					else {
						$pi = new User($data['pi']);
						echo $pi->get_last_name();
					}
					if ($account['name']!='')
						echo  ', ' . substr($account['name'], 0, 20);
					echo ')';
				}
			*/
				if ($account['status'] == 0)
					echo ' Inactive';
			
				echo '</option>
						';
			}
				/*
				for ($i = 0; $i < sizeof($accounts); $i++) {
					//echo $accounts[$i]['account_id'];
					$account = new Account($accounts[$i]['account_id']);

					$data = $account->get_account_data($account->get_account_id());
					//var_dump($data);

					// Option tag
					echo '<option value="' . $account->get_account_id() . '"';
					if ($res->get_account_id() == $data['account_id']) {

						// Selected attribute
						echo ' selected="selected" ';
					}

					// Disabled
					if ($data['status'] == 0)
						echo ' disabled="disabled" ';

					// PI name and account name info
					echo '>[' . $data['FRS'];

					if ($data['sub_FRS']!='') echo ' : ' . $data['sub_FRS'];

					echo ']';

					if ( ($data['pi_last_name']!='' || $data['pi'] != 0) || $data['name']!=''){
						echo ' (' ;
						if ($data['pi_last_name'] != '')
							echo $data['pi_last_name'];
						else {
							$pi = new User($data['pi']);
							echo $pi->get_last_name();
						}
						if ($data['name']!='')
							echo  ', ' . substr($data['name'], 0, 20);
						echo ')';
					}

					if ($data['status'] == 0)
						echo ' Inactive';

					echo '</option>
					';
				}
			*/
			?>
				</select>
                <br /><br />
                If the account is labled "Inactive", the <strong>Account Owner/PI</strong> needs to contact a <a href="http://www.nanocenter.umd.edu/staff/staff_list.php" target="_blank">nanocenter member</a> to clear the account.
                <br /><br />
                If the accounts dropdown list is empty or the account you wish to charge is missing, you will need to contact the Account Owner/PI to authorize you or create the account if you have permission to do so.  See the
                <a href="" onclick="javascript: window.opener.document.location.href='http://<?php echo $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], basename($_SERVER['PHP_SELF'])));?>my_accounts.php'">My Accounts</a> page.
						</td>
					</tr>
				</table>
			<?php
			}else{
				$account = $res->db->get_table_data('accounts', array('account_id', 'FRS', 'sub_FRS', 'name', 'pi_last_name'), array('FRS'), NULL, NULL, ' WHERE account_id = ?', array($res->get_account_id()));
			?><!--
				<table>
					<tr><td>FRS</td>
						<td>
						<?php echo $account[0]['FRS'] ?>
						</td>
					</tr>


					<?php if ($account[0]['sub_FRS']!='') { ?>
					<tr><td>Sub-FRS</td>
						<td>
							<?php echo $account[0]['sub_FRS'] ?>
						</td>
					</tr>
					<?php } ?>

					<?php if ($account[0]['name']!='') { ?>
					<tr><td>Account name</td>
						<td>
							<?php echo $account[0]['name'] ?>
						</td>
					</tr>
					<?php } ?>

					<?php if ($account[0]['pi_last_name']!='') { ?>
					<tr><td>PI</td>
						<td>
							<?php echo $account[0]['pi_last_name'] ?>
						</td>
					</tr>
					<?php } ?>
				</table>
			-->
				<?php
					$account_data = $res->get_res_account_data();
					if($account_data){
						echo $account_data['FRS'] . ' - ' . $account_data['pi_last_name'];
						if($account_data['name']!='')
							echo ' - '.$account_data['name'];
						else if($account_data['pi_first_name']!='')
							echo ', '.$account_data['pi_first_name'];
						//print_r ($account_data);
					}else
						echo $res->get_account_id();
				?>
			<?php
			}
		?>
			</td>
          </tr>
        </table>
      </td>
     </tr>
    </table>
    <p>&nbsp;</p>
    <?php
	//unset($account);
}


/**
* Get user input about usage
*/
function print_signout() {
?>
   <p>&nbsp;</p>
   <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr class="tableBorder">
     <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
       <tr>
        <td colspan="2" class="cellColor"><h5 align="center">Signout Info</h5></td></tr>
       <tr>
        <td width="100" class="formNames">Description of usage</td>
        <td class="cellColor"><textarea name="use_description" cols="30" rows="3"></textarea></td>
       </tr>
       <tr>
        <td width="100" class="formNames">Notes</td>
        <td class="cellColor"><textarea name="notes" cols="30" rows="3"></textarea></td>
       </tr>
       <tr>
        <td width="100" class="formNames">Problems</td>
        <td class="cellColor"><textarea name="problems" cols="30" rows="3"></textarea></td>
       </tr>
      </table>
     </td>
    </tr>
   </table>
   <p>&nbsp;</p>
    <?php
}


/**
* Print out created and modifed times in a table, if they exist
* @param int $c created timestamp
* @param int $m modified stimestamp
*/
function print_create_modify($c, $m, $d = '', $d_by = '', $d_by_email = '') {
?>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr class="tableBorder">
     <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
       <tr>
       <td class="formNames"><?php echo translate('Created')?></td>
       <td class="cellColor"><?php echo CmnFns::formatDateTime($c)?></td>
	   </tr>
       <tr>
       <td class="formNames"><?php echo translate('Last Modified')?></td>
       <td class="cellColor"><?php echo !empty($m) ? CmnFns::formatDateTime($m) : translate('N/A') ?></td>
       </tr>
       <tr>
       <td class="formNames"><?php echo 'Deleted'?></td>
       <td class="cellColor"><?php echo ($d !== '0000-00-00 00:00:00') ? $d . ' by: <a href="mailto:'.$d_by_email.'">'.$d_by.'</a>'  : translate('N/A') ?></td>
       </tr>
      </table>
     </td>
    </tr>
   </table>
   <p>&nbsp;</p>
<?php
}

/**
* Prints out a checkbox to modify all recurring reservations associated with this one
* @param string $parentid id of parent reservation
*/
function print_recur_checkbox($parentid) {
	?>
	<p align="left"><input type="checkbox" name="mod_recur" value="<?php echo $parentid?>" /><?php echo translate('Update all recurring records in group')?></p>
	<?php
}

function print_del_checkbox() {
?>
    <p align="left"><input type="checkbox" name="del" id="del_checkbox" value="true" /><label for="del_checkbox"><?php echo translate('Delete?')?></label></p>
<?php
}

/**
* Prints a box where users can select if they want
*  to repeat a reservation
* @param int $month month of current reservation
* @param int $year year of current reservation
*/
function print_repeat_box($month, $year) {
	global $days_abbr;
?>

<table width="200" border="0" cellspacing="0" cellpadding="0" class="recur_box" id="repeat_table">
  <tr>
    <td style="padding: 5px;">
	 <p style="margin-bottom: 8px;">
	  <?php echo translate('Repeat every')?><br/>
	  <select name="frequency" class="textbox">
	    <option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
		<option value="10">10</option>
	  </select>
      <select name="interval" class="textbox" onchange="javascript: showHideDays(this);">
	    <option value="none"><?php echo translate('Never')?></option>
	    <option value="day"><?php echo translate('Days')?></option>
	    <option value="week"><?php echo translate('Weeks')?></option>
		<option value="month_date"><?php echo translate('Months (date)')?></option>
	    <option value="month_day"><?php echo translate('Months (day)')?></option>
      </select>
    </p>
	<div id="week_num" style="position: relative; visibility: hidden; overflow: show; display: none;">
	<p>
	<select name="week_number" class="textbox">
	  <option value="1"><?php echo translate('First Days')?></option>
	  <option value="2"><?php echo translate('Second Days')?></option>
	  <option value="3"><?php echo translate('Third Days')?></option>
	  <option value="4"><?php echo translate('Fourth Days')?></option>
	  <option value="last"><?php echo translate('Last Days')?></option>
	</select>
	</p>
	</div>
	<div id="days" style="position: relative; visibility: hidden; overflow: show; display: none;">
        <p style="margin-bottom: 8px;">
		<?php echo translate('Repeat on')?><br/>
        <input type="checkbox" name="repeat_day[]" value="0" /><?php echo $days_abbr[0]?><br />
		<input type="checkbox" name="repeat_day[]" value="1" /><?php echo $days_abbr[1]?><br />
		<input type="checkbox" name="repeat_day[]" value="2" /><?php echo $days_abbr[2]?><br />
		<input type="checkbox" name="repeat_day[]" value="3" /><?php echo $days_abbr[3]?><br />
		<input type="checkbox" name="repeat_day[]" value="4" /><?php echo $days_abbr[4]?><br />
		<input type="checkbox" name="repeat_day[]" value="5" /><?php echo $days_abbr[5]?><br />
		<input type="checkbox" name="repeat_day[]" value="6" /><?php echo $days_abbr[6]?>
        </p>
	</div>
	<div id="until" style="position: relative;">
		<p>
		<?php echo translate('Repeat until date')?>
        <input type="date" name="repeat_until" id="repeat_until" value="" />
		</p>
	</div>
	</td>
  </tr>
</table>
<?php
}

/**
* Prints a box where users can select if they want
*  to repeat a reservation
* @param int $month month of current reservation
* @param int $year year of current reservation
*/
function print_pending_approval_msg() {
?>
<br />
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
            <td style="padding: 5px;">
	           <p style="font-weight: bold;" align="center"><?php echo translate('This reservation must be approved by the administrator.')?></p>
	       </td>
        </tr>
      </table>
     </td>
    </tr>
</table>
<?php
}

/**
* Print out the reservation summary or a box to add/edit one
* @param string $summary summary to edit
* @param string $type type of reservation
*/
function print_summary($summary, $type) {
?>
   <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr class="tableBorder">
     <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
       <tr>
	    <td class="cellColor"><h5 align="center">Description of Operation</h5></td>
		</tr>
		<tr>
		<td class="cellColor" style="text-align: left;">
		<?php
		if ($type == RES_TYPE_ADD || $type == RES_TYPE_MODIFY)
			echo '<div style="text-align:center;" id="summary_div"><textarea class="textbox" name="summary" rows="5" cols="45">' . $summary . '</textarea></div>';
		else
			echo (!empty($summary) ? CmnFns::html_activate_links($summary) : translate('N/A'));
		?>
		</td>
	   </tr>
      </table>
     </td>
    </tr>
   </table>
<?php
}

/**
* Closes reserve form
* @param none
*/
function end_reserve_form() {
	echo "</form>\n";
}

/**
* Splits the table into two columns
*/
function divide_table() {
?>
</td>
<td style="vertical-align: top; padding-left: 15px;">
<?php
}


/**
*
*
**/
function print_res_note_box($resid, $action, $billing_note, $technical_note=''){
?>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>">
	<table width="100%" border="0" cellspacing="0" cellpadding="1">
		<tr class="tableBorder">
			<td>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr>
						<td class="cellColor">
							<h5 align="center"><?php echo ucwords($action) ?> Technical Note</h5>
						</td>
					</tr>
					<tr>
						<td class="cellColor" style="text-align: left;">
							<div style="text-align:center;" id="summary_div">
								<textarea class="textbox" name="technical_note" rows="5" cols="45"><?php echo $technical_note ?></textarea>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br>
	<table width="100%" border="0" cellspacing="0" cellpadding="1">
		<tr class="tableBorder">
			<td>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr>
						<td class="cellColor">
							<h5 align="center"><?php echo ucwords($action) ?> Billing Note</h5>
						</td>
					</tr>
					<tr>
						<td class="cellColor" style="text-align: left;">
							<div style="text-align:center;" id="summary_div">
								<textarea class="textbox" name="billing_note" rows="5" cols="45"><?php echo $billing_note ?></textarea>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<center>
	<input type="submit" name="submit" value="Enter Note">
	<input type="hidden" name="resid" value="<?php echo $resid ?>">
	</center>
	</form>
<?php
}

/**
* Prints out the javascript necessary to set up the calendars for choosing recurring dates, start/end dates
* @param Reservation $res reservation to populate the calendar dates with
*/
function print_jscalendar_setup(&$res, $rs) {
	global $dates;
	$allow_multi = (isset($rs['allow_multi']) && $rs['allow_multi'] == 1);
?>
	<script type="text/javascript">
	var now = new Date(<?php echo date('Y', $res->start_date) . ',' . (intval(date('m', $res->start_date))-1) . ',' . date('d', $res->start_date);?>);
	<?php
	//echo "res type: ".$res->get_type();
	if ($res->get_type() == RES_TYPE_ADD) {
	?>
	// Recurring calendar
		Calendar.setup(
			{
			inputField : "repeat_until", // ID of the input field
			ifFormat : "<?php echo '%m' . INTERNAL_DATE_SEPERATOR . '%d' . INTERNAL_DATE_SEPERATOR . '%Y';?>", // the date format
			button : "btn_choosedate", // ID of the button
			date : now,
			displayArea : "_repeat_until",
			daFormat : "<?php echo $dates['general_date'];?>" // the date format
			}
		);
	<?php
	}

	if ($allow_multi && ($res->get_type() == RES_TYPE_ADD || $res->get_type() == RES_TYPE_MODIFY)) {
	?>
		// Start date calendar
		Calendar.setup(
			{
			inputField : "hdn_start_date", // ID of the input field
			ifFormat : "<?php echo '%m' . INTERNAL_DATE_SEPERATOR . '%d' . INTERNAL_DATE_SEPERATOR . '%Y';?>", // the date format
			daFormat : "<?php echo $dates['general_date'];?>", // the date format
			button : "img_start_date", // ID of the button
			date : now,
			displayArea : "div_start_date"
			}
		);

		// End date calendar
		Calendar.setup(
			{
			inputField : "hdn_end_date", // ID of the input field
			ifFormat : "<?php echo '%m' . INTERNAL_DATE_SEPERATOR . '%d' . INTERNAL_DATE_SEPERATOR . '%Y'; ?>", // the date format
			daFormat : "<?php echo $dates['general_date']; ?>", // the date format
			button : "img_end_date", // ID of the button
			date : now,
			displayArea : "div_end_date"
			}
		);
	<?php
	}
	?>
	</script>
<?php
}



function print_add_on_charge_list($add_on_charges_available, $add_on_charges) {
    ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
        <tr class="tableBorder">
            <td>
                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                    <tr>
                        <td class="cellColor"><h5 align="center">Add On Charges</h5></td>
                    </tr>
                    <tr>
                        <td class="cellColor" style="text-align: left;">
                            <ul>
                            <?php
                                foreach($add_on_charges as $charge) {
                                    echo "<li></li>";
                                }
                                ?>
                            </ul>
                            <label for="">Attach another add on charge:</label>
                                <?php
                                echo "<select name=''>";
                                foreach($add_on_charges as $aoc) {
                                    echo "<option value=''>" . $aoc[''] . "</option>";
                                }
                                echo "</select>";
                                ?>
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?php
}

?>