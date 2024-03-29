<?php
/**
* Interface form for placing/modifying/viewing a reservation
* This file will present a form for a user to
*  make a new reservation or modify/delete an old one.
* It will also allow other users to view this reservation.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @version 02-21-05
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
include_once('lib/Equipment.class.php');

global $reservation_type_text;
global $conf;

$is_blackout = filter_input(INPUT_POST, 'is_blackout', FILTER_SANITIZE_STRING);
if (is_null($is_blackout)) {
	$is_blackout = filter_input(INPUT_GET, 'is_blackout', FILTER_SANITIZE_STRING);
}

$is_pending = filter_input(INPUT_POST, 'is_pending', FILTER_SANITIZE_STRING);
if (is_null($is_pending)) {
	$is_pending = filter_input(INPUT_GET, 'is_pending', FILTER_SANITIZE_STRING);
}

$reservation_type_text = $is_blackout ? 'Blackout' : 'Reservation';

$resid = filter_input(INPUT_POST, 'resid', FILTER_SANITIZE_STRING);
if (is_null($resid)) {
	$resid = filter_input(INPUT_GET, 'resid', FILTER_SANITIZE_STRING);
}

$fn = filter_input(INPUT_POST, 'fn', FILTER_SANITIZE_STRING);

$lab_id = filter_input(INPUT_POST, 'lab_id', FILTER_SANITIZE_STRING);
if (is_null($lab_id)) {
	$lab_id = filter_input(INPUT_GET, 'lab_id', FILTER_SANITIZE_STRING);
}

$read_only = filter_input(INPUT_GET, 'read_only', FILTER_SANITIZE_STRING);


if ($is_blackout) {
	// Make sure user is logged in
	if (!Auth::is_logged_in()) {
		Auth::print_login_msg();
	}
	include_once('lib/Blackout.class.php');
	$res = new Blackout($resid, $lab_id);
} else {
	include_once('lib/Reservation.class.php');
	$res = new Reservation($resid, false, $is_pending, $lab_id);
}

if ($read_only && $conf['app']['readOnlyDetails']) {
	// Make sure user is logged in
	if (!Auth::is_logged_in()) {
		Auth::print_login_msg();
	}
}

$t = new Template();

if (strstr($_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF']) && !is_null($fn)) {
	$t->set_title(translate("Processing $reservation_type_text"));
	$t->printHTMLHeader();
	$t->startMain();
	
	process_reservation($res, $fn);
} else {
	$res_info = getResInfo();
	$t->set_title($res_info['title']);
    $t->printHTMLHeader();
    $t->startMain();
    
    present_reservation($res);
}

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();
	
	/**
	 * Processes a reservation request (add/del/edit)
	 * @param $res Reservation Object
	 * @param string $fn function to perform
	 */
function process_reservation(Reservation $res, $fn) {
	$success = false;
	$minRes = 0;        // default min number of minutes a reservation can be.
	$maxRes = 1440;     // default max number of minutes a reservation can be.
	$repeat = array();  // array of timestamps for repeated dates
    $is_pending = filter_input(INPUT_POST, 'pending');
    $resid = filter_input(INPUT_POST, 'resid');
    if (is_null($resid)) {
    	$resid = filter_input(INPUT_GET, 'resid');
    }
    $start_date = filter_input(INPUT_POST, 'start_date');
    $end_date = filter_input(INPUT_POST, 'end_date');
    $start_time = filter_input(INPUT_POST, 'startTime');
    $end_time = filter_input(INPUT_POST, 'endTime');
	$repeat_day = filter_input(INPUT_POST, 'repeat_day');
	$week_number = filter_input(INPUT_POST, 'week_number');
	$repeat_until = filter_input(INPUT_POST, 'repeat_until');
	$interval = filter_input(INPUT_POST, 'interval');
	$frequency = filter_input(INPUT_POST, 'frequency');
	$user_id = filter_input(INPUT_POST, 'user_id');
	$machid = filter_input(INPUT_POST, 'machid');
	$summary = filter_input(INPUT_POST, 'summary');
	$lab_id = filter_input(INPUT_POST, 'lab_id');
	$account_id = filter_input(INPUT_POST, 'account_id');
    $del = filter_input(INPUT_POST, 'del');
    $mod_recur = filter_input(INPUT_POST, 'mod_recur');
	
    if (!is_null($start_date)) {
	    $start_date = strtotime($start_date);
    }
    if (!is_null($end_date)) {
        $end_date = strtotime($end_date);
    }
	
    if (is_null($resid)) {
		// New reservation
		if (!is_null($interval) && $interval !== 'none') {		// Check for reservation repetition
			if ($start_date === $end_date) {
				$res->is_repeat = true;
				$repeat = get_repeat_dates($start_date, $interval, $repeat_day, $repeat_until, $frequency, $week_number);
			} else {
				// Cannot repeat multi-day reservations
				$repeat = array($start_date);
				$res->is_repeat = false;
			}
		} else {
			$repeat = array($start_date);
			$res->is_repeat = false;
		}
	}
	if (is_null($machid)) {
		$machid = $res->get_machid();
	}
	$resource = new Equipment($machid);
	$minRes = filter_input(INPUT_POST, 'minRes');
    $maxRes = filter_input(INPUT_POST, 'maxRes');
	if (is_null($minRes)) {
		$minRes = $resource->get_field('minRes');
	}
	if (is_null($maxRes)) {
		$maxRes = $resource->get_field('maxRes');
	}
	
	$invited_users = (isset($_POST['invited_users'])) ? $_POST['invited_users'] : array();
	$removed_users = (isset($_POST['removed_users'])) ? $_POST['removed_users'] : array();

	if ($fn == RES_TYPE_ADD)
		$res->add_res($machid, $invited_users, $user_id, $start_date, $end_date, $start_time, $end_time, $repeat, $minRes, $maxRes, $summary, $lab_id, $account_id);
	else if ($fn == RES_TYPE_MODIFY)
		$res->mod_res($user_id, $invited_users, $removed_users, $start_date, $end_date, $start_time, $end_time, !is_null($del), $minRes, $maxRes, !is_null($mod_recur), $account_id, str_replace("\n", "", $summary));
	else if ($fn == RES_TYPE_DELETE)
		$res->del_res(!is_null($mod_recur));
	else if ($fn == RES_TYPE_APPROVE) 
		$res->approve_res(!is_null($mod_recur));
	else if ($fn == RES_TYPE_VIEW)
		$res->print_res();
}
	
	/**
	 * Prints out reservation info depending on what parameters
	 *  were passed in through the query string
	 * @param Reservation $res Reservation object
	 */
function present_reservation(Reservation $res) {
	
	if ($res->get_id() === null) {
		$res->mach_id 	    = filter_input(INPUT_GET, 'machid');
		$res->start_date    = filter_input(INPUT_GET, 'start_date');
		$res->end_date      = filter_input(INPUT_GET, 'start_date');
		$res->user_id       = Auth::getCurrentID();
		$res->is_pending    = filter_input(INPUT_GET, 'pending');
		$res->start         = filter_input(INPUT_GET, 'start_time');
		$res->end           = filter_input(INPUT_GET, 'end_time');
	}
	$res->set_type(filter_input(INPUT_GET, 'type'));
	$res->print_res();
}
	
	
	/**
	 * Return array of data from query string about this reservation
	 *  or about a new reservation being created
	 * @param none
	 * @return array
	 */
function getResInfo() {
	$res_info = array();
	global $reservation_type_text;

	// Determine title and set needed variables
	$res_info['type'] = filter_input(INPUT_GET, 'type');
	$res_info['resid'] = filter_input(INPUT_GET, 'resid');
	switch($res_info['type']) {
		case 'reserve' :
			$res_info['title'] = "New $reservation_type_text";
			break;
		case 'modify' :
			$res_info['title'] = "Modify $reservation_type_text";
			break;
		case 'delete' :
			$res_info['title'] = "Delete $reservation_type_text";
			break;
        case 'approve' :
			$res_info['title'] = "Approve $reservation_type_text";
			break;
        case 'sign-in' :
			$res_info['title'] = "Sign In $reservation_type_text";
			break;
        case 'sign-out' :
			$res_info['title'] = "Sign Out $reservation_type_text";
			break;
        default : $res_info['title'] = "View $reservation_type_text";
			break;
	}

	return $res_info;
}

/**
* Returns an array of all timestamps for repeat reservations
* @param string $initial_ts timestamp of first reservation
* @param string $interval interval of reservation recurrances
* @param array $days days of week to repeat on
* @param string $until final date of recurrance
* @param int $frequency frequency of interval
* @param string $week_number week of month number (for reserve by day of month)
* @return array of all timestamps that the reservation is repeated on
*/
function get_repeat_dates($initial_ts, $interval, $days, $until, $frequency, $week_number) {
	$res_dates = array();
	$initial_date = getdate($initial_ts);
	
	list($last_y, $last_m, $last_d) = explode('-', $until);
	$last_ts = mktime(0,0,0,$last_m, $last_d, $last_y);
	$last_date = getdate($last_ts);
	
	$day_of_week = $initial_date['wday'];
	$day_of_month = $initial_date['mday'];
	
	$ts = $initial_ts;
	
	if ($initial_ts > $last_ts)		// Recurring date is in the past
		return array($ts);
	
	switch ($interval) {
		case 'day' :
			for ($i = $frequency; $ts <= $last_ts; $i += $frequency) {
				$res_dates[] = $ts;
				$ts = mktime(0,0,0, $initial_date['mon'], $i + $initial_date['mday'], $initial_date['year']);						
			}
		break;
		case 'week' :
			$additional_days = 0;
			$res_dates[] = $ts;		// Add initial reservation
			
			while ($ts <= $last_ts) {		
				for ($i = 0; $i < count($days); $i++) {					// Repeat for all days selected
					$days_between = ($days[$i] - $day_of_week) + $additional_days;
					// If the day of week is less than reservation day of week, move ahead one week
					if ($days[$i] <= $day_of_week) {
						$days_between += $frequency * 7;
					}
					$ts = mktime(0,0,0,$initial_date['mon'], $initial_date['mday'] + $days_between, $initial_date['year']);
					
					if ($ts <= $last_ts)
						$res_dates[] = $ts;
				}
				$additional_days += $frequency * 7;	// Move ahead week
			}
		break;
		case 'month_date' :
			$next_month = $initial_date['mon'];
			$res_dates[] = $ts;			// Add initial reservation
			
			while ($ts <= $last_ts) {			
				$next_month += $frequency;
				if (date('t',mktime(0,0,0, $next_month, 1, $initial_date['year'])) >= $initial_date['mday']) {		// Make sure month has enough days
					$ts = mktime(0,0,0,$next_month, $initial_date['mday'], $initial_date['year']);
					if ($ts <= $last_ts)
						$res_dates[] = $ts;
				}
			}
		break;
		case 'month_day' :
			$res_dates[] = $ts;		// Add initial reservation
		
			$days_in_month = date('t', mktime(0,0,0, $initial_date['mon'], $initial_date['mday'], $initial_date['year']));
			$next_month = $initial_date['mon'];
			
			// Fill in all months			
			while ($ts <= $last_ts) {
				
				$days_in_month = date('t', mktime(0,0,0, $next_month, 1, $initial_date['year']));
				$first_day_of_month = date('w', mktime(0,0,0, $next_month, 1, $initial_date['year']));
				$last_day_of_month = date('w', mktime(0,0,0, $next_month, $days_in_month, $initial_date['year']));	
			
				if ($week_number != 'last') {
					$offset_date = ($week_number - 1) * 7 + 1; 		// Starting date
					$day_of_week = $first_day_of_month;				// Day of week
				}
				else {
					$offset_date = $days_in_month - 6;
					$day_of_week = $last_day_of_month + 1;
				}
				
				// Repeat on chosen days for this week
				for ($i = 0; $i < count($days); $i++) {					// Repeat for all days selected
					$days_between = ($days[$i] - $day_of_week);
					
					// If the day of week is less than reservation day of week, move ahead one week
					if ($days[$i] < $day_of_week) {
						$days_between += 7;
					}
					
					$current_date = $offset_date + $days_between;
					
					$need_to_add = ( ($current_date <= $days_in_month) && ($next_month > $initial_date['mon'] || ($current_date >= $initial_date['mday'] && $next_month >= $initial_date['mon'])) );
					
					if ($need_to_add)
						$ts = mktime(0,0,0, $next_month, $current_date, $initial_date['year']);
					
					if ( $ts <= $last_ts && $need_to_add && $ts != $initial_ts)// && ($current_date <= $days_in_month) && ($current_date >= $initial_date['mday'] && $next_month >= $initial_date['mon']) )
						$res_dates[] = $ts;
				}
					
				$next_month += $frequency;
			}	
		break;
	}
	return $res_dates;
}
?>