<?php
/**
* MyCalendar class
* This file contians the API functions for displaying
*  reservation data in a particular format for a resource
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author Richard Cantzler <rmcii@users.sourceforge.net>
* @version 11-18-05
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Base directory of application
*/
//@define('BASE_DIR', dirname(__FILE__) . '/..');
/**
* Include MyCalendar class
*/
include_once('MyCalendar.class.php');
/**
* Include ResCalendarDB class
*/
include_once('db/ResCalendarDB.class.php');
/**
* Include ResCalendar template files
*/
include_once(BASE_DIR . '/templates/rescalendar.template.php');

class ResCalendar extends MyCalendar {
	var $mach_id;
	var $lab_id;
	var $type;
	var $resources;
	var $labs;
	
	var $start_time;
	var $end_time;
	var $time_span;
	
	var $name;
	var $is_resource;
	
	/**
	 * Sets up initial variable values
	 * @param null $user_id id of the calendar user
	 * @param null $type
	 * @param null $actual_date todays date
	 * @param null $mach_id
	 * @param null $lab_id
	 */
	function __construct($user_id = null, $type = null, $actual_date = null, $mach_id = null, $lab_id = null) {
		$this->mach_id = $mach_id;
		$this->lab_id = $lab_id;

		$this->db = new ResCalendarDB();
		parent::__construct($user_id, $type, $actual_date, false);
		
		$this->resources = $this->db->get_resources($user_id, $lab_id);		// Used to provide a pull down to change resources
		$this->labs = $this->db->get_lab_list();		// Used in resource pull down and to determine start/end/interval times
		
		if ($this->mach_id == null && $this->lab_id == null) {
			$this->lab_id = $this->resources[0]['lab_id'];
		}
		$this->is_resource = ($this->lab_id == null);
		
		if ($this->is_resource && $this->mach_id == null) {
			$this->mach_id = $this->resources[0]['machid'];	// If we dont have a machid from the querystring, take the first one in the list
			$this->lab_id = $this->resources[0]['lab_id'];
		}
		else if (!$this->is_resource && $this->lab_id == null) {
			$this->lab_id = $this->labs[0]['lab_id'];
		}
		else if ($this->is_resource && $this->mach_id != null) {
			// Set the lab_id for this machid
			for ($i = 0; $i < count($this->resources); $i++) {
				if ($this->resources[$i]['machid'] == $this->mach_id) {
					$this->lab_id = $this->resources[$i]['lab_id'];
					$this->name = $this->resources[$i]['name'];
					break;
				}
			}
		}
		else if (!$this->is_resource && $this->lab_id != null) {
			for ($i = 0; $i < count($this->labs); $i++) {
				if ($this->labs[$i]['lab_id'] == $this->lab_id) {
					$this->name = $this->labs[$i]['labTitle'];
					break;
				}
			}
		}
		
		if ($this->type !=  MYCALENDARTYPE_MONTH) {
			// Set the lab properties (only needed for the day/week views
			for ($i = 0; $i < count($this->labs); $i++) {
				if ($this->labs[$i]['lab_id'] == $this->lab_id) {
					$this->start_time = $this->labs[$i]['dayStart'];
					$this->end_time   = $this->labs[$i]['dayEnd'];
					$this->time_span  = $this->labs[$i]['timeSpan'];
					break;
				}
			}
		}
		
		$this->loadReservations();
	}
	
	/**
	* Calls the appropriate function to load the reservations fitting this calendar data
	*/
	function loadReservations() {
		global $conf;
		$firstResDate = $this->first_date;
		$lastResDate = $this->last_date;
		if ($this->type == MYCALENDARTYPE_MONTH) {
			$datestamp = $this->first_date;
			$date_vars = explode(' ',date('d m Y t w W',$datestamp));
			$last_month_num_days = date('t', mktime(0,0,0, $date_vars[1]-1, $date_vars[0], $date_vars[2]));
			$week_start = $conf['app']['calFirstDay'];
			$firstWeekDay = (7 + (date('w', $datestamp) - $week_start)) % 7;
			$lastWeekDay = date('w',$this->last_date) + 1;
			$firstResDate = mktime(0,0,0, $date_vars[1]-1, ($last_month_num_days - $firstWeekDay), $date_vars[2]);
			$lastResDate = mktime(0,0,0, $date_vars[1]+1, (7 + $week_start - $lastWeekDay) % 7, $date_vars[2]);
		}
		
		$this->reservations = $this->db->get_all_reservations($firstResDate, $lastResDate, (($this->is_resource) ? $this->mach_id : $this->lab_id), $this->is_resource);
	}
	
	/**
	 * Prints the given calendar out based on type
	 * @param bool $isAdminCpanel
	 * @param bool $print_view
	 */
	function printCalendar($isAdminCpanel=false, $print_view=false) {
		global $conf;
		
		$is_private = $conf['app']['privacyMode'] && !Auth::isAdmin();
		
		if ($this->type != MYCALENDARTYPE_SIGNUP) {
			$param_name = $this->is_resource ? 'machid' : 'lab_id';
			$param_value = $this->is_resource ? $this->mach_id : $this->lab_id;
			$prefix = $this->is_resource ? 'm' : 's';
			printDateSpan($this->first_date, $this->last_date, $this->type, array($param_name), array($param_value), $this->name);
			
			if ($print_view!=="1") {
				printViewLinks($this->actual_date, $this->type, array($param_name), array($param_value));
			}
			echo "<br>";
			print_equipment_jump_link($this->resources, $this->labs, $this->mach_id, $this->lab_id, $this->actual_date, $this->type, $this->is_resource);
			echo "<br>";
		
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER[HTTP_HOST].str_replace('&print_view=1', '', $_SERVER[REQUEST_URI]);
			if ($print_view==="1" && $this->type === MYCALENDARTYPE_DAY) {
				echo "<a href='".$actual_link."'>Web View</a>";
			} else if ($this->type === MYCALENDARTYPE_DAY) {
				echo "<a href='" . $actual_link . "&print_view=1'>Print View</a>";
			}

			switch ($this->type) {
				case MYCALENDARTYPE_DAY :
				case MYCALENDARTYPE_WEEK :
					if ($this->is_resource) {
						print_day_equipment_reservations($this->reservations, $this->first_date, $this->total_days, $this->lab_id, $this->start_time, $this->end_time, $this->time_span, $is_private);
					}
					else {
						printDayReservations($this->reservations, $this->first_date, $this->total_days, false, $is_private);
					}
					break;
				case MYCALENDARTYPE_MONTH :
					printMonthReservations($this->reservations, $this->first_date, array('first_name', 'last_name'), false, $is_private);
			}
			
			printDetailsDiv();
		}
		else {
			print_signup_sheet($this->reservations, $this->first_date, 1, $this->start_time, $this->end_time, $this->time_span, $this->name, $is_private);
		}
	}
}
?>