<?php
/**
* This file provides output functions for ctrlpnl.php
* No data manipulation is done in this file
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author Adam Moore
* @author David Poole <David.Poole@fccc.edu>
* @author Richard Cantzler <rmcii@users.sourceforge.net>
* @version 07-07-05
* @package Templates
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/

// Get Link object
$link = CmnFns::getNewLink();

/**
* This function prints out the announcement table
*
* @param array announcements
* @global $conf
* @global $link
*/
function showAnnouncementTable($announcements) {
	global $link;
	global $conf;
    ?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="announceTableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="announceTableTitle"><font size="+3"><?php echo translate('My Announcements') ?></font>
		  </td>
          <td class="announceTableTitle">
            <div align="right">
              <?php $link->doLink('javascript: help(\'my_announcements\');', '?', '', 'color: #FFFFFF;', translate('Help') . ' - ' . translate('My Announcements')) ?>
            </div>
          </td>
        </tr>
      </table>
      <div id="announcements" style="display: <?php getShowHide('announcements') ?>">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr class="cellColor">
          <td colspan="2">
            <ul style="margin-bottom:1.2em; margin-top:1em">
              <?php
				// Cycle through and print out announcements
                if (!$announcements) {
                    echo "<li>There are no announcements.</li>\n";
                } else {
                	$curLab = '';
                  foreach ($announcements as $a) {
                    if (empty($a['nickname'])) $a['nickname'] = 'Scheduler';
                      if ($curLab !== $a['nickname'] || empty($a['nickname'])) {
                  			$curLab = $a['nickname'];
                  			echo '<li style="margin-top:1em;margin-left:-1.2em;list-style-type:none;font-weight:bold;font-size:1.2em;color:#900;">'.$curLab.'</li>';
                      }
                      echo '<li style="margin-top: .5em"><strong>' . $a['announcement'] . '</strong></li>';
                  }
                  unset($announcements);
                }
				?>
            </ul>
          </td>
        </tr>
      </table>
	 </div>
    </td>
  </tr>
</table>
<?php
}


/**
* Print table listing upcoming reservations
* This function prints a table of all upcoming
* reservations for the current user.  It also
* provides a way for them to modify and delete
* their reservations
* @param mixed $res array of reservation data
* @param string $err last error message from database
*/
function showReservationTable($res, $err, $res_in_use=NULL) {
	global $link;
  $limit = 25; // default list limit

  if (array_key_exists('limit', $_POST)) {
    $limit = filter_var($_POST['limit'], FILTER_SANITIZE_SPECIAL_CHARS);
  } else if (array_key_exists('limit', $_GET)) {
    $limit = filter_var($_GET['limit'], FILTER_SANITIZE_SPECIAL_CHARS);
  }
  /*
	if(isset($_POST['limit']) && empty($_POST['limit'])){
		if(isset($_GET['limit']) && empty($_GET['limit']))
		  $_GET['limit']=25;
		$_POST['limit']=$_GET['limit'];
	}
  */
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td colspan="<?php
          	if(Auth::isAdmin())
		  				echo "9";
		  			else
							echo "7";
						?>" class="tableTitle">
		    <a href="javascript: void(0);" onclick="showHideCpanelTable('reservation');"><?php echo translate('My Reservations')?></a>
		  </td>
          <td class="tableTitle">
            <div align="right">
              <?php $link->doLink('javascript: help(\'my_reservations\');', '?', '', 'color: #FFFFFF;', translate('Help') . ' - ' . translate('My Reservations')) ?>
            </div>
          </td>
        </tr>
      </table>
      <div id="reservation" style="display: <?php echo getShowHide('reservation') ?>">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <?php

	// Write message if they have no reservations
	if (!$res){
		echo '<tr class="cellColor"><td colspan="7" align="center">' . $err . '<br></td></tr>';
	}else{
?>
        <tr class="rowHeaders">
          <td width="10%"><?php echo translate('Date'); ?></td>
          <td width="10%"><?php echo translate('Start Time'); ?></td>
          <td width="10%"><?php echo translate('End Time'); ?></td>
          <td width="23%"><?php echo translate('Resource'); ?></td>
          <!--  <td width="8%">Sign In</td> -->
          <td width="8%"><?php echo translate('Modify'); ?></td>
          <td width="8%"><?php echo translate('Delete'); ?></td>
        </tr>
        <tr class="cellColor" style="text-align: center">
          <td>
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=start_date&amp;vert=DESC&amp;limit='.$limit, '[&#8211;]', '', '', translate('Sort by descending date')) ?>
			&nbsp;&nbsp;
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=start_date&amp;vert=ASC&amp;limit='.$limit, '[+]', '', '', translate('Sort by ascending date')) ?>
          </td>
          <td>
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=startTime&amp;vert=DESC&amp;limit='.$limit, '[&#8211;]', '', '', translate('Sort by descending start time')) ?>
			&nbsp;&nbsp;
            <?php
			$link->doLink($_SERVER['PHP_SELF'].'?order=startTime&amp;vert=ASC&amp;limit='.$limit, '[+]', '', '', translate('Sort by ascending start time')) ?>
          </td>
          <td>
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=endTime&amp;vert=DESC&amp;limit='.$limit, '[&#8211;]', '', '', translate('Sort by descending end time')) ?>
			&nbsp;&nbsp;
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=endTime&amp;vert=ASC&amp;limit='.$limit, '[+]', '', '', translate('Sort by ascending end time')) ?>
          </td>
          <td>
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=name&amp;vert=DESC&amp;limit='.$limit, '[&#8211;]', '', '', translate('Sort by descending resource name')) ?>
			&nbsp;&nbsp;
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=name&amp;vert=ASC&amp;limit='.$limit, '[+]', '', '', translate('Sort by ascending resource name')) ?>
          </td>
          <!-- <td>&nbsp;</td> -->
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
<?php
		// For each reservation, clean up the date/time and print it
		for ($i = 0; is_array($res) && $i < count($res); $i++) {
			$rs = $res[$i];
			$reservation = new Reservation($rs['resid']);
			$class = 'cellColor' . ($i%2);
			$modified = (isset($rs['modified']) && !empty($rs['modified'])) ?
			CmnFns::formatDateTime($rs['modified']) : 'N/A';
			echo "        <tr class=\"$class\" align=\"center\">\n"
						. '          <td>' . $link->getLink("javascript: reserve('".RES_TYPE_VIEW."','','','" . $rs['resid']. "');", CmnFns::formatDate($rs['start_date']), '', '', translate('View this reservation')) . "</td>\n"
						. '          <td>' . CmnFns::formatTime($rs['startTime']) . "</td>\n"
						. '          <td>' . CmnFns::formatTime($rs['endTime']) . "</td>\n"
						. '          <td style="text-align:left;">' . $rs['name'] . "</td>\n"
						;

			// if local time is 15 min past reservation time then cannot sign in to the reservation
			// reservation should be charged - function to be made later
			//if (CmnFns::formatDate($rs['start_date']) == date("m/d/Y") && $rs['status'] == 'a' && strtotime($rs['startTime']) < time()  ) {
			/*
			echo "<td>";
			if (CmnFns::formatDate($rs['start_date']) == date("m/d/Y") && $rs['status'] == 'a'  ) {
				// if signed in show sign out link
				//echo "time: ".strtotime($rs['startTime'])."<br>";
				//echo "now: ".(time())."<br>";
				if($rs['signin']!=NULL){
					if ($rs['signout']==NULL){
						echo $link->getLink("javascript: reserve('o','','','" . $rs['resid'] . "');", 'Sign Out', '', '', 'Sign Out of this reservation');
						echo "			 </td>\n"
							. '          <td></td>'
							. '          <td></td>'
							. "        </tr>\n";
					}else{
						echo "			 Completed</td>\n"
							. '          <td></td>'
							. '          <td></td>'
							. "        </tr>\n";
					}
				}else{
					if (!in_array($rs['machid'], $res_in_use)){ echo $link->getLink("javascript: reserve('i','','','" . $rs['resid'] . "');", 'Sign In', '', '', 'Sign In for this reservation'); }
					echo "			 </td>\n"
						. '          <td>' . $link->getLink("javascript: reserve('".RES_TYPE_MODIFY."','','','" . $rs['resid'] . "');", translate('Modify'), '', '', translate('Modify this reservation')) . "</td>\n"
						. '          <td>' . $link->getLink("javascript: reserve('".RES_TYPE_DELETE."','','','" . $rs['resid'] . "');", translate('Delete'), '', '', translate('Delete this reservation')) . "</td>\n"
						. "        </tr>\n";
				}
			echo "			 </td>\n"
			*/
			//}else{

				// if the reservation is in the future, past the 'no modification'(24hr) horizon
				if($reservation->check_horizon(Auth::getCurrentID())) {
					echo '<td>' . $link->getlink("javascript: reserve('".RES_TYPE_MODIFY."','','','" . $rs['resid']. "');", translate('Modify')) . '</td>';
					echo '<td>' . $link->getLink("javascript: reserve('".RES_TYPE_DELETE."','','','" . $rs['resid']. "');", translate('Delete')) . '</td>';
				}else{
					echo '<td></td><td></td>';
				}
				unset ($reservation);
				echo "        </tr>\n";
			//}
		}
		unset($res);
	}
?>
      </table>
	  </div>
    </td>
  </tr>
</table>
<?php
}


/**
*
*
*
*/
function showLabReservationsTable($calendar){
	global $link;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="tableTitle">
		    <a href="javascript: void(0);" onclick="showHideCpanelTable('todaysRes');">Today's Reservations</a>
		  </td>
          <td class="tableTitle">
            <div align="right">
              <?php $link->doLink('javascript: help(\'todaysRes\');', '?', '', 'color: #FFFFFF;', translate('Help') . ' - ' . translate('Signed In Users')) ?>
            </div>
          </td>
        </tr>
      </table>
      <div id="todaysRes" style="display: <?php echo getShowHide('todaysRes') ?>">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
	    <tr><td bgcolor="#efefef">
<?php
	$calendar->print_calendar(Auth::isAdmin());
?>
		</td></tr>
	  </table>
	  </div>
	</td>
  </tr>
</table>
<?php
}


/**
* Print table with all user training information
* @param mixed $per permissions array
* @param string $err last database error
*/
function showTrainingTable($per, $err, $res_in_use=NULL, $mach_reserved=NULL) {
	global $link;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="tableTitle" colspan="3">
		    <a href="javascript: void(0);" onclick="showHideCpanelTable('permissions');">My Equipment Access</a>
		  </td>
          <td class="tableTitle">
            <div align="right">
              <?php $link->doLink('javascript: help(\'my_training\');', '?', '', 'color: #FFFFFF', translate('Help') . ' - ' . translate('My Permissions')) ?>
            </div>
          </td>
        </tr>
      </table>
      <div id="permissions" style="display: <?php echo getShowHide('permissions') ?>;">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <?php
	// If they have no training, inform them
	if (!$per){
		echo '<tr><td colspan="4" class="cellColor" align="center">' . $err . '</td></tr>';
	}else{
	?>
        <tr class="rowHeaders">
          <td><?php echo translate('Resource')?></td>
          <td>Current Status</td>
          <td>Lab</td>
          <td>Calendar</td>
          <td>Reserve</td>
          <!-- <td width="15%"><?php echo translate('Phone')?></td> -->
          <!-- <td width="40%"><?php echo translate('Notes')?></td> -->
        </tr>
<!--
        <tr class="cellColor" style="text-align: center">
          <td>
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=name&amp;vert=DESC', '[&#8211;]', '', '', translate('Sort by descending resource name')); ?>
			&nbsp;&nbsp;
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=name&amp;vert=ASC', '[+]', '', '', translate('Sort by ascending resource name')); ?>
		  </td>
		  <td>&nbsp;</td>
          <td>
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=nickname&amp;vert=DESC', '[&#8211;]', '', '', translate('Sort by descending lab')); ?>
			&nbsp;&nbsp;
            <?php $link->doLink($_SERVER['PHP_SELF'].'?order=nickname&amp;vert=ASC', '[+]', '', '', translate('Sort by ascending lab')); ?>
		  </td>
          <td>&nbsp;</td>
        </tr>
-->
	<?php
	// Cycle through and print out machines
		for ($i = 0; is_array($per) && $i < count($per); $i++) {
			$rs = $per[$i];
			$class = 'cellColor' . ($i%2);
			echo "<tr class=\"$class\">\n"
				. '<td>' . $rs['name'] . '</td>'
				. "<td align='center' nowrap><font color='#" . ($rs['status'] == 'a' ? "009900'>Active" : "FF0000'>Inactive") . "</font>";

			// chech if resource is currently in use and label.
      /*
				. " - <font color='#";
			if (in_array($rs['machid'], $res_in_use)){
				echo "ff0000'>In Use";
			}else{
				echo "009900'>Not In Use";
			}
			echo "</font>";
      */
      echo '</td>';
      echo '<td>' . $rs['nickname'] . '</td>';

      echo '<td>';
      $link->doLink('rescalendar.php?date='.date('n-j-Y').'&amp;view=2&amp;machid='.$rs['machid'], 'View Calendar', '', '', 'View Calendar');
      echo '</td>';

      echo '<td>';
      if ($rs['status'] == 'a') {
      	$link->doLink("javascript: reserve('".RES_TYPE_ADD."', '".$rs['machid']."','".mktime()."', '', '".$rs['lab_id']."', '0', '0', '');", 'Make Reservation', '', '','Make a Reservation');
      }
      echo '</td>'
				//. '<td>' . $rs['rphone'] . '</td>'
				//. '<td>' . $rs['notes'] . '</td>'
				. "</tr>\n";
		}
		unset($per);
	}
    ?>
      </table>
	  </div>
    </td>
  </tr>
</table>
<?php
}

/**
* Print table with all account information
* @param mixed $accounts accounts data array
* @param string $err last database error
*/
function showAccountsTable($accounts, $err) {
	global $link;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="tableTitle" colspan="3">
		    <a href="javascript: void(0);" onclick="showHideCpanelTable('accounts');">&#8250; Accounts</a>
		  </td>
          <td class="tableTitle">
<!--
            <div align="right">
              <?php //$link->doLink('javascript: help(\'my_training\');', '?', '', 'color: #FFFFFF', translate('Help') . ' - ' . translate('My Permissions')) ?>
            </div>
-->
          </td>
        </tr>
      </table>
      <div id="accounts" style="display: <?= getShowHide('accounts') ?>;">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr class="rowHeaders">
          <td width="20%">FRS</td>
          <td width="15%">Current Status</td>
          <td width="25%">PI</td>
          <td width="25%">Last Update</td>
          <td></td>
        </tr>
        <?php
	// If they have no training, inform them
	if (!$per)
		echo '<tr><td colspan="4" class="cellColor" align="center">' . $err . '</td></tr>';

	// Cycle through and print out machines
    for ($i = 0; is_array($accounts) && $i < count($accounts); $i++) {
		$rs = $accounts[$i];
		$class = 'cellColor' . ($i%2);
		echo "<tr class=\"$class\">\n"
            . '<td>' . $rs['frs'] . '</td>'
			. "<td><font color='#" . ($rs['status'] == 'a' ? "009900'>Active" : "FF0000'>Inactive") . "</font></td>"
			. '<td>' . $rs['pi'] . '</td>'
			. '<td>' . $rs['last_updated'] . '</td>'
            . '<td><input type=\'checkbox\' name=\'mass_update\' value=\'' . $rs['account_id'] . '\'></td>'
			. "</tr>\n";
	}
	unset($accounts);
    ?><font color="#009900" color="#FF0000">
      </table>
	  </div>
    </td>
  </tr>
</table>
<?php  $link->doLink('javascript: account(\'a\',\'\',\'\',\'\');', 'Add Account', '', '', 'Add an account');
}



/**
* This function prints a table of all upcoming
* reservations that the current user has been invited to but not yet responded to.
* It also provides a way for them to accept/decline invitations
* @param mixed $res array of reservation data
* @param string $err last error message from database
*/
function showInvitesTable($res, $err) {
	global $link;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td colspan="7" class="tableTitle">
		    <a href="javascript: void(0);" onclick="showHideCpanelTable('invites');">&#8250; <?=translate('My Invitations')?></a>
		  </td>
          <td class="tableTitle">
            <div align="right">
              <?php $link->doLink('javascript: help(\'my_invitations\');', '?', '', 'color: #FFFFFF;', translate('Help') . ' - ' . translate('My Invitations')) ?>
            </div>
          </td>
        </tr>
      </table>
      <div id="invites" style="display: <?= getShowHide('invites') ?>">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr class="rowHeaders">
          <td width="10%"><?=translate('Start Date')?></td>
          <td width="10%"><?=translate('End Date')?></td>
          <td width="23%"><?=translate('Resource')?></td>
          <td width="10%"><?=translate('Start Time')?></td>
          <td width="10%"><?=translate('End Time')?></td>
          <td width="20%"><?=translate('Owner')?></td>
          <td width="8%"><?=translate('Accept')?></td>
          <td width="8%"><?=translate('Decline')?></td>
		</tr>
        <?php

	// Write message if they have no reservations
	if (!$res)
		echo '        <tr class="cellColor"><td colspan="8" align="center">' . $err . '</td></tr>';

    // For each reservation, clean up the date/time and print it
	for ($i = 0; is_array($res) && $i < count($res); $i++) {
		$rs = $res[$i];
		$class = 'cellColor' . ($i%2);
		echo "        <tr class=\"$class\" align=\"center\">"
					. '          <td>' . $link->getLink("javascript: reserve('v','','','" . $rs['resid']. "');", CmnFns::formatDate($rs['start_date']), '', '', translate('View this reservation')) . '</td>'
					. '          <td>' . $link->getLink("javascript: reserve('v','','','" . $rs['resid']. "');", CmnFns::formatDate($rs['end_date']), '', '', translate('View this reservation')) . '</td>'
					. '          <td style="text-align:left;">' . $rs['name'] . '</td>'
					. '          <td>' . CmnFns::formatTime($rs['startTime']) . '</td>'
					. '          <td>' . CmnFns::formatTime($rs['endTime']) . '</td>'
                    . '          <td style="text-align:left;">' . $rs['first_name'] . ' ' . $rs['last_name'] . '</td>'
					. '          <td>' . $link->getLink("manage_invites.php?id={$rs['resid']}&amp;user_id={$rs['user_id']}&amp;accept_code={$rs['accept_code']}&amp;action=" . INVITE_ACCEPT, translate('Accept'), '', '', translate('Accept or decline this reservation')) . '</td>'
					. '          <td>' . $link->getLink("manage_invites.php?id={$rs['resid']}&amp;user_id={$rs['user_id']}&amp;accept_code={$rs['accept_code']}&amp;action=" . INVITE_DECLINE, translate('Decline'), '', '', translate('Accept or decline this reservation')) . '</td>'
					. "        </tr>\n";
	}
	unset($res);
?>
      </table>
	  </div>
    </td>
  </tr>
</table>
<?php
}

/**
* This function prints a table of all upcoming
* reservations that the current user has been invited to but not yet responded to.
* It also provides a way for them to accept/decline invitations
* @param mixed $res array of reservation data
* @param string $err last error message from database
*/
function showParticipatingTable($res, $err) {
	global $link;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td colspan="7" class="tableTitle">
		    <a href="javascript: void(0);" onclick="showHideCpanelTable('accepted');">&#8250; <?=translate('My Reservation Participation')?></a>
		  </td>
          <td class="tableTitle">
            <div align="right">
              <?php $link->doLink('javascript: help(\'my_participation\');', '?', '', 'color: #FFFFFF;', translate('Help') . ' - ' . translate('My Reservation Participation')) ?>
            </div>
          </td>
        </tr>
      </table>
      <div id="accepted" style="display: <?= getShowHide('accepted') ?>">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr class="rowHeaders">
          <td width="10%"><?=translate('Start Date')?></td>
          <td width="10%"><?=translate('End Date')?></td>
          <td width="23%"><?=translate('Resource')?></td>
          <td width="10%"><?=translate('Start Time')?></td>
          <td width="10%"><?=translate('End Time')?></td>
          <td width="20%"><?=translate('Owner')?></td>
          <td width="16%"><?=translate('End Participation')?></td>
		</tr>
        <?php

	// Write message if they have no reservations
	if (!$res)
		echo '        <tr class="cellColor"><td colspan="7" align="center">' . $err . '</td></tr>';

    // For each reservation, clean up the date/time and print it
	for ($i = 0; is_array($res) && $i < count($res); $i++) {
		$rs = $res[$i];
		$class = 'cellColor' . ($i%2);
		echo "        <tr class=\"$class\" align=\"center\">"
					. '          <td>' . $link->getLink("javascript: reserve('v','','','" . $rs['resid']. "');", CmnFns::formatDate($rs['start_date']), '', '', translate('View this reservation')) . '</td>'
					. '          <td>' . $link->getLink("javascript: reserve('v','','','" . $rs['resid']. "');", CmnFns::formatDate($rs['end_date']), '', '', translate('View this reservation')) . '</td>'
					. '          <td style="text-align:left;">' . $rs['name'] . '</td>'
					. '          <td>' . CmnFns::formatTime($rs['startTime']) . '</td>'
					. '          <td>' . CmnFns::formatTime($rs['endTime']) . '</td>'
                    . '          <td style="text-align:left;">' . $rs['first_name'] . ' ' . $rs['last_name'] . '</td>'
					. '          <td>' . $link->getLink("manage_invites.php?id={$rs['resid']}&amp;user_id={$rs['user_id']}&amp;accept_code={$rs['accept_code']}&amp;action=" . INVITE_DECLINE, translate('End Participation'), '', '', translate('End Participation')) . '</td>'
					. "        </tr>\n";
	}
	unset($res);
?>
      </table>
	  </div>
    </td>
  </tr>
</table>
<?php
}

/**
* Print out a table of links for user or administrator
* This function prints out a table of links to
* other parts of the system.  If the user is an admin,
* it will print out links to administrative pages, also
* @param none
*/
function showQuickLinks() {
	global $conf;
	global $link;
?>
<div id="quickLinks" style="">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="tableTitle" style="background-color:#000000;">
		    &#8250; <?=translate('My Quick Links')?>
		  </td>
          <td class="tableTitle" style="background-color:#000000;"><div align="right">
              <?php $link->doLink("javascript: help('quick_links');", '?', '', 'color: #FFFFFF', translate('Help') . ' - ' . translate('My Quick Links')) ?>
            </div>
          </td>
        </tr>
      </table>
      <div id="quicklinks" style="display: <?= getShowHide('quicklinks') ?>;">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr style="padding: 5px;" class="cellColor">
          <td colspan="2">
            <p><b>&raquo;</b>
              <?php $link->doLink('schedule.php', 'Make a Reservation') ?>
            </p>
			<p><b>&raquo;</b>
              <?php $link->doLink('mycalendar.php?view=2', 'View My Calendar') ?>
            </p>
			<p><b>&raquo;</b>
              <?php $link->doLink('rescalendar.php?view=2', translate('Resource Calendar')) ?>
            </p>

			<p><br></p>

            <p><b>&raquo;</b>
              <?php $link->doLink('/edit_profile.php', 'Edit My Info') ?>
            </p>
            <p><b>&raquo;</b>
              <?php $link->doLink('my_email.php', 'My Preferences') ?>
            </p>
            <p><b>&raquo;</b>
              <?php $link->doLink('my_accounts.php', 'My Accounts') ?>
            </p>
            <p><b>&raquo;</b>
              <?php $link->doLink('my_reservations.php', 'My Reservations') ?>
            </p>

			<p><br></p>

			<p><b>&raquo;</b>
              <?php $link->doLink('mailto:' . $conf['app']['adminEmail'].'?cc=' . $conf['app']['ccEmail'], translate('Email Administrator'), '', '', 'Send a non-technical email to the administrator') ?>
            </p>
            <p><b>&raquo;</b>
              <?php $link->doLink('index.php?logout=true', translate('Log Out')) ?>
            </p>
            <?php
			// If it's the admin, print out admin links
			if (Auth::isAdmin()) {
				echo
					  '<p style="margin-top:2em;font-weight:bold;text-align:center;">' . translate('System Administration') . '</p>'
					. '<p style="margin-top:1em;"><b>&raquo;</b> ' .  $link->getLink('admin.php?tool=announcements', translate('Manage Announcements')) . "</p>\n"
					. '<p style="margin-top:1em;"><b>&raquo;</b> ' .  $link->getLink('admin.php?tool=labs', translate('Manage Labs')) . "</p>\n"
					. '<p><b>&raquo;</b> ' .  $link->getLink('admin.php?tool=resources', translate('Manage Resources')) . "</p>\n"
					. '<p style="margin-top:1em;"><b>&raquo;</b> ' .  $link->getLink('admin.php?tool=users', translate('Manage Users')) . "</p>\n"
					. '<p><b>&raquo;</b> ' .  $link->getLink('admin.php?tool=accounts', 'Manage Accounts') . "</p>\n"
					. '<p style="margin-top:1em;"><b>&raquo;</b> ' .  $link->getLink('admin.php?tool=reservations', translate('Manage Reservations')) . "</p>\n"
					. '<p><b>&raquo;</b> ' .  $link->getLink('blackouts.php', translate('Manage Blackout Times')) . "</p>\n"
					. '<p><b>&raquo;</b> ' .  $link->getLink('admin.php?tool=approval', translate('Approve Reservations')) . "</p>\n"
					. '<p style="margin-top:1em;"><b>&raquo;</b> ' .  $link->getLink('admin.php?tool=email', translate('Mass Email Users')) . "</p>\n"
	        . '<p><b>&raquo;</b> ' .  $link->getLink('usage.php', translate('Search Resource Usage')) . "</p>\n"
					. '<p><b>&raquo;</b> ' .  $link->getLink('admin.php?tool=export', translate('Export Database Content')) . "</p>\n"
					. '<p><b>&raquo;</b> ' .  $link->getLink('stats.php', translate('View System Stats')) . "</p>\n";
			}
		?>

          </td>
        </tr>
      </table>
	  </div>
    </td>
  </tr>
</table>
</div>
<?php
}

/**
* Print out break to be used between tables
* @param none
*/
function printCpanelBr() {
	echo '<p>&nbsp;</p>';
}

/**
* Returns the proper expansion type for this table
*  based on cookie settings
* @param string table name of table to check
* @return either 'block' or 'none'
*/
function getShowHide($table) {
	if (isset($_COOKIE[$table]) && $_COOKIE[$table] == 'hide') {
		return 'none';
	}
	else
		return 'block';
}

function startQuickLinksCol() {
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td style="vertical-align:top; width:200px; border:solid 2px #000000; background-color:#FFFFFF;">
<?php
}

function startDataDisplayCol() {
?>
</td>
<td style="padding-left:5px; vertical-align:top;">
<?php
}

function endDataDisplayCol() {
?>
</td>
</tr>
</table>
<?php
}

/**
* Displays users currently Signed in
*
*
*/
function showSignedInUsers($users){
	global $link;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td colspan="7" class="tableTitle">
		    <a href="javascript: void(0);" onclick="showHideCpanelTable('signedin');">Current Users Signed In</a>
		  </td>
          <td class="tableTitle">
            <div align="right">
              <?php $link->doLink('javascript: help(\'signedin\');', '?', '', 'color: #FFFFFF;', translate('Help') . ' - ' . translate('Signed In Users')) ?>
            </div>
          </td>
        </tr>
      </table>
      <div id="signedin" style="display: <?php echo getShowHide('signedin') ?>">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr class="rowHeaders">
          <td><?php echo translate('User')?></td>
		  <td>Signed In Time</td>
		  <td>Elapsed Time</td>
		  <td width="10%">Sign Out</td>
        </tr>
<?php
	// Write message if they have no reservations
	if (!$users)
		echo '        <tr class="cellColor"><td colspan="7" align="center">No users are signed in</td></tr>';

	for ($i = 0; is_array($users) && $i < count($users); $i++) {
		$rs = $users[$i];
		$class = 'cellColor' . ($i%2);
		echo "        <tr class=\"$class\" align=\"center\">"
					. '          <td>' . $link->getLink("javascript: viewUser('" . $rs['user_id'] . "');",  $rs['first_name'] . ' ' . $rs['last_name'], '', '', 'View information about' . $rs['first_name'] . ' ' . $rs['last_name']) . '</td>'
					. '          <td>' . date('g:ia D, n/d/Y', strtotime($rs['signin'])) . '</td>'
					. '          <td>' . Duration::toString(CmnFns::dateDifference(strtotime($rs['signin']), time())) . '</td>'
					. '          <td>';
		echo $link->doLink($_SERVER['PHP_SELF'].'?signout=1&lab_id=' . $rs['lab_id'] . '&user_id=' . $rs['user_id'] . '&signid=' . $rs['signid'], 'Sign Out', '', '', translate('Sign User Out'));
		echo "					 </td>"
					. "        </tr>\n";
	}
?>
	  </table>
	  </div>
	</td>
  </tr>
</table>
<?php
}
?>