<?php
/**
 * This file provides output functions for the admin class
 * No data manipulation is done in this file
 * @author Nick Korbel <lqqkout13@users.sourceforge.net>
 * @author David Poole <David.Poole@fccc.edu>
 * @version 07-20-05
 * @package Templates
 *
 * Copyright (C) 2003 - 2005 phpScheduleIt
 * License: GPL, see LICENSE
 */

$link = CmnFns::getNewLink();	// Get Link object
/**
 * Return the tool name
 * @param none
 */
function getTool() {
	if (isset($_GET['tool'])) {
		return filter_input(INPUT_GET, 'tool', FILTER_SANITIZE_SPECIAL_CHARS);
	}else{
		return false;
	}
}

/**
 * Prints out list of current labs
 * @param Object $pager pager object
 * @param mixed $labs array of lab data
 * @param string $err last database error
 */
function print_manage_labs(&$pager, $labs, $err) {
	global $link;

	?>
<form name="manageLab" method="post" action="admin_update.php"
	onsubmit="return checkAdminForm();">
<table width="100%" border="0" cellspacing="0" cellpadding="1"
	align="center">
	<tr>
		<td class="tableBorder">
		<table width="100%" border="0" cellspacing="1" cellpadding="0">
			<tr>
				<td colspan="9" class="tableTitle">&#8250; <?php echo translate('All Labs')?></td>
			</tr>
			<tr class="rowHeaders">
				<!-- <td><?php echo translate('Lab Title')?></td> -->
				<td>Lab Nickname</td>
				<td width="8%"><?php echo translate('Start Time')?></td>
				<td width="8%"><?php echo translate('End Time')?></td>
				<td width="9%"><?php echo translate('Time Span')?></td>
				<td width="11%"><?php echo translate('Weekday Start')?></td>
				<td width="20%"><?php echo translate('Admin Email')?></td>
				<td width="7%"><?php echo translate('Default')?></td>
				<td width="5%"><?php echo translate('Edit')?></td>
				<td width="5%">Permissions</td>
				<td width="7%"><?php echo translate('Delete')?></td>
			</tr>
			<?php

			if (!$labs)
			echo '<tr class="cellColor0"><td colspan="9" style="text-align: center;">' . $err . '</td></tr>' . "\n";

			for ($i = 0; is_array($labs) && $i < count($labs); $i++) {
				$cur = $labs[$i];
				echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\" id=\"tr$i\">\n";
				echo '<td style="text-align:left" title="';
				echo $cur['labTitle'];
				echo '">';
				echo $cur['nickname'];
				echo "</td>\n";
				echo '<td style="text-align:left">';
				echo CmnFns::formatTime($cur['dayStart']);
				echo "</td>\n";
				echo '<td style="text-align:left">';
				echo CmnFns::formatTime($cur['dayEnd']);
				echo "</td>\n";
				echo '<td style="text-align:left">';
				echo CmnFns::minutes_to_hours($cur['timeSpan']);
				echo "</td>\n";
				echo '<td style="text-align:left">';
				echo CmnFns::get_day_name($cur['weekDayStart'], 0);
				echo "</td>\n";
				echo '<td style="text-align:left">';
				echo $cur['adminEmail'];
				echo "</td>\n";
				echo '<td><input type="radio" value="';
				echo $labs[$i]['lab_id'];
				echo "\" name=\"isDefault\"";
				echo ($labs[$i]['isDefault'] == 1 ? ' checked="checked"' : '');
				echo ' onclick="javacript: setLab(\'';
				echo $labs[$i]['lab_id'];
				echo '\');" /></td>';
				echo '<td>';
				echo $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&lab_id=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;lab_id=' . $cur['lab_id'] . ((strpos($_SERVER['QUERY_STRING'], $pager->getLimitVar())===false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''), translate('Edit'), '', '', translate('Edit data for', array($cur['labTitle'])));
				echo "</td>\n";
				echo '<td>';
				echo $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&lab_id=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;lab_id=' . $cur['lab_id'] . ((strpos($_SERVER['QUERY_STRING'], $pager->getLimitVar())===false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''), translate('Permissions'), '', '', translate('Edit lab permissions', array($cur['labTitle'])));
				echo "</td>\n";
				echo "<td><input type=\"checkbox\" name=\"lab_id[]\" value=\"";
				echo $cur['lab_id'];
				echo "\" onclick=\"adminRowClick(this,'tr$i',$i);\"/></td>\n";
				echo "</tr>\n";
			}

			// Close table
			?>
		</table>
		</td>
	</tr>
</table>
<br />
			<?php
			echo submit_button(translate('Delete'), 'lab_id') . hidden_fn('delLab');
			?></form>
<form id="setDefaultLab" name="setDefaultLab" method="post"
	action="admin_update.php"><input type="hidden" name="lab_id" value="" />
<input type="hidden" name="fn" value="dfltLab" /></form>
			<?php
}

/**
 * Interface to add or edit lab information
 * @param mixed $rs array of lab data
 * @param boolean $edit whether this is an edit or not
 * @param object $pager Pager object
 */
function print_lab_edit($rs, $edit, &$pager) {
	global $conf;
	?>
<form name="addLab" method="post" action="admin_update.php"
<?php echo  $edit ? "" : "onsubmit=\"return checkAddLab();\"" ?>>
<table width="100%" border="0" cellspacing="0" cellpadding="1"
	align="center">
	<tr>
		<td class="tableBorder">
		<table width="100%" border="0" cellspacing="1" cellpadding="0">
			<tr>
				<td width="200" class="formNames"><?php echo translate('Lab Title')?></td>
				<td class="cellColor"><input type="text" name="labTitle"
					class="textbox"
					value="<?php echo  isset($rs['labTitle']) ? $rs['labTitle'] : '' ?>"
					size="75" /></td>
			</tr>
			<tr>
				<td width="200" class="formNames">Lab Nickname</td>
				<td class="cellColor"><input type="text" name="nickname"
					class="textbox"
					value="<?php echo  isset($rs['nickname']) ? $rs['nickname'] : '' ?>"
					size="75" /></td>
			</tr>
			<tr>
				<td width="200" class="formNames">Building</td>
				<td class="cellColor"><input type="text" name="building"
					class="textbox"
					value="<?php echo  isset($rs['building']) ? $rs['building'] : '' ?>"
					size="75" /></td>
			</tr>
			<tr>
				<td width="200" class="formNames">Room Number</td>
				<td class="cellColor"><input type="text" name="room_number"
					class="textbox"
					value="<?php echo  isset($rs['room_number']) ? $rs['room_number'] : '' ?>" />
				</td>
			</tr>
			<tr>
				<td width="200" class="formNames">URL</td>
				<td class="cellColor"><input type="text" name="url" class="textbox"
					value="<?php echo  isset($rs['url']) ? $rs['url'] : '' ?>" size="75" /></td>
			</tr>
			<tr>
				<td width="200" class="formNames">Phone</td>
				<td class="cellColor"><input type="text" name="phone"
					class="textbox"
					value="<?php echo  isset($rs['phone']) ? $rs['phone'] : '' ?>" /></td>
			</tr>
			<tr>
				<td width="200" class="formNames">Type</td>
				<td class="cellColor"><select name="type" class="textbox">
					<option value=""
					<?php echo  ((isset($rs['type']) && ($rs['type'] == "")) ? ' selected="selected"' : '') ?>>None</option>
					<option value="shared"
					<?php echo  ((isset($rs['type']) && ($rs['type'] == "shared")) ? ' selected="selected"' : '') ?>>Shared</option>
					<option value="collaborative"
					<?php echo  ((isset($rs['type']) && ($rs['type'] == "collaborative")) ? ' selected="selected"' : '') ?>>Collaborative</option>
					<option value="research"
					<?php echo  ((isset($rs['type']) && ($rs['type'] == "research")) ? ' selected="selected"' : '') ?>>Research</option>
					<option value="partner"
					<?php echo  ((isset($rs['type']) && ($rs['type'] == "partner")) ? ' selected="selected"' : '') ?>>Partner</option>
					<option value="instructional"
					<?php echo  ((isset($rs['type']) && ($rs['type'] == "instructional")) ? ' selected="selected"' : '') ?>>Instructional</option>
				</select></td>
			</tr>
			<tr>
				<td class="formNames"><?php echo translate('Start Time')?></td>
				<td class="cellColor"><select name="dayStart" class="textbox">
				<?php
		  for ($time = 0; $time <= 1410; $time += 30)
		  echo '<option value="' . $time . '"' . ((isset($rs['dayStart']) && ($rs['dayStart'] == $time)) ? ' selected="selected"' : '') . '>' . CmnFns::formatTime($time) . '</option>' . "\n";
		  ?>
				</select></td>
			</tr>
			<tr>
				<td class="formNames"><?php echo translate('End Time')?></td>
				<td class="cellColor"><select name="dayEnd" class="textbox">
				<?php
		  for ($time = 30; $time <= 1440; $time += 30)
		  echo '<option value="' . $time . '"' . ((isset($rs['dayEnd']) && ($rs['dayEnd'] == $time)) ? (' selected="selected"') : (($time==1440 && !isset($rs['dayEnd'])) ? ' selected="selected"' : '')) . '>' . CmnFns::formatTime($time) . '</option>' . "\n";
		  ?>
				</select></td>
			</tr>
			<tr>
				<td class="formNames"><?php echo translate('Time Span')?></td>
				<td class="cellColor"><select name="timeSpan" class="textbox">
				<?php
		  $spans = array (30, 10, 15, 60, 120, 180, 240);
		  for ($i = 0; $i < count($spans); $i++)
		  echo '<option value="' . $spans[$i] . '"' . ((isset($rs['timeSpan']) && ($rs['timeSpan'] == $spans[$i])) ? (' selected="selected"') : '') . '>' . CmnFns::minutes_to_hours($spans[$i]) . '</option>' . "\n";
		  ?>
				</select></td>
			</tr>
			<tr>
				<td class="formNames"><?php echo translate('Weekday Start')?></td>
				<td class="cellColor"><select name="weekDayStart" class="textbox">
				<?php
		  for ($i = 0; $i < 7; $i++)
		  echo '<option value="' . $i . '"' . ( (isset($rs['weekDayStart']) && $rs['weekDayStart'] == $i) ? ' selected="selected"' : '') . '>' . CmnFns::get_day_name($i) . '</option>' . "\n";
		  ?>
				</select></td>
			</tr>
			<tr>
				<td class="formNames"><?php echo translate('Days to Show')?></td>
				<td class="cellColor"><input type="text" name="viewDays"
					class="textbox" size="2" maxlength="2"
					value="<?php echo  isset($rs['viewDays']) ? $rs['viewDays'] : '7' ?>" /></td>
			</tr>
			<tr>
				<td class="formNames"><?php echo translate('Reservation Offset')?></td>
				<td class="cellColor"><input type="text" name="dayOffset"
					class="textbox" size="2" maxlength="2"
					value="<?php echo  isset($rs['dayOffset']) ? $rs['dayOffset'] : '0' ?>" />
				</td>
			</tr>
			<tr>
				<td class="formNames"><?php echo translate('Hidden')?></td>
				<td class="cellColor"><select name="isHidden" class="textbox">
				<?php
		  $yesNo = array(translate('No'), translate('Yes'));
		  for ($i = 0; $i < 2; $i++)
		  echo '<option value="' . $i . '"' . ((isset($rs['isHidden']) && ($rs['isHidden'] == $i)) ? (' selected="selected"') : '') . '>' . $yesNo[$i]  . '</option>' . "\n";
		  ?>
				</select></td>
			</tr>
			<tr>
				<td class="formNames"><?php echo translate('Show Summary')?></td>
				<td class="cellColor"><select name="showSummary" class="textbox">
				<?php
		  for ($i = 1; $i >= 0; $i--)
		  echo '<option value="' . $i . '"' . ((isset($rs['showSummary']) && ($rs['showSummary'] == $i)) ? (' selected="selected"') : '') . '>' . $yesNo[$i]  . '</option>' . "\n";
		  ?>
				</select></td>
			</tr>
			<tr>
				<td class="formNames"><?php echo translate('Admin Email')?></td>
				<td class="cellColor"><input type="text" name="adminEmail"
					maxlength="75" class="textbox"
					value="<?php echo  isset($rs['adminEmail']) ? $rs['adminEmail'] : $conf['app']['adminEmail'] ?>" />
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
		  	echo submit_button(translate('Add Lab'), 'lab_id') . hidden_fn('addLab')
		  	. ' <input type="reset" name="reset" value="' . translate('Clear') . '" class="button" />' . "\n";
		  }
		  else {
		  	echo submit_button(translate('Edit Lab'), 'lab_id') . cancel_button($pager) . hidden_fn('editLab')
		  	. '<input type="hidden" name="lab_id" value="' . $rs['lab_id'] . '" />' . "\n";
		  	// Unset variables
		  	unset($rs);
		  }
		  echo "</form>\n";
}

/**
 * Prints out the user management table
 * @param Object $pager pager object
 * @param mixed $users array of user data
 * @param string $err last database error
 */
function print_manage_lab_users(&$pager, $lab_id, $lab_name, $all_users, $trained_users, $err) {
	global $link;
	$trained = array();
	$viewed_users = array();

	foreach ($trained_users as $t) {
			$uid = $t['user_id'];

			$trained[$uid]['safety_trained'] = $t['safety_trained'];
			if (is_numeric($t['trained_by']))
				$trained[$uid]['trained_by'] = $t['first_name']." ".$t['last_name'];
			else
				$trained[$uid]['trained_by'] = $t['trained_by'];
			$trained[$uid]['trained_date'] = $t['trained_date'];
			$trained[$uid]['is_admin'] = $t['is_admin'];
	}

	//var_dump($trained);
?>
<h4>You are Managing <?php echo $lab_name; ?> Users</h4>
<form name="manageLabUsers" method="post" action="admin_update.php">
<table width="100%" border="0" cellspacing="0" cellpadding="1"
	align="center">
	<tr>
		<td class="tableBorder">
		<table width="100%" border="0" cellspacing="1" cellpadding="0">
			<tr>
				<td colspan="5" class="tableTitle">&#8250; All <?php echo $lab_name; ?> Users</td>
			</tr>
			<tr class="rowHeaders">
				<td width="6%">Lab Admin</td>
				<td width="21%"><?php echo translate('Name')?></td>
				<td width="25%"><?php echo translate('Email')?></td>
				<td width="6%">Safety Trained</td>
				<td width="14%">Trained <?php echo translate('Date')?></td>
				<td>Trained By</td>
			</tr>
			<tr class="cellColor0" style="text-align: center;">
				<td>&nbsp;</td>
				<td><?php printDescLink($pager, 'last_name', 'last name') ?>
				&nbsp;&nbsp; <?php printAscLink($pager, 'last_name', 'last name') ?></td>
				<td><?php printDescLink($pager, 'email', 'email address') ?>
				&nbsp;&nbsp; <?php printAscLink($pager, 'email', 'email address') ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?php
			//var_dump($trained);
			for ($i = 0; is_array($all_users) && $i < count($all_users); $i++) {
				$cur = $all_users[$i];
				$uid = $cur['user_id'];
				array_push($viewed_users, $uid);		// used to clear this set of permissions in case of removal

				$first_name = 		$cur['first_name'];
				$last_name = 		$cur['last_name'];
				$email = 			$cur['email'];
				$safety_trained = 	$trained[$uid]['safety_trained'];
				$date = 			$trained[$uid]['trained_date'];
				$trainer = 			$trained[$uid]['trained_by'];
				$is_admin =			$trained[$uid]['is_admin'];

				$first_name_last_name = array($first_name, $last_name);

				$admin_link = "user_id={$cur['user_id']}&amp;status=" . (($cur['is_admin'] == 1) ? '0' : '1');
				$admin_text = (($cur['is_admin'] == 1) ? translate('Yes') : translate('No'));

				echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\" id=\"tr$i\">\n";

				// checked if the user is an admin in this lab
				echo '<td><input type="checkbox" name="lab_admin[]" value="' . $cur['user_id'] . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"";
				if ($is_admin) {
					echo " checked ";
				}
				echo "/></td>\n";


				echo '<td style="text-align:left;">' . $link->getLink("javascript: viewUser('". $cur['user_id'] . "');", $first_name . ' ' . $last_name, '', '', translate('View information about', $first_name_last_name)) . "</td>\n"
				. '<td style="text-align:left;">' . $link->getLink("mailto:$email", $email, '', '', translate('Send email to', array($first_name, $last_name))) . "</td>\n";

				// checked if the user is safety trained in this lab
				echo '<td><input type="checkbox" name="trained[]" value="' . $cur['user_id'] . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"";
				if ($safety_trained) {
					echo " checked ";
				}
				echo "/></td>\n";

				echo '<td style="text-align:left;\">' . $date . "</td>\n"
				. '<td style="text-align:left;">' . $trainer . "</td>\n"
				. "</tr>\n";
			}

			// Close users table
			?>
		</table>
		</td>
	</tr>
</table>
<br />
<input type="hidden" name="tool" value="<?php echo getTool()?>" />
<input type="hidden" name="lab_id" value="<?php echo filter_input(INPUT_GET, 'lab_id', FILTER_SANITIZE_SPECIAL_CHARS);?>">
<input type="hidden" name="<?php echo $pager->getLimitVar()?>" value="<?php echo $pager->getLimit()?>" />
<input type="hidden" name="viewed_users" value="<?php echo implode(",", $viewed_users); ?>" />
<?php if (isset($_GET['order'])) { ?>
<input type="hidden" name="order" value="<?php echo filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS); ?>" />
<?php } ?>
<?php if (isset($_GET['vert'])) { ?>
<input type="hidden" name="vert" value="<?php echo filter_input(INPUT_GET, 'vert', FILTER_SANITIZE_SPECIAL_CHARS); ?>" />
<?php } ?>
<?php
echo submit_button(translate('Update')) . hidden_fn('editLabUsers') . '</form>';
?>
<form name="name_search" action="<?php echo $_SERVER['PHP_SELF']?>" method="get">
<p align="center"><?php print_last_name_links(); ?></p>
<br />
<p align="center"><?php echo translate('First Name')?>
<input type="text"	 name="firstName" class="textbox" /> <?php echo translate('Last Name')?>
<input type="text" 	 name="lastName" class="textbox" />
<input type="hidden" name="searchUsers" value="true" />
<input type="hidden" name="tool" value="<?php echo getTool()?>" />
<input type="hidden" name="lab_id" value="<?php echo $lab_id; ?>" />
<input type="hidden" name="<?php echo $pager->getLimitVar()?>" value="<?php echo $pager->getLimit()?>" />
<input type="hidden" name="viewed_users" value="<?php echo implode(",", $viewed_users); ?>" />
<?php if (isset($_GET['order'])) { ?>
<input type="hidden" name="order" value="<?php echo filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
<?php } ?>
<?php if (isset($_GET['vert'])) { ?>
<input type="hidden" name="vert" value="<?php echo filter_input(INPUT_GET, 'vert', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
<?php } ?>
<input type="submit" name="searchUsersBtn" value="<?php echo translate('Search Users')?>" class="button" />
</p>
</form>
<?php
}

/**
 * Prints out the user management table
 * @param Object $pager pager object
 * @param mixed $users array of user data
 * @param string $err last database error
 */
function print_manage_users(&$pager, $users, $err) {
	global $link;
	$initial_archived_users = array();
	foreach ($users as $u) {
	    if (array_key_exists('deleted', $u)) {
	        if ($u['deleted'] === "1") {
	            array_push($initial_archived_users, $u['user_id']);
	        }
	    }
	}
	?>
<form name="name_search" action="<?php echo $_SERVER['PHP_SELF']?>" method="get">
<p align="center"><?php print_last_name_links(); ?></p>
<br />
<p align="center"><?php echo translate('First Name')?> <input type="text"
	name="firstName" class="textbox" value="<?php echo filter_input(INPUT_GET, 'firstName');?>" /> <?php echo translate('Last Name')?> <input
	type="text" name="lastName" class="textbox" value="<?php echo filter_input(INPUT_GET, 'lastName');?>" /> <input type="hidden"
	name="searchUsers" value="true" /> <input type="hidden" name="tool"
	value="<?php echo getTool()?>" /> <input type="hidden"
	name="<?php echo $pager->getLimitVar()?>" value="<?php echo $pager->getLimit()?>" /> <?php if (isset($_GET['order'])) { ?>
<input type="hidden" name="order" value="<?php echo filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS);?>" /> <?php } ?>
			<?php if (isset($_GET['vert'])) { ?> <input type="hidden" name="vert"
	value="<?php echo filter_input(INPUT_GET, 'vert', FILTER_SANITIZE_SPECIAL_CHARS);?>" /> <?php } ?> <input type="submit"
	name="searchUsersBtn" value="<?php echo translate('Search Users')?>"
	class="button" />
	<input type="checkbox" name="show_deleted" id="show_deleted" value="1" <?php if(filter_input(INPUT_GET, 'show_deleted')==="1") echo "checked";?>><label for="show_deleted">Show Archived Users?</label>
	</p>
</form>

<form name="manageUser" method="post" action="admin_update.php" onsubmit="return checkAdminForm();">
<input type="hidden" name="initial_archived_users" value="<?php echo implode(",", $initial_archived_users); ?>">
<?php
/*
    foreach ($initial_archived_users as $u) {
        echo "<input type=\"hidden\" name=\"initial_archived_users[]\" value=\"" . $u . "\">\r\n";
    }
*/
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
	<tr>
		<td class="tableBorder">
		<table width="100%" border="0" cellspacing="1" cellpadding="0">
			<tr>
				<td colspan="7" class="tableTitle">&#8250; <?php echo translate('All Users')?>
				</td>
			</tr>
			<tr class="rowHeaders">
				<td width="21%"><?php echo translate('Name')?></td>
				<td width="25%"><?php echo translate('Email')?></td>
				<td width="8%"><?php echo translate('Password')?></td>
				<td width="5%"><?php echo translate('Admin')?></td>
				<td width="10%">Equipment <?php echo translate('Permissions')?></td>
				<td width="10%">Account <?php echo translate('Permissions')?></td>
				<td width="6%"><?php echo translate('Archive')?></td>
			</tr>
			<tr class="cellColor0" style="text-align: center;">
				<td><?php printDescLink($pager, 'last_name', 'last name') ?>
				&nbsp;&nbsp; <?php printAscLink($pager, 'last_name', 'last name') ?></td>
				<td><?php printDescLink($pager, 'email', 'email address') ?>
				&nbsp;&nbsp; <?php printAscLink($pager, 'email', 'email address') ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?php

			if (!$users)
			echo '<tr class="cellColor0"><td colspan="8" style="text-align: center;">' . $err . '</td></tr>' . "\n";

			for ($i = 0; is_array($users) && $i < count($users); $i++) {
				$cur = $users[$i];
				$first_name = $cur['first_name'];
				$last_name = $cur['last_name'];
				$email = $cur['email'];

				$first_name_last_name = array($first_name, $last_name);

				$admin_link = "user_id={$cur['user_id']}&amp;status=" . (($cur['is_admin'] == 1) ? '0' : '1');
				$admin_text = (($cur['is_admin'] == 1) ? translate('Yes') : translate('No'));
				
				if ($cur['deleted'] === "1") {
				    echo "<tr class=\"cellColorDeleted\" align=\"center\" id=\"tr$i\">\n";
				} else {
				    echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\" id=\"tr$i\">\n";
				}
				echo '<td style="text-align:left;">' . $link->getLink("javascript: viewUser('". $cur['user_id'] . "');", $first_name . ' ' . $last_name, '', '', translate('View information about', $first_name_last_name)) . "</td>\n"
				. '<td style="text-align:left;">' . $link->getLink("mailto:$email", $email, '', '', translate('Send email to', array($first_name, $last_name))) . "</td>\n"
				. '<td>' . $link->getLink("admin.php?tool=pwreset&amp;user_id=" . $cur['user_id'], translate('Reset'), '', '', translate('Reset password for', $first_name_last_name)) .  "</td>\n"
				. '<td>' . '<a href="admin_update.php?fn=adminToggle&amp;' . $admin_link . '">' . $admin_text . '</a></td>'
				. '<td>' . $link->getLink("admin.php?tool=perms&amp;user_id=" . $cur['user_id'], translate('Edit'), '', '', translate('Edit permissions for', $first_name_last_name)) . "</td>\n"
				. '<td>' . $link->getLink("admin.php?tool=user_accounts&amp;user_id=" . $cur['user_id'], translate('Edit'), '', '', translate('Edit permissions for', $first_name_last_name)) . "</td>\n"
				. '<td><input type="checkbox" name="user_id[]" value="' . $cur['user_id'] . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"";
				if ($cur['deleted'] === "1") {
				    echo " checked=checked";
				}
				echo "/></td>\n"
				. "</tr>\n";
			}

			// Close users table
			?>
		</table>
		</td>
	</tr>
</table>
<div style="text-align: right;margin: 15px 0;">
<?php echo submit_button(translate('Update')) . hidden_fn('deleteUsers') . '</form>'; ?>
</div>
<!--
<form name="name_search" action="<?php echo $_SERVER['PHP_SELF']?>" method="get">
<p align="center"><?php print_last_name_links(); ?></p>
<br />
<p align="center"><?php echo translate('First Name')?> <input type="text"
	name="firstName" class="textbox" /> <?php echo translate('Last Name')?> <input
	type="text" name="lastName" class="textbox" /> <input type="hidden"
	name="searchUsers" value="true" /> <input type="hidden" name="tool"
	value="<?php echo getTool()?>" /> <input type="hidden"
	name="<?php echo $pager->getLimitVar()?>" value="<?php echo $pager->getLimit()?>" /> <?php if (isset($_GET['order'])) { ?>
<input type="hidden" name="order" value="<?php echo filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS);?>" /> <?php } ?>
			<?php if (isset($_GET['vert'])) { ?> <input type="hidden" name="vert"
	value="<?php echo filter_input(INPUT_GET, 'vert', FILTER_SANITIZE_SPECIAL_CHARS);?>" /> <?php } ?> <input type="submit"
	name="searchUsersBtn" value="<?php echo translate('Search Users')?>"
	class="button" /></p>
</form>
-->
			<?php
}


/**
 * Prints out the user's accounts management lists
 */
function print_manage_users_accounts($user, $user_accounts, $all_accounts){
	?>
<table width="100%">
	<tr>
		<td class="tableBorder">

		<table width="100%">
			<tr>
				<td class="tableTitle">&#8250; <?php echo $user->get_name(); ?>'s
				Accounts</td>
				<td class="tableTitle" align="right"><a href="help_accounts.php"
					target="_blank"><strong>Accounts Help</strong></a></td>
			</tr>

			<?php
			$ua_array = array();
			foreach ($user_accounts as $acct) {
				array_push($ua_array, $acct['account_id']);
			}
			//echo "<!--\r\n";
			//print_r($user_accounts);
			//echo "-->\r\n";
			?>
			<tr class="cellColor">
				<td colspan="2">
				<form name="manage_account_users"
					action="<?php echo  (strstr($_SERVER['PHP_SELF'], "admin.php")) ? "admin_update.php" : $_SERVER['PHP_SELF'] ?>"
					method="post"
					onsubmit="javascript: selectOptions('account_user_list');selectOptions('account_admin_list');">
				<table>
					<tr>
						<td>
						<center><strong>Unauthorized Account List</strong></center>
						<br>
						<select id="all_accounts_list" size="15" style="width: 15em"
							multiple="multiple">
							<?php foreach($all_accounts as $acct){ ?>
							<?php if (!in_array($acct['account_id'], $ua_array)) {?>
							<option value="<?php echo $acct['account_id'] ?>"><?php echo $acct['FRS']; ?>
							- <?php echo ($acct['pi']==NULL || $acct['pi']==0) ? $acct['pi_last_name'] : $acct['pi_ln']; ?></option>
							<?php } ?>
							<?php } ?>
						</select></td>
						<td><input type="button" name="add_user" value=" >> "
							onclick="moveOptions('all_accounts_list','account_user_list')"
							class="button"> <br>
						<br>
						<input type="button" name="remove_user" value=" << "
							onclick="moveOptions('account_user_list','all_accounts_list')"
							class="button"></td>
						<td>
						<center><strong>Authorized Accounts</strong></center>
						<br>
						<select name="account_list[]" id="account_user_list" size="15"
							style="width: 15em" multiple="multiple">
							<?php
							foreach($user_accounts as $acct){
								if(!$acct['is_admin']) {
									?>
							<option value="<?php echo $acct['account_id'] ?>"><?php echo $acct['FRS']; ?>
							- <?php echo ($acct['pi']==NULL || $acct['pi']==0) ? $acct['pi_last_name'] : $acct['pi_ln']; ?></option>
							<?php
								}
							}
							?>
						</select></td>
						<td><input type="button" name="promote_user" value=" >> "
							onclick="moveOptions('account_user_list', 'account_admin_list')"
							class="button"> <br>
						<br>
						<input type="button" name="demote_user" value=" << "
							onclick="moveOptions('account_admin_list','account_user_list')"
							class="button"></td>
						<td>
						<center><strong>Admin Accounts</strong></center>
						<br>
						<select name="admin_list[]" id="account_admin_list" size="15"
							style="width: 15em" multiple="multiple">
							<?php
							foreach($user_accounts as $acct){
								if($acct['is_admin']) {
									?>
							<option value="<?php echo $acct['account_id'] ?>"><?php echo $acct['FRS']; ?>
							- <?php echo ($acct['pi']==NULL || $acct['pi']==0) ? $acct['pi_last_name'] : $acct['pi_ln']; ?></option>
							<?php
								}
							}
							?>
						</select></td>
					</tr>
					<tr>
						<td colspan="5"><input type="hidden" name="user_id"
							value="<?php echo $user->get_id(); ?>"> <input type="submit"
							name="submit" value="Submit" class="button"> <input type="button"
							name="cancel" value="Cancel" class="button"
							onclick="document.location='<?php echo $_SERVER['PHP_SELF']; ?>?user_id=<?php echo  $user->get_id() ?>';" />
							<?php if (strstr($_SERVER['PHP_SELF'], "admin.php")) { ?> <input
							type="hidden" name="fn" value="editUserAccounts"> <?php } ?></td>
					</tr>
				</table>
				</form>
				</td>
			</tr>
		</table>
		<?php
}

function print_manage_equipment_users($mach_data, $equipment_users, $allUsers) {
	?>
		<table width="100%">
			<tr>
				<td class="tableBorder">

				<table width="100%">
					<tr>
						<td class="tableTitle">&#8250; <?php echo $mach_data['name']; ?>
						Users</td>
					</tr>
					<?php
					$au_array = array();
					foreach ($equipment_users as $au) {
						array_push($au_array, $au['user_id']);
					}
					?>
					<tr class="cellColor">
						<td>
						<form name="manage_equipment_users"
							action="<?php echo  (strstr($_SERVER['PHP_SELF'], "admin.php")) ? "admin_update.php" : $_SERVER['PHP_SELF'] ?>"
							method="post"
							onsubmit="javascript: selectOptions('equipment_user_list');">
						<table>
							<tr>
								<td width="40%" align="center">
								<center><strong>Unauthorized Users</strong></center>
								<br>
								<select id="all_users_list" size="15" style="width: 15em"
									multiple="multiple">
									<?php foreach($allUsers as $user){ ?>
									<?php if (!in_array($user['user_id'], $au_array)) {?>
									<option value="<?php echo  $user['user_id'] ?>"><?php echo  $user['last_name']. ", ".$user['first_name'] ?></option>
									<?php } ?>
									<?php } ?>
								</select></td>
								<td align="center"><input type="button" name="add_user"
									value=" >> "
									onclick="moveOptions('all_users_list','equipment_user_list')"
									class="button"> <br>
								<br>
								<input type="button" name="remove_user" value=" << "
									onclick="moveOptions('equipment_user_list','all_users_list')"
									class="button"></td>
								<td width="40%" align="center">
								<center><strong>Authorized Users</strong></center>
								<br>
								<select name="equipment_user_list[]" id="equipment_user_list" size="15"
									style="width: 15em" multiple="multiple">
									<?php
									foreach($equipment_users as $equip_user){
										?>
									<option value="<?php echo  $equip_user['user_id'] ?>"><?php echo  $equip_user['last_name']. ", ".$equip_user['first_name'] ?></option>
									<?php
									}
									?>
								</select></td>
							</tr>
							<tr>
								<td colspan="3"><input type="hidden" name="machid"
									value="<?php echo $mach_data['machid']; ?>"> <input type="submit"
									name="submit" value="Submit" class="button"> <input
									type="button" name="cancel" value="Cancel" class="button"
									onclick="document.location='<?php echo $_SERVER['PHP_SELF']; ?>?machid=<?php echo $mach_data['machid']; ?>';" />
									<?php if ((strstr($_SERVER['PHP_SELF'], "admin.php")) || (strstr($_SERVER['PHP_SELF'], "equipment_users.php"))) { ?> <input
									type="hidden" name="fn" value="editEquipmentUsers"> <?php } ?>
								</td>
							</tr>
							<tr id="email_text">
								<td colspan=3><?php
								$user_count = 0;
								foreach ($equipment_users as $au) {
									if ($user_count > 0) echo ", ";
									$user_count++;
									echo $au['first_name']." ".$au['last_name']."&lt;".$au['email']."&gt;";
								}

								?></td>
							</tr>
						</table>
						</form>
						</td>
					</tr>
				</table>

				<?php
}

/**
 * Prints out the links to select last names
 * @param none
 */
function print_last_name_links() {
	global $letters;
	echo '<a href="javascript: search_user_last_name(\'\');">' . translate('All Users') . '</a>';
	foreach($letters as $letter) {
		echo '<a href="javascript: search_user_last_name(\''. $letter . '\');" style="padding-left: 10px; font-size: 12px;">' . $letter . '</a>';
	}
}

/**
 * Prints out list of current accounts
 * @param Object $pager pager object
 * @param mixed $accounts array of account data
 * @param string $err last database error
 */
function print_manage_accounts(&$pager, $accounts, $err) {
	global $link;

	?>

<form name="search_accounts_form" action="<?php echo $_SERVER['PHP_SELF']?>" method="get">
<p align="center">KFS#:
	<input type="text" name="frs" class="textbox" value="<?php echo filter_input(INPUT_GET, 'kfs'); ?>" />
	<input type="hidden" name="tool" value="<?php echo getTool()?>" />
	<input type="hidden" name="searchAccounts" value="true" />
	<input type="hidden" name="<?php echo $pager->getLimitVar()?>" value="<?php echo $pager->getLimit()?>" />
	<?php if (isset($_GET['order'])) { ?>
	<input type="hidden" name="order" value="<?php echo filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
	<?php } ?>
	<?php if (isset($_GET['vert'])) { ?>
	<input type="hidden" name="vert" value="<?php echo filter_input(INPUT_GET, 'vert', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
	<?php } ?>
	<input type="checkbox" name="getArchived" id="getArchived" value="1" <?php echo (isset($_GET['getArchived'])) ? 'checked="checked"' : '';?>>
	<label for="getArchived">Show Archived Data?</label>
	<input type="submit" name="searchAcctsBtn" value="Update" class="button" />
</p>
</form>
<?php
	$accounts_list_shown = '';
	foreach ($accounts as $a) {
		if (!empty($accounts_list_shown)) {
			$accounts_list_shown .= ',';
		}
		$accounts_list_shown .= $a['account_id'];
	}
 ?>
				<form name="manageAccount" method="post" action="admin_update.php">
				<input type="hidden" name="account_list_shown" value="<?php echo $accounts_list_shown ;?>">
				<table width="100%" border="0" cellspacing="0" cellpadding="1"
					align="center">
					<tr>
						<td class="tableBorder">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td colspan="8" class="tableTitle">&#8250; All Accounts</td>
							</tr>
							<tr class="rowHeaders">
								<td>Name</td>
								<td>KFS #</td>
								<td>PI</td>
								<td>Last Update</td>
								<td>Edit</td>
								<td>Users</td>
								<td>Billing Data</td>
								<td>Status</td>
								<td></td>
							</tr>
							<tr class="cellColor" style="text-align: center">
								<td><?php printDescLink($pager, 'name', 'account name'); ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'name', 'account name'); ?>
								</td>
								<td><?php printDescLink($pager, 'FRS', 'FRS') ?> &nbsp;&nbsp; <?php printAscLink($pager, 'FRS', 'FRS') ?>
								</td>
								<td><?php printDescLink($pager, 'pi_last_name', 'pi_last_name') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'pi_last_name', 'pi_last_name') ?>
								</td>
								<td><?php printDescLink($pager, 'last_update', 'last update') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'last_update', 'last update') ?>
								</td>
								<td></td>
								<td></td>
								<td></td>
								<td><?php printDescLink($pager, 'status', 'status') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'status', 'status') ?></td>
								<td></td>
							</tr>
							<?php
							if (!$accounts)
							echo '<tr class="cellColor0"><td colspan="8" style="text-align: center;">' . $err . '</td></tr>' . "\n";

							for ($i = 0; is_array($accounts) && $i < count($accounts); $i++) {
								$cur = $accounts[$i];
								//var_dump($accounts);

								echo "<tr class='cellColor" . ($i%2);
								if ($cur['deleted']) {
									echo ' deleted';
								}
								echo "' align='center' id='tr$i'>\r\n";

								echo "<td style='text-align:left'>" . $cur['name'] . "</td>\r\n";
								echo "<td style='text-align:left'>" . $cur['FRS'] . "</td>\r\n";
								echo "<td style='text-align:left'>";

								if ($cur['pi']==NULL || $cur['pi']==0) {
									echo $cur['pi_last_name'];
									if($cur['pi_first_name']!='')
									echo ", " . $cur['pi_first_name'];
									echo " <span style='font-size:.8em;color:red;'>Not Registered</span>";
								} else {
									$pi = new User($cur['pi']);
									echo $pi->get_name(true);
								}

								echo "</td>\n";
								echo '<td style="text-align:left">' . $cur['last_update'] . "</td>\n";

								echo '<td>' . $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&account_id=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;account_id=' . $cur['account_id'] . ((strpos($_SERVER['QUERY_STRING'], $pager->getLimitVar())===false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''), translate('Edit'), '', '', translate('Edit data for', array($cur['name']))) . "</td>\n";

								echo '<td>' . $link->getLink("admin.php?tool=account_users&amp;account_id=" . $cur['account_id'], 'Users', '', '', 'Edit this accounts users') . "</td>\n";

								echo '<td>' . $link->getLink("view_account_info.php?account_id=" . $cur['account_id'], 'View Billing', '', '', 'View Billing Data') . "</td>\n";

								echo '<td>' . $link->getLink("admin_update.php?fn=togAccount&amp;account_id=" . $cur['account_id'] . "&amp;status=" . $cur['status'], $cur['status'] == 1 ? '<font color="#009900">Active' : '<font color="#ff0000">Inactive', '', '', 'Toggle this account active/inactive') . "</font></td>\n";
								echo "<td><input type=\"checkbox\" name=\"account_id[]\" value=\"" . $cur['account_id'] . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"";
								if ($cur['deleted']) {
									echo ' checked="checked"';
								}
								echo "/></td>\n";

								echo "</tr>\n";
							}
							// Close table
							?>
						</table>
						</td>
					</tr>
				</table>
				<br />
				<?php
				echo "<div style='text-align: right;'>";
				echo submit_button(translate('Update'), 'account_id') . hidden_fn('delAccount');
				//echo submit_button('Toggle', 'account_id') . hidden_fn('togAccount');
				echo "</div>";
				echo '</form>';
}

function print_manage_account_users_old(&$pager, $account, $users, $err){

	global $link;

	/*
	 if (!$account->is_valid()) {
		CmnFns::do_error_box($users->get_error() . '<br /><a href="' . $_SERVER['PHP_SELF'] . '?tool=accounts">' . translate('Back') . '</a>', '', false);
		return;
		}
		*/

	echo '<h4>Account Name: ';
	if($account->get_name()!=""){
		echo $account->get_name();
	}else{
		echo "<i>Blank</i>";
	}
	echo '</h4>';

	echo '<h4>PI #: ' . $account->get_field('pi') . '</h4>';
	echo '<h4>FRS #: ' . $account->get_field('FRS') . '</h4>';
	echo '<h4>Sub-FRS #: ' . $account->get_field('sub_FRS') . '</h4>';
	echo '<h4>Current Status: ' . ($account->get_field('status') ? "Active" : "Inactive") . '</h4>';
	?>
				<table>
					<tr>
						<th valign="top">Current Users:</th>
						<td><?php
						$count = 0;
						for ($i = 0; is_array($users) && $i < count($users); $i++) {
							$br = 0;

							if ($account->is_permitted($users[$i]['user_id'])){
								echo $users[$i]['last_name'] . ', ' . $users[$i]['first_name'];
								$br = 1;
							}

							if ($account->is_admin($users[$i]['user_id'])){
								echo ' - Admin';
								$br = 1;
							}

							if($br==1) echo "<br>";
						}

						?></td>
					</tr>
				</table>
				<br />
				<form name="account_permissions" method="post"
					action="admin_update.php">
				<table border="0" cellspacing="0" cellpadding="1">
					<tr>
						<td class="tableBorder">
						<table cellspacing="1" cellpadding="2" border="0" width="100%">
							<tr class="rowHeaders">
								<td width="240">User</td>
								<td width="60"><?php echo translate('Allowed')?></td>
								<td width="90">Is Admin?</td>
							</tr>
							<?php
							if (!$users) echo '<tr class="cellColor0" style="text-align: center;"><td colspan="2">' . $err . '</td></tr>';

							$count = 0;
							for ($i = 0; is_array($users) && $i < count($users); $i++) {
								if($count%10==0 && $count>0){
									echo '<tr class="cellColor"><td colspan=3 align=right>' . submit_button(translate('Save')) . hidden_fn('editAccountUsers') . '</td>';
									?>
							<tr class="rowHeaders">
								<td width="240">User</td>
								<td width="60"><?php echo translate('Allowed')?></td>
								<td width="90">Is Admin?</td>
							</tr>
							<?php
								}
								$count++;
								echo '<tr class="cellColor"><td>' . $users[$i]['last_name'] . ', ' . $users[$i]['first_name'] . '</td>';
								echo '<td style="text-align: center;">'
								. '<input type="checkbox" name="user_id[]" value="' . $users[$i]['user_id'] . '"';
								if ($account->is_permitted($users[$i]['user_id']))
								echo ' checked="checked"';
								echo '/></td>';
								echo '<td style="text-align: center;"><input type="checkbox" name="is_admin[]" value="' . $users[$i]['user_id'] . '"';
								if ($account->is_admin($users[$i]['user_id']))
								echo ' checked="checked"';
								echo '/></td>';
								echo '</tr>';
							}

							// Close off tables/forms.  Print buttons and hidden field
							?>
							<tr class="cellColor1">
								<td>&nbsp;</td>
								<td style="text-align: center;"><input type="checkbox"
									name="checkAll" onclick="checkAllAccountUserBoxes(this);" /></td>
								<td style="text-align: center;"><input type="checkbox"
									name="checkAll" onclick="checkAllAccountUserAdminBoxes(this);" />
								</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<input type="hidden" name="account_id"
					value="<?php echo $account->get_account_id(); ?>" />
				<p style="padding-top: 5px; padding-bottom: 5px;"><input
					type="checkbox" name="notify_user" value="true" /><?php echo translate('Notify user')?></p>
					<?php echo  submit_button(translate('Save')) . hidden_fn('editAccountUsers')?>
				<input type="button" name="cancel" value="Back to Accounts"
					class="button"
					onclick="document.location='<?php echo $_SERVER['PHP_SELF']?>?tool=accounts';" />
				</form>
				<?php
}

/**
 * Interface to add or edit account information
 * @param mixed $rs array of account data
 * @param boolean $edit whether this is an edit or not
 * @param object $pager Pager object
 */
function print_account_admin_edit($rs, $users, $edit, &$pager, $account_types = array()) {
	$disabled = ($edit && array_key_exists('allow_multi', $rs) && $rs['allow_multi'] == 1) ? 'disabled="disabled"' : '';

	if ($edit) {
	    if (array_key_exists('minRes', $rs) && is_numeric($rs['minRes'])) {
	        $minH = intval($rs['minRes'] / 60);
		    $minM = intval($rs['minRes'] % 60);
	    } else {
	        $minH = null;
	        $minM = null;
	    }

	    if (array_key_exists('maxRes', $rs) && is_numeric($rs['maxRes'])) {
	        $maxH = intval($rs['maxRes'] / 60);
		    $maxM = intval($rs['maxRes'] % 60);
	    } else {
	        $maxH = null;
	        $maxM = null;
	    }
	}
	else {
		$maxH = 24;
	}

	?>
				<form name="addAccount" method="post" action="admin_update.php"
				<?php echo  $edit ? "" : "onsubmit=\"return checkAddAccount(this);\"" ?>>
				<table width="100%" border="0" cellspacing="0" cellpadding="1"
					align="center">
					<tr>
						<td class="tableBorder">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td width="200" class="formNames">Account Name</td>
								<td class="cellColor"><input type="text" name="name"
									class="textbox"
									value="<?php echo  isset($rs['name']) ? $rs['name'] : '' ?>" size="50" />
								</td>
							</tr>
							<tr>
								<td class="formNames">KFS #</td>
								<td class="cellColor"><input type="text" name="FRS"
									class="textbox" onkeyup="javascript: check_frs(this);"
									onchange="javascript: check_frs(this);"
									value="<?php echo  isset($rs['FRS']) ? $rs['FRS'] : '' ?>" size="18" />
								</td>
							</tr>
							<tr>
								<td class="formNames">Sub FRS #</td>
								<td class="cellColor"><input type="text" name="sub_FRS"
									class="textbox"
									value="<?php echo  isset($rs['sub_FRS']) ? $rs['sub_FRS'] : '' ?>"
									size="18" /></td>
							</tr>
							<tr>
								<td class="formNames">Account Type</td>
								<td class="cellColor">
									<?php
									if (array_key_exists('account_type', $rs)) {
									    print_account_type_select_box('account_type', $account_types, $rs['account_type']);
									}
									?>
								</td>
							</tr>
							<tr>
								<td class="formNames">Federal ID #<br /><span style="font-size:9px;">(for commercial accounts only)</span></td>
								<td class="cellColor"><input type="text" name="fed_id"
									class="textbox"
									value="<?php echo  isset($rs['fed_id']) ? $rs['fed_id'] : '' ?>" />
								</td>
							</tr>
							<tr>
								<td class="formNames">PI</td>
								<td class="cellColor"><select name="pi" class="textbox">
									<option value="">-- Select PI --</option>
									<?php
									if (empty($users))
									echo '<option value="">No PI\'s found</option>';
									else {
										for ($i = 0; $i < count($users); $i++)
										echo '<option value="' . $users[$i]['user_id'] . '"' . (isset($rs['pi']) && $users[$i]['user_id'] == $rs['pi'] ? ' selected="selected"' : '') . '>' . $users[$i]['last_name'] . ", " . $users[$i]['first_name'] . "</option>\n";
									}
									?>
								</select> <br />
								<br />

								If the PI is not in the above list, please fill the name in the
								boxes below.<br />
								<table>
									<tr>
										<td class="formNames">First Name</td>
										<td class="cellColor"><input type="text" name="pi_first_name"
											class="textbox"
											value="<?php echo  isset($rs['pi_first_name']) ? $rs['pi_first_name'] : '' ?>" /></td>
									</tr>
									<tr>
										<td class="formNames">Last Name</td>
										<td class="cellColor"><input type="text" name="pi_last_name"
											class="textbox"
											value="<?php echo  isset($rs['pi_last_name']) ? $rs['pi_last_name'] : '' ?>" />
										</td>
									</tr>
								</table>

								</td>
							</tr>
							<tr>
								<td class="formNames">Start Date</td>
								<td class="cellColor"><input type="text" name="start_date"
									class="textbox"
									value="<?php echo  isset($rs['start_date']) ? $rs['start_date'] : '' ?>" />
								</td>
							</tr>
							<tr>
								<td class="formNames">End Date</td>
								<td class="cellColor"><input type="text" name="end_date"
									class="textbox"
									value="<?php echo  isset($rs['end_date']) ? $rs['end_date'] : '' ?>" />
								</td>
							</tr>
							<tr>
								<td class="formNames">Admin Unit</td>
								<td class="cellColor"><input type="text" name="admin_unit"
									class="textbox"
									value="<?php echo  isset($rs['admin_unit']) ? $rs['admin_unit'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Admin Contact Name</td>
								<td class="cellColor"><input type="text"
									name="admin_contact_name" class="textbox"
									value="<?php echo  isset($rs['admin_contact_name']) ? $rs['admin_contact_name'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Admin Contact Email</td>
								<td class="cellColor"><input type="text"
									name="admin_contact_email" class="textbox"
									value="<?php echo  isset($rs['admin_contact_email']) ? $rs['admin_contact_email'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Admin Contact Phone</td>
								<td class="cellColor"><input type="text"
									name="admin_contact_phone" class="textbox"
									value="<?php echo  isset($rs['admin_contact_phone']) ? $rs['admin_contact_phone'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Organization</td>
								<td class="cellColor"><input type="text" name="organization"
									class="textbox"
									value="<?php echo  isset($rs['organization']) ? $rs['organization'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Billing Address 1</td>
								<td class="cellColor"><input type="text" name="billing_address1"
									class="textbox"
									value="<?php echo  isset($rs['billing_address1']) ? $rs['billing_address1'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Billing Address 2</td>
								<td class="cellColor"><input type="text" name="billing_address2"
									class="textbox"
									value="<?php echo  isset($rs['billing_address2']) ? $rs['billing_address2'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Billing City</td>
								<td class="cellColor"><input type="text" name="billing_city"
									class="textbox"
									value="<?php echo  isset($rs['billing_city']) ? $rs['billing_city'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Billing State</td>
								<td class="cellColor"><input type="text" name="billing_state"
									class="textbox"
									value="<?php echo  isset($rs['billing_state']) ? $rs['billing_state'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Billing Zip</td>
								<td class="cellColor"><input type="text" name="billing_zip"
									class="textbox"
									value="<?php echo  isset($rs['billing_zip']) ? $rs['billing_zip'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Business Contact Name</td>
								<td class="cellColor"><input type="text"
									name="business_contact_name" class="textbox"
									value="<?php echo  isset($rs['business_contact_name']) ? $rs['business_contact_name'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Business Contact Email</td>
								<td class="cellColor"><input type="text"
									name="business_contact_email" class="textbox"
									value="<?php echo  isset($rs['business_contact_email']) ? $rs['business_contact_email'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Business Contact Phone</td>
								<td class="cellColor"><input type="text"
									name="business_contact_phone" class="textbox"
									value="<?php echo  isset($rs['business_contact_phone']) ? $rs['business_contact_phone'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Technical Contact Name</td>
								<td class="cellColor"><input type="text"
									name="technical_contact_name" class="textbox"
									value="<?php echo  isset($rs['technical_contact_name']) ? $rs['technical_contact_name'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Technical Contact Email</td>
								<td class="cellColor"><input type="text"
									name="technical_contact_email" class="textbox"
									value="<?php echo  isset($rs['technical_contact_email']) ? $rs['technical_contact_email'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Technical Contact Phone</td>
								<td class="cellColor"><input type="text"
									name="technical_contact_phone" class="textbox"
									value="<?php echo  isset($rs['technical_contact_phone']) ? $rs['technical_contact_phone'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Comments</td>
								<td class="cellColor"><textarea name="comments" class="textbox"
									rows="8" cols="70"><?php echo  isset($rs['comments']) ? $rs['comments'] : '' ?></textarea>
								</td>
							</tr>
							<tr>
								<td class="formNames">Source</td>
								<td class="cellColor"><input type="text" name="source"
									class="textbox"
									value="<?php echo  isset($rs['source']) ? $rs['source'] : '' ?>"
									size="50" /></td>
							</tr>
							<tr>
								<td class="formNames">Agency</td>
								<td class="cellColor"><input type="text" name="agency"
									class="textbox"
									value="<?php echo  isset($rs['agency']) ? $rs['agency'] : '' ?>"
									size="50" /></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<br />
				<?php
				// Print out correct buttons
				if (!$edit) {
					echo submit_button('Add Account', 'account_id') . hidden_fn('addAccount')
					. ' <input type="reset" name="reset" value="' . translate('Clear') . '" class="button" />' . "\n";
				}
				else {
					echo submit_button('Edit Account', 'account_id') . cancel_button($pager) . hidden_fn('editAccount')
					. '<input type="hidden" name="account_id" value="' . $rs['account_id'] . '" />' . "\n";
					// Unset variables
					unset($rs);
				}
				echo "</form>\n";
}

/**
 * Prints out list of current resources
 * @param Pager $pager pager object
 * @param mixed $resources array of resource data
 * @param string $err last database error
 */
function print_manage_resources(&$pager, $resources, $err) {
	global $link;
	$getDeleted = filter_input(INPUT_GET, 'getDeleted', FILTER_VALIDATE_INT, array('options'=>array('default'=>0, 'min_range'=>0, 'max_range'=>1)));
	
	$resources_shown_list = '';
	foreach ($resources as $a) {
		if (!empty($resources_shown_list)) {
			$resources_shown_list .= ',';
		}
		$resources_shown_list .= $a['machid'];
	}
	?>
				<div>
				    <div style="width:250px;margin:5px auto;">
				        <form name="show_deleted_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
				        	<input type="hidden" name="tool" value="<?php echo getTool()?>" />
                            <input type="hidden" name="<?php echo $pager->getLimitVar()?>" value="<?php echo $pager->getLimit()?>" />
                            <?php if (isset($_GET['order'])) { ?>
                            <input type="hidden" name="order" value="<?php echo filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
                            <?php } ?>
                            <?php if (isset($_GET['vert'])) { ?>
                            <input type="hidden" name="vert" value="<?php echo filter_input(INPUT_GET, 'vert', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
                            <?php } ?>
                            <input type="checkbox" name="getDeleted" id="getDeleted" value="1" <?php echo ($getDeleted === 1) ? 'checked="checked"' : '';?>>
                            <label for="getDeleted"><?php echo translate('Show Deleted Data');?></label>
                            <input type="submit" name="searchAcctsBtn" value="Update" class="button">
				        </form>
				    </div>
				</div>
				<form name="manageResource" method="post" action="admin_update.php">
                    <input type="hidden" name="tool" value="<?php echo getTool()?>" />
       				<input type="hidden" name="resource_list_shown" value="<?php echo $resources_shown_list ;?>">

                    <input type="hidden" name="<?php echo $pager->getLimitVar()?>" value="<?php echo $pager->getLimit()?>" />
                    <?php if (isset($_GET['order'])) { ?>
                    <input type="hidden" name="order" value="<?php echo filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
                    <?php } ?>
                    <?php if (isset($_GET['vert'])) { ?>
                    <input type="hidden" name="vert" value="<?php echo filter_input(INPUT_GET, 'vert', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
                    <?php } ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1"
                        align="center">
                        <tr>
                            <td class="tableBorder">
                            <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                <tr>
                                    <td colspan="7" class="tableTitle">&#8250; <?php echo translate('All Resources')?></td>
                                </tr>
                                <tr class="rowHeaders">
                                    <td><?php echo translate('Resource Name')?></td>
                                    <!-- <td width="18%"><?php echo translate('Location')?></td> -->
                                    <td width="12%"><?php echo translate('Lab')?></td>
                                    <td width="10%"><?php echo translate('Phone')?></td>
                                    <!-- <td width="25%"><?php echo translate('Notes')?></td> -->
                                    <td width="5%"><?php echo translate('Edit')?></td>
                                    <td width="5%">View Users</td>
                                    <td width="9%"><?php echo translate('Reservation Status')?></td>
                                    <td width="9%"><?php echo translate('Operational Status')?></td>
                                    <td width="7%"><?php echo translate('Delete')?></td>
                                </tr>
                                <tr class="cellColor" style="text-align: center">
                                    <td><?php printDescLink($pager, 'name', 'resource name') ?>
                                    &nbsp;&nbsp; <?php printAscLink($pager, 'name', 'resource name') ?>
                                    </td>
                                    <!-- <td> <?php printDescLink($pager, 'location', 'location') ?> &nbsp;&nbsp; <?php printAscLink($pager, 'location', 'location') ?> </td> -->
                                    <td><?php printDescLink($pager, 'labTitle', 'lab title') ?>
                                    &nbsp;&nbsp; <?php printAscLink($pager, 'labTitle', 'lab title') ?>
                                    </td>
                                    <td>&nbsp;</td>
                                    <!-- <td>&nbsp;</td> -->
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php
    
                                if (!$resources)
                                echo '<tr class="cellColor0"><td colspan="8" style="text-align: center;">' . $err . '</td></tr>' . "\n";
    
                                for ($i = 0; is_array($resources) && $i < count($resources); $i++) {
                                    $cur = $resources[$i];
                                    echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\" id=\"tr$i\">\n"
                                    . '<td style="text-align:left">' . $cur['name'] . "</td>\n";
                                    //    . '<td style="text-align:left">';
                                    //echo isset($cur['location']) ?  $cur['location'] : '&nbsp;';
                                    //echo "</td>\n"
                                    echo '<td style="text-align:left">' . $cur['nickname'] . "</td>\n";
                                    echo '<td style="text-align:left">';
                                    echo isset($cur['rphone']) ?  $cur['rphone'] : '&nbsp;';
                                    echo "</td>\n";
                                    //    . '<td style="text-align:left">';
                                    //echo isset($cur['notes']) ?  $cur['notes'] : '&nbsp;';
                                    //echo "</td>\n"
                                    echo '<td>' . $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&machid=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;machid=' . $cur['machid'] . ((strpos($_SERVER['QUERY_STRING'], $pager->getLimitVar())===false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : '')."#edit", translate('Edit'), '', '', translate('Edit data for', array($cur['name']))) . "</td>\n";
    
                                    echo '<td>' . $link->getLink("javascript: equip_users('" . $cur['machid'] . "');", 'Users', '', '', 'Edit Users') . '</td>';
    
                                    echo '<td>' . $link->getLink("admin_update.php?fn=togResource&amp;machid=" . $cur['machid'] . "&amp;status=" . $cur['status'], $cur['status'] == 'a' ? '<font color="#009900">Active' : '<font color="#ff0000">Inactive', '', '', translate('Toggle this resource active/inactive')) . "</font></td>\n";
    
                                    echo "<td>";
                                    $operational_statuses = ['Online', 'Maintenance', 'Waiting', 'Offline']; // @todo: create dynamic table for op statuses.
                                    echo "<select name='operational_status-" . $cur['machid'] . "' class='operational-status-" . strtolower($cur['operational_status']) ."'>";
                                    foreach ($operational_statuses as $op_status) {
                                        echo "<option value='$op_status' class='operational-status-" . strtolower($op_status) ."'";
                                        if ($cur['operational_status'] === $op_status) {
                                            echo " selected";
                                        }
                                        echo ">".translate($op_status)."</option>";
                                    }
                                    echo "</select>";
                                    echo "</td>\n";
    
                                    echo "<td><input type=\"checkbox\" name=\"machid[]\" value=\"" . $cur['machid'] . "\" onclick=\"adminRowClick(this,'tr$i',$i);\"";
                                    if ($cur['deleted'] == 1) {
                                        echo "checked";
                                    }
                                    echo "></td>\n";
    
                                    echo "</tr>\n";
                                }
    
                                // Close table
                                ?>
                            </table>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <div style="margin: 5px 0 5px 0;text-align: right;">
                    <?php
                    echo submit_button(translate('Update')) . hidden_fn('updateResources');
                    ?>
                    </div>
				</form>
    <?php
}

/**
 * Interface to add or edit resource information
 * @param mixed $rs array of resource data
 * @param boolean $edit whether this is an edit or not
 * @param Pager $pager Pager object
 */
function print_equipment_edit($rs, $labs, $usersList, $rateCategories, $resourceRates, $equipment_list, $edit, &$pager) {
	global $conf;
	$start = 0;
	$end   = 1440;
	$mins = array(0, 10, 15, 30);
	$disabled = ($edit && $rs['allow_multi'] == 1) ? 'disabled="disabled"' : '';

	if ($edit) {
		$minH = intval($rs['minRes'] / 60);
		$minM = intval($rs['minRes'] % 60);
		$maxH = intval($rs['maxRes'] / 60);
		$maxM = intval($rs['maxRes'] % 60);
	}
	else {
		$maxH = 24;
	}

	?> <a href="" id="edit"></a>
				<form name="addResource" method="post" action="admin_update.php"
				<?php echo  $edit ? "" : "onsubmit=\"return checkAddResource(this);\"" ?>>
				<table width="100%" border="0" cellspacing="0" cellpadding="1"
					align="center">
					<tr>
						<td class="tableBorder">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td width="200" class="formNames"><?php echo translate('Resource Name')?></td>
								<td class="cellColor"><input type="text" name="name"
									class="textbox"
									value="<?php echo  isset($rs['name']) ? $rs['name'] : '' ?>" size="50" />
								</td>
							</tr>
							<tr>
								<td class="formNames">Owner</td>
								<td class="cellColor"><select name="owner" class="textbox">
									<option value="">Select Owner</option>
									<?php
									for ($i = 0; $i < count($usersList); $i++){
										echo '<option value="' . $usersList[$i]['user_id'] . '"' . (isset($rs['owner']) && $usersList[$i]['user_id'] == $rs['owner'] ? ' selected="selected"' : '') . '>' . $usersList[$i]['last_name'] . ", " . $usersList[$i]['first_name'] . "</option>\n";
									}
									?>
								</select></td>
							</tr>
							<tr>
								<td class="formNames">Staff Contact</td>
								<td class="cellColor"><select name="staff_contact" class="textbox">
									<option value="">Select Contact</option>
									<?php
									for ($i = 0; $i < count($usersList); $i++){
										echo '<option value="' . $usersList[$i]['user_id'] . '"' . (isset($rs['owner']) && $usersList[$i]['user_id'] == $rs['staff_contact'] ? ' selected="selected"' : '') . '>' . $usersList[$i]['last_name'] . ", " . $usersList[$i]['first_name'] . "</option>\n";
									}
									?>
								</select></td>
							</tr>
							<tr>
								<td class="formNames">Lab</td>
								<td class="cellColor"><select name="lab_id" class="textbox">
								<?php
								if (empty($labs))
								echo '<option value="">Please add labs</option>';
								else {
									for ($i = 0; $i < count($labs); $i++)
									echo '<option value="' . $labs[$i]['lab_id'] . '"' . (isset($rs['lab_id']) && $labs[$i]['lab_id'] == $rs['lab_id'] ? ' selected="selected"' : '') . '>' . $labs[$i]['labTitle'] . "</option>\n";
								}
								?>
								</select></td>
							</tr>
							<tr>
								<td class="formNames"><?php echo translate('Location')?></td>
								<td class="cellColor"><input type="text" name="location"
									class="textbox"
									value="<?php echo  isset($rs['location']) ? $rs['location'] : '' ?>" />
								</td>
							</tr>
							<tr>
								<td class="formNames"><?php echo translate('Phone')?></td>
								<td class="cellColor"><input type="text" name="rphone"
									class="textbox"
									value="<?php echo  isset($rs['rphone']) ? $rs['rphone'] : '' ?>" /></td>
							</tr>
							<tr>
								<td class="formNames"><?php echo translate('Notes')?></td>
								<td class="cellColor"><textarea name="notes" class="textbox"
									rows="8" cols="70"><?php echo  isset($rs['notes']) ? $rs['notes'] : '' ?></textarea>
								</td>
							</tr>
							<tr>
								<td class="formNames"><?php echo translate('Minimum Reservation Time')?></td>
								<td class="cellColor"><select name="minH" class="textbox"
									id="minH" <?php echo $disabled?>>
									<?php
									for ($h = 0; $h < 25; $h++)
									echo '<option value="' . $h . '"' . ((isset($minH) && $minH == $h) ? ' selected="selected"' : '') . '>' . $h . ' ' . translate('hours') . '</option>' . "\n";
									?>
								</select> <select name="minM" class="textbox" id="minM"
								<?php echo $disabled?>>
									<?php
									foreach ($mins as $m)
									echo '<option value="' . $m . '"' . ((isset($minM) && $minM == $m) ? ' selected="selected"' : '') . '>' . $m . ' ' . translate('minutes') . '</option>' . "\n";
									?>
								</select></td>
							</tr>
							<tr>
								<td class="formNames"><?php echo translate('Maximum Reservation Time')?></td>
								<td class="cellColor"><select name="maxH" class="textbox"
									id="maxH" <?php echo $disabled?>>
									<?php
									for ($h = 0; $h < 25; $h++)
									echo '<option value="' . $h . '"' . ((isset($maxH) && $maxH == $h) ? ' selected="selected"' : '') . '>' . $h . ' ' . translate('hours') . '</option>' . "\n";
									?>
								</select> <select name="maxM" class="textbox" id="maxM"
								<?php echo $disabled?>>
									<?php
									foreach ($mins as $m)
									echo '<option value="' . $m . '"' . ((isset($maxM) && $maxM == $m) ? ' selected="selected"' : '') . '>' . $m . ' ' . translate('minutes') . '</option>' . "\n";
									?>
								</select></td>
							</tr>
							<?php
							foreach ($rateCategories as $rcat) {
								echo '<tr>
										<td class="formNames">'.$rcat['label'].' Rate</td>
										<td class="cellColor">
											$<input type="text" class="textbox"
												name="resource_rate:'.$rcat['id'].'"';
								if (array_key_exists($rcat['id'], $resourceRates)) {
								    echo 'value="'.$resourceRates[$rcat['id']]['rate'].'"';
								}
								echo ' size="4">
										</td>
									</tr>
									';
							}
							?>
							<tr>
								<td class="formNames"><?php echo translate('Auto-assign permission')?></td>
								<td class="cellColor"><input type="checkbox" class="textbox"
									name="autoAssign"
									<?php echo (isset($rs['autoAssign']) && ($rs['autoAssign'] == 1)) ? 'checked="checked"' : ''?> />
								</td>
							</tr>
							<tr>
								<td class="formNames"><?php echo translate('Approval Required')?></td>
								<td class="cellColor"><input type="checkbox" class="textbox"
									name="approval"
									<?php echo (isset($rs['approval']) && ($rs['approval'] == 1)) ? 'checked="checked"' : ''?> />
								</td>
							</tr>
							<tr>
								<td class="formNames"><?php echo translate('Allow Multiple Day Reservations')?></td>
								<td class="cellColor"><input type="checkbox" name="allow_multi"
								<?php echo (isset($rs['allow_multi']) && ($rs['allow_multi'] == 1)) ? 'checked="checked"' : ''?>
									onclick="showHideMinMax(this);" /></td>
							</tr>
							<tr>
								<td class="formNames"><?php echo "Cancellation/Modify Horizon";?></td>
								<td class="cellColor"><input type="text" class="textbox"
									name="edit_horizon"
									value="<?php if(isset($rs['edit_horizon']) && $rs['edit_horizon']!=''){ echo $rs['edit_horizon'];}else{ echo "0"; } ?>"
									size="4" />hrs</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<br />
				<?php
				// Print out correct buttons
				if (!$edit) {
					echo submit_button(translate('Add Resource'), 'machid') . hidden_fn('addResource')
					. ' <input type="reset" name="reset" value="' . translate('Clear') . '" class="button">' . "\n";
				}
				else {
					echo submit_button(translate('Edit Resource'), 'machid') . cancel_button($pager) . hidden_fn('editResource')
					. '<input type="hidden" name="machid" value="' . $rs['machid'] . '">' . "\n";
					// Unset variables
					unset($rs);
				}
				echo "</form>\n";
}

/**
 * Interface for managing user training
 * Provide interface for viewing and managing
 *  user training information
 * @param object $user User object of user to manage
 * @param array $rs list of resources
 */
function print_manage_perms(&$user, $rs, $err) {
	global $link;

	if (!$user->is_valid()) {
		CmnFns::do_error_box($user->get_error() . '<br /><a href="' . $_SERVER['PHP_SELF'] . '?tool=users">' . translate('Back') . '</a>', '', false);
		return;
	}

	echo '<h3>' . $user->get_name() . '</h3>';
	?>
				<form name="train" method="post" action="admin_update.php">
				<table border="0" cellspacing="0" cellpadding="1">
					<tr>
						<td class="tableBorder">
						<table cellspacing="1" cellpadding="2" border="0" width="100%">
							<tr class="rowHeaders">
								<td width="240"><?php echo translate('Resource Name')?></td>
								<td width="60"><?php echo translate('Allowed')?></td>
							</tr>
							<?php
							if (!$rs) echo '<tr class="cellColor0" style="text-align: center;"><td colspan="2">' . $err . '</td></tr>';

							for ($i = 0; is_array($rs) && $i < count($rs); $i++) {
								echo '<tr class="cellColor"><td>' . $rs[$i]['name'] . '</td><td style="text-align: center;">'
								. '<input type="checkbox" name="machid[]" value="' . $rs[$i]['machid'] . '"';
								if ($user->has_perm($rs[$i]['machid']))
								echo ' checked="checked"';
								echo '/></td></tr>';
							}

							// Close off tables/forms.  Print buttons and hidden field
							?>
							<tr class="cellColor1">
								<td>&nbsp;</td>
								<td style="text-align: center;"><input type="checkbox"
									name="checkAll" onclick="checkAllBoxes(this);" /></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<input type="hidden" name="user_id" value="<?php echo $user->get_id()?>" />
				<p style="padding-top: 5px; padding-bottom: 5px;"><input
					type="checkbox" name="notify_user" value="true" /><?php echo translate('Notify user')?></p>
					<?php echo  submit_button(translate('Save')) . hidden_fn('editPerms')?> <input
					type="button" name="cancel" value="<?php echo translate('Manage Users')?>"
					class="button"
					onclick="document.location='<?php echo $_SERVER['PHP_SELF']; ?>?tool=users';" />
				</form>
				<?php
}

/**
 * Interface for displaying today's reservations
 * Provide a table to current day's reservations
 * @param Object $pager pager object
 * @param mixed $res reservation data
 * @param string $err last database error
 */
function print_todays_reservations(&$pager, $res, $err) {
	global $link;

	?>
				<form name="approve" id="approve" method="post" action="reserve.php"
					style="margin: 0px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="1"
					align="center">
					<tr>
						<td class="tableBorder">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td colspan="5" class="tableTitle">&#8250; Today's Reservations</td>
							</tr>
							<tr class="rowHeaders">
								<td width="10%"><?php echo translate('Start Time')?></td>
								<td width="10%"><?php echo translate('End Time')?></td>
								<td width="20%"><?php echo translate('User')?></td>
								<td>Technical Notes</td>
								<td>Billing Notes</td>
								<td width="19%"><?php echo translate('Resource')?></td>
								<td width="7%"><?php echo translate('View')?></td>
							</tr>
							<tr class="cellColor" style="text-align: center">
								<td><?php printDescLink($pager, 'startTime', 'Sort by descending start time') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'startTime', 'Sort by ascending start time') ?>
								</td>
								<td><?php printDescLink($pager, 'endTime', 'Sort by descending end time') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'endTime', 'Sort by ascending end time') ?>
								</td>
								<td><?php printDescLink($pager, 'last_name', 'Sort by descending user name') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'last_name', 'Sort by ascending user name') ?>
								</td>
								<td></td>
								<td></td>
								<td><?php printDescLink($pager, 'name', 'Sort by descending resource name') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'name', 'Sort by ascending resource name') ?>
								</td>
								<td>&nbsp;</td>
							</tr>
							<?php
							// Write message if they have no reservations
							if (!$res)
							echo '<tr class="cellColor"><td colspan="7" align="center">' . $err . '</td></tr>';

							// For each reservation, clean up the date/time and print it
							for ($i = 0; is_array($res) && $i < count($res); $i++) {
								$cur = $res[$i];
								$first_name = $cur['first_name'];
								$last_name = $cur['last_name'];
								echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\">\n"
								. '<td>' . CmnFns::formatTime($cur['startTime']) . '</td>'
								. '<td>' . CmnFns::formatTime($cur['endTime']) . '</td>'
								. '<td style="text-align:left">' . $link->getLink("javascript: viewUser('" . $cur['user_id'] . "');", $first_name . ' ' . $last_name, '', '', translate('View information about', array($first_name,$last_name))) . '</td>';
								?>
							<td><?php
							if(empty($cur['technical_note'])){
								echo $link->getLink("javascript: res_note('technical','add','" . $cur['resid']. "');", 'Add'                         , '', '', 'Add a technical note');
							}else{
								echo $link->getLink("javascript: res_note('technical','edit','" . $cur['resid']. "');", '<font color=#aa0000>Edit/View</font>', '', '', 'Edit a technical note');
							}
							?></td>
							<?php


							?>
							<td><?php
							if(empty($cur['billing_note'])){
								echo $link->getLink("javascript: res_note('billing','add','" . $cur['resid']. "');", 'Add', '', '', 'Add a billing note');
							}else{
								echo $link->getLink("javascript: res_note('billing','edit','" . $cur['resid']. "');", '<font color=#aa0000>Edit/View</font>', '', '', 'Edit a billing note');
							}

							?></td>
							<?php
							echo '<td style="text-align:left">' . $cur['name'] . ' - ' . ($cur['status'] == 'a' ? translate('Active') : translate('Inactive')) . '</td>'
							. '<td>' . $link->getLink("javascript: reserve('".RES_TYPE_VIEW."','','','" . $cur['resid']. "');", translate('View'), '', '', translate('View this reservation information')) . '</td>'
							. "</tr>\n";
							}
							?>
						</table>
						</td>
					</tr>
				</table>
				</form>
				<br />
				<?php
}

/**
 * Interface for approving reservations
 * Provide a table to allow admin to approve or delete reservations
 * @param Object $pager pager object
 * @param mixed $res reservation data
 * @param string $err last database error
 */
function print_approve_reservations(&$pager, $res, $err) {
	global $link;

	?>
				<form name="approve" id="approve" method="post" action="reserve.php"
					style="margin: 0px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="1"
					align="center">
					<tr>
						<td class="tableBorder">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td colspan="9" class="tableTitle">&#8250; <?php echo translate('Pending User Reservations')?></td>
							</tr>
							<tr class="rowHeaders">
								<td width="10%"><?php echo translate('Start Date')?></td>
								<td width="10%"><?php echo translate('End Date')?></td>
								<td width="20%"><?php echo translate('User')?></td>
								<td width="19%"><?php echo translate('Resource')?></td>
								<td width="10%"><?php echo translate('Start Time')?></td>
								<td width="10%"><?php echo translate('End Time')?></td>
								<td width="7%"><?php echo translate('View')?></td>
								<td width="7%"><?php echo translate('Approve')?></td>
								<td width="7%"><?php echo translate('Delete')?></td>
							</tr>
							<tr class="cellColor" style="text-align: center">
								<td><?php printDescLink($pager, 'start_date', 'Sort by descending date') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'start_date', 'Sort by ascending date') ?>
								</td>
								<td><?php printDescLink($pager, 'end_date', 'Sort by descending date') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'end_date', 'Sort by ascending date') ?>
								</td>
								<td><?php printDescLink($pager, 'last_name', 'Sort by descending user name') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'last_name', 'Sort by ascending user name') ?>
								</td>
								<td><?php printDescLink($pager, 'name', 'Sort by descending resource name') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'name', 'Sort by ascending resource name') ?>
								</td>
								<td><?php printDescLink($pager, 'startTime', 'Sort by descending start time') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'startTime', 'Sort by ascending start time') ?>
								</td>
								<td><?php printDescLink($pager, 'endTime', 'Sort by descending end time') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'endTime', 'Sort by ascending end time') ?>
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?php
							// Write message if they have no reservations
							if (!$res)
							echo '<tr class="cellColor"><td colspan="9" align="center">' . $err . '</td></tr>';

							// For each reservation, clean up the date/time and print it
							for ($i = 0; is_array($res) && $i < count($res); $i++) {
								$cur = $res[$i];
								$first_name = $cur['first_name'];
								$last_name = $cur['last_name'];
								echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\">\n"
								. '<td>' . CmnFns::formatDate($cur['start_date']) . '</td>'
								. '<td>' . CmnFns::formatDate($cur['end_date']) . '</td>'
								. '<td style="text-align:left">' . $link->getLink("javascript: viewUser('" . $cur['user_id'] . "');", $first_name . ' ' . $last_name, '', '', translate('View information about', array($first_name,$last_name))) . '</td>'
								. '<td style="text-align:left">' . $cur['name'] . "</td>"
								. '<td>' . CmnFns::formatTime($cur['startTime']) . '</td>'
								. '<td>' . CmnFns::formatTime($cur['endTime']) . '</td>'
								. '<td>' . $link->getLink("javascript: reserve('".RES_TYPE_VIEW."','','','" . $cur['resid']. "');", translate('View'), '', '', translate('View this reservation information')) . '</td>'
								. '<td>' . $link->getlink("javascript: reserve('".RES_TYPE_APPROVE."','','','" . $cur['resid'] ."');", translate('Approve'), '', '', translate('Approve this reservation')) . '</td>'
								. '<td>' . $link->getLink("javascript: reserve('".RES_TYPE_DELETE."','','','" . $cur['resid']. "');", translate('Delete'), '', '', translate('Delete this reservation')) . '</td>'
								. "</tr>\n";
							}
							?>
						</table>
						</td>
					</tr>
				</table>
				</form>
				<br />
				<?php
}

/**
 * Interface for managing reservations
 * Provide a table to allow admin to modify or delete reservations
 * @param Object $pager pager object
 * @param mixed $res reservation data
 * @param string $err last database error
 */
function print_manage_reservations(&$pager, $res, $err) {
	global $link;

	?>
				<table width="100%" border="0" cellspacing="0" cellpadding="1"
					align="center">
					<tr>
						<td class="tableBorder">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td colspan="9" class="tableTitle">&#8250; <?php echo translate('User Reservations')?></td>
							</tr>
							<tr class="rowHeaders">
								<td width="10%"><?php echo translate('Start Date')?></td>
								<td width="20%"><?php echo translate('User')?></td>
								<td width="15%"><?php echo translate('Resource Name')?></td>
								<td width="10%"><?php echo translate('Start Time')?></td>
								<td width="10%"><?php echo translate('End Time')?></td>
								<td width="7%">Technical Note</td>
								<td width="7%">Billing Note</td>
								<td width="7%"><?php echo translate('View')?></td>
								<td width="7%"><?php echo translate('Modify')?></td>
								<td width="7%"><?php echo translate('Delete')?></td>
							</tr>
							<tr class="cellColor" style="text-align: center">
								<td><?php printDescLink($pager, 'start_date', 'start date') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'start_date', 'start date') ?>
								</td>
								<td><?php printDescLink($pager, 'last_name', 'user name') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'last_name', 'user name') ?>
								</td>
								<td><?php printDescLink($pager, 'name', 'resource name') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'name', 'resource name') ?>
								</td>
								<td><?php printDescLink($pager, 'startTime', 'start time') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'startTime', 'start time') ?>
								</td>
								<td><?php printDescLink($pager, 'endTime', 'end time') ?>
								&nbsp;&nbsp; <?php printAscLink($pager, 'endTime', 'end time') ?>
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?php
							// Write message if they have no reservations
							if (!$res)
							echo '<tr class="cellColor"><td colspan="9" align="center">' . $err . '</td></tr>';

							// For each reservation, clean up the date/time and print it
							for ($i = 0; is_array($res) && $i < count($res); $i++) {
								$cur = $res[$i];
								$first_name = $cur['first_name'];
								$last_name = $cur['last_name'];
								echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\">\n"
								. '<td>' . CmnFns::formatDate($cur['start_date']) . '</td>'
								. '<td style="text-align:left">' . $link->getLink("javascript: viewUser('" . $cur['user_id'] . "');", $first_name . ' ' . $last_name, '', '', translate('View information about', array($first_name,$last_name))) . "</td>"
								. '<td style="text-align:left">' . $cur['name'] . "</td>"
								. '<td>' . CmnFns::formatTime($cur['startTime']) . '</td>'
								. '<td>' . CmnFns::formatTime($cur['endTime']) . '</td>';

								?>
							<td><?php
							if(empty($cur['technical_note'])){
								echo $link->getLink("javascript: res_note('technical','add','" . $cur['resid']. "');", 'Add', '', '', 'Add a technical note');
							}else{
								echo $link->getLink("javascript: res_note('technical','edit','" . $cur['resid']. "');", '<font color=#aa0000>Edit/View</font>', '', '', 'Edit a technical note');
							}
							?></td>
							<?php


							?>
							<td><?php
							if(empty($cur['billing_note'])){
								echo $link->getLink("javascript: res_note('billing','add','" . $cur['resid']. "');", 'Add', '', '', 'Add a billing note');
							}else{
								echo $link->getLink("javascript: res_note('billing','edit','" . $cur['resid']. "');", '<font color=#aa0000>Edit/View</font>', '', '', 'Edit a billing note');
							}

							?></td>
							<?php
							echo '<td>' . $link->getLink("javascript: reserve('".RES_TYPE_VIEW."','','','" . $cur['resid']. "');", translate('View')) . '</td>'
							. '<td>' . $link->getlink("javascript: reserve('".RES_TYPE_MODIFY."','','','" . $cur['resid']. "');", translate('Modify')) . '</td>'
							. '<td>' . $link->getLink("javascript: reserve('".RES_TYPE_DELETE."','','','" . $cur['resid']. "');", translate('Delete')) . '</td>'
							. "</tr>\n";
							}
							?>
						</table>
						</td>
					</tr>
				</table>
				<br />
				<?php
}

/**
 * Prints out list of current announcements
 * @param Object $pager pager object
 * @param mixed $announcements array of announcement data
 * @param string $err last database error
 */
function print_manage_announcements(&$pager, $announcements, $err, $labs=NULL) {
	global $link;
	$lab_list = array();
	if (!is_null($labs) && is_array($labs)) {
		foreach ($labs as $lab) {
			$lab_list[$lab['lab_id']] = $lab['nickname'];
		}
	}
	?>
				<form name="manageAnnouncement" method="post"
					action="admin_update.php"
					onsubmit="return checkAnnouncementForm();">
				<table width="100%" border="0" cellspacing="0" cellpadding="1"
					align="center">
					<tr>
						<td class="tableBorder">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td colspan="7" class="tableTitle">&#8250; <?php echo translate('All Announcements')?></td>
							</tr>
							<tr class="rowHeaders">
								<td><?php echo translate('Announcement')?></td>
								<td width="7%"><?php echo translate('Number')?></td>
								<td width="5%"><?php echo translate('Edit')?></td>
								<td width="5%">Lab</td>
								<td width="7%"><?php echo translate('Delete')?></td>
								<!--<td>Lab</td>-->
							</tr>
							<?php

							if (!$announcements)
							echo '<tr class="cellColor0"><td colspan="8" style="text-align: center;">' . $err . '</td></tr>' . "\n";

							for ($i = 0; is_array($announcements) && $i < count($announcements); $i++) {
								$cur = $announcements[$i];
								echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\" id=\"tr$i\">\n"
								. '<td style="text-align:left">' . htmlspecialchars($cur['announcement']) . "</td>\n"
								. '<td style="text-align:left">'.$cur['number']."</td>\n"
								. '<td>' . $link->getLink($_SERVER['PHP_SELF'] . '?' . preg_replace("/&announcmentid=[\d\w]*/", "", $_SERVER['QUERY_STRING']) . '&amp;announcementid=' . $cur['announcementid'] . ((strpos($_SERVER['QUERY_STRING'], $pager->getLimitVar())===false) ? '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() : ''), translate('Edit'), '', '', translate('Edit data for', array($cur['announcementid']))) . "</td>\n"
								. '<td>';
								if (array_key_exists($cur['lab_id'], $lab_list)) {
									echo $lab_list[$cur['lab_id']];
								} else if ($cur['lab_id'] == 0) {
									echo 'Scheduler';
								}
								echo '</td>'
								. "<td><input type=\"checkbox\" name=\"announcementid[]\" value=\"" . $cur['announcementid'] . "\" onclick=\"adminRowClick(this,'tr$i',$i);\" /></td>\n";

								echo "</tr>\n";
							}

							// Close table
							?>
						</table>
						</td>
					</tr>
				</table>
				<br />
				<?php
				echo submit_button(translate('Delete Announcements'), 'announcementid') . hidden_fn('delAnnouncement');
				?></form>
				<?php
}

/**
 * Interface to add or edit announcement information
 * @param mixed $rs array of lab data
 * @param boolean $edit whether this is an edit or not
 * @param object $pager Pager object
 */
function print_announce_edit($rs, $labs, $edit, &$pager) {
	global $conf;
	$start_date_ok = (isset($rs['start_datetime']) && !empty($rs['start_datetime']));
	$end_date_ok = (isset($rs['end_datetime']) && !empty($rs['end_datetime']));

	$start_date = ($start_date_ok) ? $rs['start_datetime'] : time();
	$end_date = ($end_date_ok) ? $rs['end_datetime'] : time();
	?>
				<form name="addAnnouncement" method="post" action="admin_update.php"
				<?php echo $edit ? "" : "onsubmit=\"return checkAddAnnouncement();\"" ?>>
				<table width="100%" border="0" cellspacing="0" cellpadding="1"
					align="center">
					<tr>
						<td class="tableBorder">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td class="formNames"><?php echo translate('Announcement')?></td>
								<td class="cellColor"><textarea name="announcement"
									class="textbox" cols="100" rows="3" /><?php echo isset($rs['announcement']) ? htmlspecialchars($rs['announcement']) : '' ?></textarea>
								</td>
							</tr>
							<tr>
								<td width="200" class="formNames">Order <?php echo translate('Number')?></td>
								<td class="cellColor"><input type="text" name="number"
									class="textbox" size="3" maxlength="3"
									value="<?php echo isset($rs['number']) ? $rs['number'] : '' ?>" /></td>
							</tr>
							<tr>
								<td class="formNames">Lab</td>
								<td class="cellColor"><select name="lab_id" class="textbox">
								<?php
								if (empty($labs)) {
									echo '<option value="">Please add labs</option>';
								} else {
									echo '<option value="0">Scheduler Announcement</option>';
									for ($i = 0; $i < count($labs); $i++)
									echo '<option value="' . $labs[$i]['lab_id'] . '"' . (isset($rs['lab_id']) && $labs[$i]['lab_id'] == $rs['lab_id'] ? ' selected="selected"' : '') . '>' . $labs[$i]['labTitle'] . "</option>\n";
								}
								?>
								</select></td>
							</tr>
							<tr>
								<td class="formNames"><?php echo translate('Start Date') ?></td>
								<td class="cellColor"><?php echo '<div id="div_start_date" style="float:left;width:70px;">' . CmnFns::formatDate($start_date) . '</div><input type="hidden" id="hdn_start_date" name="start_date" value="' . date('m' . INTERNAL_DATE_SEPERATOR . 'd' . INTERNAL_DATE_SEPERATOR . 'Y', $start_date) . '"/> <a href="javascript:void(0);">&nbsp;&nbsp;<img src="img/calendar.gif" border="0" id="img_start_date" alt="' . translate('Start') . '"/></a>';
								$s_hour = ($start_date_ok) ? date('h', $rs['start_datetime']) : '';
								$s_min = ($start_date_ok) ? date('i', $rs['start_datetime']) : '';
								$s_pm = ($start_date_ok) ? intval(date('H', $rs['start_datetime'])) >= 12 : false;
								echo ' @ <input type="text" maxlength="2" size="2" class="textbox" name="start_hour" value="' . $s_hour . '"/> : <input type="text" maxlength="2" size="2" class="textbox" name="start_min" value="' . $s_min . '"/>';
								echo ' <select name="start_ampm" class="textbox"><option value="am">' . translate('am') . '</option><option value="pm"' . (($s_pm) ? ' selected="selected"' : '') . '>' . translate('pm') . '</option></select>';
								echo ' <input type="checkbox" name="use_start_time"' . ($start_date_ok ? ' checked="checked"' : ''). '/> ' . translate('Use start date/time?');
								?></td>
							</tr>
							<tr>
								<td class="formNames"><?php echo translate('End Date') ?></td>
								<td class="cellColor"><?php echo '<div id="div_end_date" style="float:left;width:70px;">' . CmnFns::formatDate($end_date) . '</div><input type="hidden" id="hdn_end_date" name="end_date" value="' . date('m' . INTERNAL_DATE_SEPERATOR . 'd' . INTERNAL_DATE_SEPERATOR . 'Y', $end_date) . '"/> <a href="javascript:void(0);">&nbsp;&nbsp;<img src="img/calendar.gif" border="0" id="img_end_date" alt="' . translate('End') . '"/></a>';
								$s_hour = ($end_date_ok) ? date('h', $rs['end_datetime']) : '';
								$s_min = ($end_date_ok) ? date('i', $rs['end_datetime']) : '';
								$s_pm = ($end_date_ok) ? intval(date('H', $rs['end_datetime'])) >= 12 : false;
								echo ' @ <input type="text" maxlength="2" size="2" class="textbox" name="end_hour" value="' . $s_hour . '"/> : <input type="text" maxlength="2" size="2" class="textbox" name="end_min" value="' . $s_min . '"/>';
								echo ' <select name="end_ampm" class="textbox"><option value="am">' . translate('am') . '</option><option value="pm"' . (($s_pm) ? ' selected="selected"' : '') . '>' . translate('pm') . '</option></select>';
								echo ' <input type="checkbox" name="use_end_time"' . ($end_date_ok ? ' checked="checked"' : ''). '/> ' . translate('Use end date/time?');
								?></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<br />
				<?php
				// Print out correct buttons
				if (!$edit) {
					echo submit_button(translate('Add Announcement'), 'announcementid') . hidden_fn('addAnnouncement')
					. ' <input type="reset" name="reset" value="' . translate('Clear') . '" class="button" />' . "\n";
				}
				else {
					echo submit_button(translate('Edit Announcement'), 'announcementid') . cancel_button($pager) . hidden_fn('editAnnouncement')
					. '<input type="hidden" name="announcementid" value="' . $rs['announcementid'] . '" />' . "\n";
				}
				echo "</form>\n";
				print_admin_jscalendar_setup($start_date_ok ? $rs['start_datetime'] : null, $end_date_ok ? $rs['end_datetime'] : null);		// Set up the javascript calendars
				// Unset variables
				unset($rs);
}



function print_email_filter_select() {
?>
	<form name="filterEmails" method="post" action="<?php echo $_SERVER['PHP_SELF'].'?tool='. filter_input(INPUT_GET, 'tool', FILTER_SANITIZE_SPECIAL_CHARS); ?> ">
    	<select name="emailFilter">
        	<option value="">Filter Emails</option>
			<option value="current_nc_users_updated"<?php if ($_POST['emailFilter']=='current_nc_users_updated') echo 'selected="selected"';?>>ALL Recent NanoCenter Users (logged in within past 60 days)</option>
            <option value="current_fablab_users_updated" <?php if ($_POST['emailFilter']=='current_fablab_users_updated') echo 'selected="selected"';?>>Recent FabLab Users (logged in within past 60 days)</option>
            <option value="current_aimlab_users_updated" <?php if ($_POST['emailFilter']=='current_aimlab_users_updated') echo 'selected="selected"';?>>Recent AIMLab Users (logged in within past 60 days)</option>
            <option value="aimlab_trained_users" <?php if ($_POST['emailFilter']=='aimlab_trained_users') echo 'selected="selected"';?>>All Trained AIMLab Users</option>
            <option value="current_roboticslab_users_updated" <?php if ($_POST['emailFilter']=='current_roboticslab_users_updated') echo 'selected="selected"';?>>Recent Robotics Lab Users (logged in within past 60 days)</option>
            <option value="all_roboticslab_users_updated" <?php if ($_POST['emailFilter']=='all_roboticslab_users_updated') echo 'selected="selected"';?>>All Permitted Robotics Lab Users</option>
        </select>
        <input type="submit" name="updateEmails" value="Filter" />
    </form>
<?php
}

/**
 * Prints out GUI list to of email addresses
 * Prints out a table with option to email users,
 *  and prints form to enter subject and message of email
 * @param array $users user data
 * @param string $sub subject of email
 * @param string $msg message of email
 * @param array $usr users to send to
 * @param string $err last database error
 */
function print_manage_email($users, $sub, $msg, $usr, $err) {
	?>
				<form name="emailUsers" method="post"
					action="<?php echo $_SERVER['PHP_SELF'] . '?tool=' . filter_input(INPUT_GET, 'tool').'&order='.filter_input(INPUT_GET,'order').'&order_direction='.filter_input(INPUT_GET,'order_direction');?>">
				<table width="60%" border="0" cellspacing="0" cellpadding="1">
					<tr>
						<td class="tableBorder">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td colspan="3" class="tableTitle">&#8250; <?php echo translate('Email Users')?> - <?php echo sizeof($users);?></td>
							</tr>
							<tr class="rowHeaders">
								<td width="15%">&nbsp;</td>
								<td width="40%">
								    <a href="<?php
								    echo $_SERVER['PHP_SELF'] . '?tool=' . filter_input(INPUT_GET, 'tool', FILTER_SANITIZE_SPECIAL_CHARS) .'&order=last_name';
                                    if (isset($_GET['order_direction']) && $_GET['order_direction'] === 'ASC') {
                                        echo '&order_direction=DESC';
                                    } else {
                                        echo '&order_direction=ASC';
                                    }
								    ?>">
								    <?php echo translate('User');
								    if (isset($_GET['order']) && $_GET['order'] === 'last_name') {
                                        if (isset($_GET['order_direction']) && $_GET['order_direction'] === 'ASC') {
                                            echo '&darr;';
                                        } else {
                                            echo '&uarr;';
                                        }
                                    }
								    ?></a>
								</td>
								<td width="45%">
								    <a href="<?php
								    echo $_SERVER['PHP_SELF'] . '?tool=' . filter_input(INPUT_GET, 'tool', FILTER_SANITIZE_SPECIAL_CHARS) . '&order=email';
								    if (isset($_GET['order_direction']) && $_GET['order_direction'] === 'ASC') {
								        echo '&order_direction=DESC';
								    } else {
								        echo '&order_direction=ASC';
								    }
								    ?>">
								    <?php echo translate('Email');
								    if (isset($_GET['order']) && $_GET['order'] === 'email') {
                                        if (isset($_GET['order_direction']) && $_GET['order_direction'] === 'ASC') {
                                            echo '&darr;';
                                        } else {
                                        echo '&uarr;';
                                        }
                                    }?></a>
								</td>
							</tr>
							<?php
							if (!$users)
							echo '<tr class="cellColor0" style="text-align: center;"><td colspan="3">' . $err . '</td></tr>';
							// Print users out in table
							for ($i = 0; is_array($users) && $i < count($users); $i++) {
								$cur = $users[$i];
								echo '<tr class="cellColor' . ($i%2) . "\">\n"
								. '<td style="text-align: center;"><input type="checkbox" ';
								if ( empty($usr) || in_array($cur['email'], $usr) )
								echo 'checked="checked" ';
								echo 'name="emailIDs[]" value="' . $cur['email'] . "\" /></td>\n"
								. '<td>&lt;' . $cur['last_name'] . ', ' . $cur['first_name'] . '&gt;</td>'
								. '<td>' . $cur['email'] . '</td>'
								. "</tr>\n";
							}
							?>
							<tr>
								<td class="cellColor0" style="text-align: center;"><input
									type="checkbox" name="checkAll" checked="checked"
									onclick="checkAllBoxes(this);" /></td>
								<td colspan="2" class="cellColor0">&nbsp;</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<br />
				<table width="60%" border="0" cellspacing="0" cellpadding="5">
					<tr>
						<td width="15%">
						<p><?php echo translate('Subject')?></p>
						</td>
						<td><input type="text" name="subject" size="60" class="textbox"
							value="<?php echo $sub?>" /></td>
					</tr>
					<tr>
						<td valign="top">
						<p><?php echo translate('Message')?></p>
						</td>
						<td><textarea rows="10" cols="60" name="message" class="textbox"><?php echo $msg?></textarea>
						</td>
					</tr>
				</table>
				<input type="submit" name="previewEmail"
					value="<?php echo translate('Next')?> &gt;" class="button" /></form>
					<?php
}

/**
 * Prints out a preview of the email to be sent
 * @param string $sub subject of email
 * @param string $msg message of email
 * @param array $usr array of users to send the email to
 */
function preview_email($sub, $msg, $usr) {
	?>
				<table width="60%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td bgcolor="#DEDEDE">
						<table width="100%" cellpadding="3" cellspacing="1" border="0">
							<tr class="cellColor0">
								<td><?php echo $sub?></td>
							</tr>
							<tr class="cellColor0">
								<td><?php echo $msg?></td>
							</tr>
							<tr class="cellColor0">
								<td><?php
								if (empty($usr)) echo translate('Please select users');
								foreach ($usr as $email) echo $email . '<br />'
								?></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<br />
				<form action="<?php echo $_SERVER['PHP_SELF'] . '?tool=' . filter_input(INPUT_GET, 'tool', FILTER_SANITIZE_SPECIAL_CHARS);?>"
					method="post" name="send_email"><input type="button" name="goback"
					value="&lt; <?php echo translate('Back')?>" class="button"
					onclick="history.back();" /> <input type="submit" name="sendEmail"
					value="<?php echo translate('Send Email')?>" class="button" /></form>
					<?php
}

/**
 * Actually sends the email to all addresses in POST
 * @param string $subject subject of email
 * @param string $msg email message
 * @param bool $success was email successful
 */
function print_email_results($subject, $msg, $success) {
	if (!$success) {
		CmnFns::do_error_box(translate('problem sending email'), '', false);
	} else {
		CmnFns::do_message_box(translate('The email sent successfully.'));
	}

	echo '<h4 align="center">' . translate('do not refresh page') . '<br/>'
	. '<a href="' . $_SERVER['PHP_SELF'] . '?tool=email">' . translate('Return to email management') . '</a></h4>';
}

/**
 * Prints out a list of tables and all the fields in them
 *  with an option to select which tables and fields should be exported
 *  and in which format
 * @param array $tables array of tables
 * @param array $fields array of fields for each table
 */
function show_tables($tables, $fields) {
	echo '<h5>' . translate('Please select which tables and fields to export') . '</h5>'
	. '<form name="get_fields" action="' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '" method="post">' . "\n";
	for ($i = 0; $i < count($tables); $i++) {
		echo '<p><input type="checkbox" name="table[]" value="' . $tables[$i] . '"  checked="checked" onclick="javascript: toggle_fields(this);" />' . $tables[$i] . "</p>\n";

		echo '<select name="table,' . $tables[$i] . '[]" multiple="multiple" size="5" class="textbox">' . "\n";
		echo '<option value="all" selected="selected">' . translate('all fields') . "</option>\n";
		for ($k = 0; $k < count($fields[$tables[$i]]); $k++)
		echo  '<option value="' . $fields[$tables[$i]][$k] . '">' . $fields[$tables[$i]][$k] . '</option>' . "\n";

		echo "</select><br />\n";
	}
	echo '<p><input type="radio" name="type" value="xml" checked="checked" />' . translate('XML')
	. '<input type="radio" name="type" value="csv" />' . translate('CSV')
	. '</p><br /><input type="submit" name="submit" value="' . translate('Export Data') . '" class="button" /></form>';
}

/**
 * Begins the line of table data
 * @param boolean $xml if this is in XML or not
 * @param string $table_name name of this table
 */
function start_exported_data($xml, $table_name) {
	echo '<pre>';
	echo ($xml) ? "&lt;$table_name&gt;\r\n" : '';
}

/**
 * Prints out the exported data in XML or CSV format
 * @param array $data array of data to print out
 * @param boolean $xml whether to print XML or not
 */
function print_exported_data($data, $xml) {
	$first_row = true;
	for ($x = 0; $x < count($data); $x++) {
		echo ($xml) ? "\t&lt;record&gt;\r\n" : '';

		if (!$xml && $first_row) {				// Print out names of fields for first row of CSV
			$keys = array_keys($data[$x]);
			for ($i = 0; $i < count($keys); $i++) {
				echo '"' . $keys[$i] . '"';
				if ($i < count($keys)-1) echo ',';
			}
			echo "\r\n";
		}

		$first_row = false;

		$first_csv = '"';
		foreach ($data[$x] as $k => $v) {
			echo ($xml) ? "\t\t&lt;$k&gt;$v&lt;/$k&gt;\r\n" : $first_csv . addslashes($v) . '"';
			$first_csv = ',"';
		}
		echo ($xml) ? "\t&lt;/record&gt;\r\n" : "\r\n";
	}
}

/**
 * Prints out an interface to manage blackout times for this resource
 * @param array $resource array of resource data
 * @param array $blackouts array of blackout data
 */
function print_blackouts($resource, $blackouts) {
	for ($i = 0; $i < count($resource); $i++)
	echo $resource[$i] . '<br />';
}

/**
 * Ends the line of table data
 * @param boolean $xml if this is in XML or not
 * @param string $table_name name of this table
 */
function end_exported_data($xml, $table_name) {
	echo ($xml) ? "&lt;/$table_name&gt;\r\n" : '';
	echo '</pre>';
}

/**
 * Prints the form to reset a users password
 * @param object $user user object
 */
function print_reset_password(&$user) {
	?>
				<form name="resetpw" method="post" action="admin_update.php">
				<table border="0" cellspacing="0" cellpadding="1" width="50%">
					<tr>
						<td class="tableBorder">
						<table cellspacing="1" cellpadding="2" border="0" width="100%">
							<tr class="rowHeaders">
								<td colspan="2"><?php echo translate('Reset Password for', array($user->get_name()))?></td>
							</tr>
							<tr class="cellColor">
								<td width="15%" valign="top"><?php echo translate('Password')?></td>
								<td><input type="password" value="" class="textbox"
									name="password" /> <br />
								<i><?php echo translate('If no value is specified, the default password set in the config file will be used.')?></i>
								</td>
							</tr>
							<tr class="cellColor">
								<td colspan="2"><input type="checkbox" name="notify_user"
									value="true" checked="checked" /><?php echo translate('Notify user that password has been changed?')?></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<input type="hidden" name="user_id" value="<?php echo $user->get_id()?>" />
				<br />
				<?php echo  submit_button(translate('Save')) . hidden_fn('resetPass')?> <input
					type="button" name="cancel" value="<?php echo translate('Manage Users')?>"
					class="button"
					onclick="document.location='<?php echo $_SERVER['PHP_SELF']?>?tool=users';" />
				</form>
				<?php
}

/**
 * Prints out a link to reorder recordset ascending order
 * @param Object $pager pager object
 * @param string $order order to sort result set by
 * @param string $text link text
 * @see print_asc_desc_link()
 */
function printAscLink(&$pager, $order, $text) {
	$text = translate("Sort by ascending $text");
	print_asc_desc_link($pager, $order, $text, 'ASC');
}

/**
 * Prints out a link to reorder recordset descending order
 * @param Object $pager pager object
 * @param string $order order to sort result set by
 * @param string $text link text
 * @see print_asc_desc_link()
 */
function printDescLink(&$pager, $order, $text) {
	$text = translate("Sort by descending $text");
	print_asc_desc_link($pager, $order, $text, 'DESC');
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
function print_asc_desc_link(&$pager, $order, $text, $vert) {
	global $link;

	$tool = getTool();
	$page = $pager->getPageNum();

	$plus_minus = ($vert == 'ASC') ? '[+]' : '[&#8211;]';		// Plus or minus box
	$limit_str = '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit();
	$page_str  = '&amp;' . $pager->getPageVar() . '=' . $pager->getPageNum();
	$vert_str  = "&amp;vert=$vert";


	// Fix up the query string
	$query =  $_SERVER['QUERY_STRING'];
	if (preg_match('/(\/?|&)(' . $pager->getLimitVar() . ")=[0-9]*/i", $query))
	$query = preg_replace('/(\/?|&)' . $pager->getLimitVar() . "=[0-9]*/i", $limit_str, $query);
	else
	$query .= $limit_str;

	if (preg_match('/(\/?|&)' . $pager->getPageVar() . "=[0-9]*/i", $query))
	$query = preg_replace('/(\/?|&)' . $pager->getPageVar() . "=[0-9]*/i", $page_str, $query);
	else
	$query .= $page_str;

	if (preg_match('/(\/?|&)vert=[a-zA-Z]*/i', $query))
	$query = preg_replace('/(\/?|&)vert=[a-zA-Z]*/i', $vert_str, $query);
	else
	$query .= $vert_str;
	//echo "ORDER: ".$order."<BR>";
	if (preg_match('/(\/?|&)order=[a-zA-Z]*/i', $query))
	$query = preg_replace('/(\/?|&)order=[a-zA-Z_]*/i', "&amp;order=$order", $query);
	else
	$query .= "&amp;order=$order";

	$link->doLink($_SERVER['PHP_SELF'] . '?' . $query, $plus_minus, '', '', $text);
}

/**
 * Returns a button to cancel editing
 * @param none
 * @return string of html for a cancel button
 */
function cancel_button(&$pager) {
	return '<input type="button" name="cancel" value="' . translate('Cancel') . '" class="button" onclick="javascript: document.location=\'' . $_SERVER['PHP_SELF'] . '?tool=' . filter_input(INPUT_GET, 'tool', FILTER_SANITIZE_SPECIAL_CHARS) . '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit() . '&amp;' . $pager->getPageVar() . '=' . $pager->getPageNum() . '\';" />' . "\n";
}

/**
 * Returns a submit button with $value value
 * @param string $value value of button
* @param string $get_value value in the query string for editing an item (ie, to edit a resource its machid)
* @return string of html for a submit button
*/
function submit_button($value, $get_value = '') {
	return '<input type="submit" name="submit" value="' . $value . '" class="button" />' . "\n"
			. '<input type="hidden" name="get" value="' . $get_value  . '" />' . "\n";
}

/**
* Returns a hidden fn field
* @param string $value value of the hidden field
* @return string of html for hidden fn field
*/
function hidden_fn($value) {
	return '<input type="hidden" name="fn" value="'. $value . '" />' . "\n";
}

/**
* Prints out the javascript necessary to set up the calendars for choosing start/end dates
* @param int $start initial start date time
* @param int $end initial end date time
*/

function print_admin_jscalendar_setup($start = null, $end = null) {
	global $dates;
	if ($start == null) { $start = time(); }
	if ($end == null) { $end = time(); }
	?>

	<script type="text/javascript">
		var start = new Date(<?php echo date('Y', $start) . ',' . (intval(date('m', $start))-1) . ',' . date('d', $start)?>);
		// Start date calendar
		Calendar.setup(
		{
			inputField : "hdn_start_date", // ID of the input field
			ifFormat : "<?php echo '%m' . INTERNAL_DATE_SEPERATOR . '%d' . INTERNAL_DATE_SEPERATOR . '%Y'?>", // the date format
			daFormat : "<?php echo $dates['general_date']?>", // the date format
			button : "img_start_date", // ID of the button
			date : start,
			displayArea : "div_start_date"
		}
		);

		var end = new Date(<?php echo date('Y', $end) . ',' . (intval(date('m', $end))-1) . ',' . date('d', $end)?>);
		// End date calendar
		Calendar.setup(
		{
			inputField : "hdn_end_date", // ID of the input field
			ifFormat : "<?php echo '%m' . INTERNAL_DATE_SEPERATOR . '%d' . INTERNAL_DATE_SEPERATOR . '%Y'?>", // the date format
			daFormat : "<?php echo $dates['general_date']?>", // the date format
			button : "img_end_date", // ID of the button
			date : end,
			displayArea : "div_end_date"
		}
		);
	</script> <?php
}


function print_equipment_user_list($users) {

}



/**
 * This function prints out a form used to search for an account
 */
function print_search_accounts() {
	global $link;
?>
<table width="100%"><tr><td class="tableBorder">
	<table width="100%">
		<tr><td colspan="2" class="tableTitle">Search Accounts</td></tr>
		<tr class="cellColor">
			<td colspan="2">
				<form action="" name="search_accounts_form" method="post">
				<table width="100%">
					<tr><td class="rowHeaders">FRS:</td><td class="cellColor0"><input type="text" name="frs" /></td></tr>
					<tr><td class="rowHeaders">Account ID:</td><td class="cellColor0"><input type="text" name="account_id" /></td></tr>
					<tr><td class="cellColor0"><input type="submit" name="search_accounts" value="Search" /></td></tr>
				</table>
				</form>
			</td>
		</tr>
	</table>
</td></tr></table>
<?php
}

function print_account_type_select_box($select_name = 'account_type', $account_types, $selected = '') {
	if (is_array($account_types)) {
		echo '<select name="'.$select_name.'">';
		echo '<option value="">Select an account type</option>';
		foreach ($account_types as $type) {
			echo '<option value="'.$type['id'].'"';
			if ($type['id']===$selected) {
			    echo ' selected="selected"';
			}
			echo '>'.$type['label'].'</option>';
		}
		echo '</select>';
	}
}


function print_account_category_select_box($select_name = 'account_category', $account_categories, $selected = '') {
	if (is_array($account_categories)) {
		echo '<select name="'.$select_name.'">';
		echo '<option value="">Select an account category</option>';
		foreach ($account_categories as $cat) {
			echo '<option value="'.$cat['id'].'"';
			if ($cat['id']===$selected) {
			    echo ' selected="selected"';
			}
			echo '>'.$cat['label'].'</option>';
		}
		echo '</select>';
	}
}

?>