<?php
/**
* Provide all of the presentation functions for the ResCalendar class
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 08-18-05
* @package Templates
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/

// Get Link object
$link = CmnFns::getNewLink();

/**
* Prints out a jump box for all of the resources
* @param $resources array list of resources
* @param $labs array list of labs
* @param $machid string id of the currently viewed resource
* @param $datestamp int the datestamp of the currently viewed date
* @param $type int calendar view type
* @param $isresource bool if this is a resource view
*/
function print_equipment_jump_link($resources, $labs, $machid, $lab_id, $datestamp, $type, $isresource) {
	global $link;
	$machCount = 0;	
	$date_string = date('m,j,Y', $datestamp);
	
	echo "<p align=\"center\"><select name=\"equipment_select\" class=\"textbox\" onchange=\"javascript:changeResCalendar($date_string, $type, this.options[this.selectedIndex].value);\">";
	for ($lab = 0; $lab < count($labs); $lab++) {
		echo "<option value=\"s|{$labs[$lab]['lab_id']}\"";
		if ($labs[$lab]['lab_id'] == $lab_id) { echo ' selected="selected" '; }
		echo ">{$labs[$lab]['labTitle']}</option>\n";
		for (; $machCount < count($resources); $machCount++) {
			echo "<option value=\"m|{$resources[$machCount]['machid']}\"";
			if ($resources[$machCount]['machid'] == $machid) { echo ' selected="selected" '; }
			echo ">&nbsp;&nbsp;{$resources[$machCount]['name']}</option>\n";
			if (isset($resources[$machCount+1]) && $resources[$machCount+1]['lab_id'] != $labs[$lab]['lab_id']) { $machCount++; break; }
		}
	}
	echo '</select> ';
	if ($type == MYCALENDARTYPE_DAY && $isresource) {
		$link_string = "javascript:window.open('signup.php?view=%d&amp;date=%s&amp;machid=%s','signup','height=700,width=600,toolbar=yes,menubar=yes,scrollbars=auto,resizable=yes');void(0);";
		$link->doImageLink(sprintf($link_string, MYCALENDARTYPE_SIGNUP, date('m-d-Y',$datestamp), $machid), 'img/signup.gif', translate('Signup View'));
	}
	echo '</p>'; 
}

/**
* Prints all reservations for a given day
* @param array $reservations array of all reservation data for this day
* @param int $datestamp the unix datestamp for the first day shown
* @param int $days number of days to print out
* @param string $lab_id id of the this resource's lab
* @param int $start_time starting time of the day for this reservation's lab
* @param int $end_time ending time of the day for this reservation's lab
* @param int $time_span the time span interval for this reservation's lab
* @param bool $is_private if we are in privacy mode and should hide user details
*/
function print_day_equipment_reservations($reservations, $datestamp, $days, $lab_id, $start_time, $end_time, $time_span, $is_private = false) {
	echo "<table border=\"0\" style:\"table-layout:fixed\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"tableBorder\">\n<table border=\"0\" width=\"100%\" cellspacing=\"1\" cellpadding=\"0\">\n";
	$date_vars = getdate($datestamp);
	$col_width = intval(100/($days));
	$seconds_in_day = 86400;
	$hour_line = array();
	$date_cells_taken = array();
	
	$datestamps = array();		// This will store the datestamp for each date on the calendar
	// Print out a date header for each date in the calendar view
	echo '<tr><td class="labDate">&nbsp;</td>';
	for ($day_count = 0; $day_count < $days; $day_count++) {
		$datestamps[$day_count] = mktime(0,0,0, $date_vars['mon'], $date_vars['mday'] + $day_count, $date_vars['year']);
		echo '<td width="' . $col_width . '%" class="labDate"><a href="rescalendar.php?lab_id=' . $lab_id . '&amp;date=' . sprintf('%d-%d-%d', $date_vars['mon'], $date_vars['mday'], $date_vars['year']) . '">' . translate_date('lab_daily', $datestamps[$day_count]) . '</a></td>';
	}
	echo "</tr>\n";
	
	for ($i = 0; $i < count($reservations); $i++) {	
		// If the reservation starts on a day other than the first day shown then just show it at the start time of the first day
		$day = ($reservations[$i]['start_date'] >= $datestamp) ? ($reservations[$i]['start_date'] - $datestamp)/$seconds_in_day : 0;	// This will tell how many days ahead of the first day this reservation occurs
		// If the reseravtion ends on a day further from the last day shown, then make the endday equal to the last day
		$endday = ($reservations[$i]['end_date'] <= $datestamps[$days-1]) ? ($reservations[$i]['end_date'] - $datestamp)/$seconds_in_day : $days-1;	// This will tell how many days ahead of the first day this reservation occurs		
		// Get temporary start and end times for dates that are off the viewable days
		$starttime = ($reservations[$i]['start_date'] >= $datestamp) ? ($reservations[$i]['startTime']) : $start_time;
		$endtime = ($reservations[$i]['end_date'] <= $datestamps[$days-1]) ? ($reservations[$i]['endTime']) : $end_time;
		
		$hour_line[$starttime][$day] = &$reservations[$i];
		
		// If this is a multi day reservation, make sure we populate the $hour_line of the last day/time for this reservation		
		if ($day != $endday) {
			for ($d = $day+1; $d <= $endday; $d++) {
				if ($datestamps[$d] == $reservations[$i]['end_date']) {
					// If this is the last day of the reservation, we need to make sure that the end time is late enough to appear on the calendar
					if ($reservations[$i]['endTime'] > $start_time) {
						$hour_line[$start_time][$d] = &$reservations[$i];
					}	
				}
				else {
					$hour_line[$start_time][$d] = &$reservations[$i];
				}
			}
		}

		// Keep an array of the cells that are taken by the rowspan of another reservation
		if ($day != $endday) {
			// MULTIDAY
			for ($d = $day; $d <= $endday; $d++) {
				if ($d == $day) {
					for ($time = $starttime; $time < $end_time; $time += $time_span) {		
						$date_cells_taken[$d][$time] = 1;
					}
				}
				else if ($d == $endday) {
					for ($time = $start_time; $time < $endtime; $time += $time_span) {		
						$date_cells_taken[$d][$time] = 1;
					}
				}
				else {
					for ($time = $start_time; $time < $end_time; $time += $time_span) {		
						$date_cells_taken[$d][$time] = 1;
					}
				}
			}
		}	
		else {
			// SINGLE DAY
			for ($time = $starttime; $time < $endtime; $time += $time_span) {		
				$date_cells_taken[$day][$time] = 1;
			}
		}	
	}
	
	// The reservation data is stored in a 2D array of time (x axis) and date (y axis)
	// This simply loops through all time/date possibilities and prints out the reservation data for each cell
	for ($time = $start_time; $time < $end_time; $time += $time_span) {
		echo '<tr><td valign="top" class="resourceName">' . CmnFns::formatTime($time) . '</td>';
		for ($date = 0; $date < $days; $date++) {
			if (isset($hour_line[$time][$date])) {
				$res = $hour_line[$time][$date];
				
				if ($is_private) {
					$res['first_name'] = 'Private';
					$res['last_name'] = '';
				}
				
				$starttime = $res['startTime'];
				$endtime = $res['endTime'];
				// Set temporary start/end times for multiday reservations so that the rowspan is correct
				if ($res['start_date'] != $res['end_date']) {
					if ($res['start_date'] != $datestamps[$date]) {
						// If the res starts on a day other than today, then make the temp starting time equal to the day start
						$starttime = $start_time;
					}						
					if ($res['end_date'] != $datestamps[$date]) {
						// If the res ends on a day other than today, then make the temp ending time equal to the day end
						$endtime = $end_time;
					}
				}
				$rowspan = intval(($endtime - $starttime)/$time_span);
				$js = "onmouseover=\"showSummary('details', event, '" . build_reservation_detail_div($res) . "');\" onmouseout=\"hideSummary('details');\" onmousemove=\"moveSummary('details', event);\"";	
				echo "<td valign=\"top\" class=\"MyCalCellColor\" rowspan=\"$rowspan\" $js>&#8226; ";
				echo "<a href=\"javascript:reserve('" . RES_TYPE_MODIFY . "','','','{$res['resid']}','{$res['lab_id']}');\">{$res['first_name']} {$res['last_name']}</a>";
				//if (isset($res['parentid'])) echo ' <img src="img/recurring.gif" width="15" height="15" alt="' . translate('Recurring') . '" title="' . translate('Recurring') . '"/>';
				if ($res['start_date'] != $res['end_date']) echo ' <img src="img/multiday.gif" width="8" height="9" alt="' . translate('Multiple Day') . '" title="' . translate('Multiple Day') . '"/>';					
				echo '</td>';
			}
			else {
				if (!isset($date_cells_taken[$date][$time])) {
					echo '<td valign="top" class="MyCalCellColorEmpty">&nbsp;</td>';	// There is no reservation for this time, print out an empty cell
				}
			}
		}
		echo "</tr>\n";			// End the time row
	}	
	echo "</table>\n</td></tr><table>\n";
}

/**
* Prints all reservations for a given day
* @param array $reservations array of all reservation data for this day
* @param int $datestamp the unix datestamp for the first day shown
* @param int $days number of days to print out
* @param int $start_time starting time of the day for this reservation's lab
* @param int $end_time ending time of the day for this reservation's lab
* @param int $time_span the time span interval for this reservation's lab
* @param string $equipment_name the name of this resource
* @param bool $is_private if we are in privacy mode and should hide user details
*/
function print_signup_sheet($reservations, $datestamp, $days, $start_time, $end_time, $time_span, $equipment_name, $is_private = false) {
	echo "<table border=\"0\" style:\"table-layout:fixed\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"background-color:#ffffff;\">\n<table border=\"1\" bordercolor=\"#000000\" width=\"100%\" cellspacing=\"1\" cellpadding=\"3\">\n";
	$date_vars = getdate($datestamp);
	$col_width = intval(100/($days));
	$seconds_in_day = 86400;
	$hour_line = array();
	$date_cells_taken = array();
	
	$datestamps = array();		// This will store the datestamp for each date on the calendar
	// Print out a date header for each date in the calendar view
	echo '<tr><td>&nbsp;</td>';
	for ($day_count = 0; $day_count < $days; $day_count++) {
		$datestamps[$day_count] = mktime(0,0,0, $date_vars['mon'], $date_vars['mday'] + $day_count, $date_vars['year']);
		echo '<td width="' . $col_width . '%" align="center"><b>' . $equipment_name . '</b><br/>' . translate_date('lab_daily', $datestamps[$day_count]) . '</td>';
	}
	echo "</tr>\n";
	
	for ($i = 0; $i < count($reservations); $i++) {	
		// If the reservation starts on a day other than the first day shown then just show it at the start time of the first day
		$day = ($reservations[$i]['start_date'] >= $datestamp) ? ($reservations[$i]['start_date'] - $datestamp)/$seconds_in_day : 0;	// This will tell how many days ahead of the first day this reservation occurs
		// If the reseravtion ends on a day further from the last day shown, then make the endday equal to the last day
		$endday = ($reservations[$i]['end_date'] <= $datestamps[$days-1]) ? ($reservations[$i]['end_date'] - $datestamp)/$seconds_in_day : $days-1;	// This will tell how many days ahead of the first day this reservation occurs		
		// Get temporary start and end times for dates that are off the viewable days
		$starttime = ($reservations[$i]['start_date'] >= $datestamp) ? ($reservations[$i]['startTime']) : $start_time;
		$endtime = ($reservations[$i]['end_date'] <= $datestamps[$days-1]) ? ($reservations[$i]['endTime']) : $end_time;
		
		$hour_line[$starttime][$day] = &$reservations[$i];
		
		// If this is a multi day reservation, make sure we populate the $hour_line of the last day/time for this reservation		
		if ($day != $endday) {
			for ($d = $day+1; $d <= $endday; $d++) {
				$hour_line[$start_time][$d] = &$reservations[$i];
			}
		}
		
		// Keep an array of the cells that are taken by the rowspan of another reservation
		if ($day != $endday) {
			// MULTIDAY
			for ($d = $day; $d <= $endday; $d++) {
				if ($d == $day) {
					for ($time = $starttime; $time < $end_time; $time += $time_span) {		
						$date_cells_taken[$d][$time] = 1;
					}
				}
				else if ($d == $endday) {
					for ($time = $start_time; $time < $endtime; $time += $time_span) {		
						$date_cells_taken[$d][$time] = 1;
					}
				}
				else {
					for ($time = $start_time; $time < $end_time; $time += $time_span) {		
						$date_cells_taken[$d][$time] = 1;
					}
				}
			}
		}	
		else {
			// SINGLE DAY
			for ($time = $starttime; $time < $endtime; $time += $time_span) {		
				$date_cells_taken[$day][$time] = 1;
			}
		}	
	}
	
	// The reservation data is stored in a 2D array of time (x axis) and date (y axis)
	// This simply loops through all time/date possibilities and prints out the reservation data for each cell
	for ($time = $start_time; $time < $end_time; $time += $time_span) {
		echo '<tr><td valign="top">' . CmnFns::formatTime($time) . '</td>';
		for ($date = 0; $date < $days; $date++) {
			if (isset($hour_line[$time][$date])) {
				$res = $hour_line[$time][$date];
				
				if ($is_private) {
					$res['first_name'] = 'Private';
					$res['last_name'] = '';
				}
				
				$starttime = $res['startTime'];
				$endtime = $res['endTime'];
				// Set temporary start/end times for multiday reservations so that the rowspan is correct
				if ($res['start_date'] != $res['end_date']) {
					if ($res['start_date'] == $datestamps[$date]) {						
						$endtime = $end_time;
					}
					else {
						$starttime = $start_time;						 
					}
				}
				$rowspan = intval(($endtime - $starttime)/$time_span);
				echo "<td valign=\"top\" rowspan=\"$rowspan\" class=\"\">&#8226; ";
				echo "{$res['first_name']} {$res['last_name']}";
				//if (isset($res['parentid'])) echo ' <img src="img/recurring.gif" width="15" height="15" alt="' . translate('Recurring') . '" title="' . translate('Recurring') . '"/>';
				if ($res['start_date'] != $res['end_date']) echo ' <img src="img/multiday.gif" width="8" height="9" alt="' . translate('Multiple Day') . '" title="' . translate('Multiple Day') . '"/>';					
				echo '</td>';
			}
			else {
				if (!isset($date_cells_taken[$date][$time])) {
					echo '<td valign="top">&nbsp;</td>';	// There is no reservation for this time, print out an empty cell
				}
			}
		}
		echo "</tr>\n";			// End the time row
	}	
	echo "</table>\n</td></tr><table>\n";
}
?>