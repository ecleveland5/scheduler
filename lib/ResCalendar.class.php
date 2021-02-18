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
@define('BASE_DIR', dirname(__FILE__) . '/..');
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
	var $machid;
	var $lab_id;
	var $type;
	var $resources;
	var $labs;
	
	var $start_time;
	var $end_time;
	var $time_span;
	
	var $name;
	var $isresource;
	
	/**
	* Sets up initial variable values
	* @param string $userid id of the calendar user
	* @param int MyCalendarType type for this calendar
	* @param int $actualDate todays date
	*/
	function __construct($userid = null, $type = null, $actualDate = null, $machid = null, $lab_id = null) {
		$this->machid = $machid;
		$this->lab_id = $lab_id;

		$this->db = new ResCalendarDB();
		parent::__construct($userid, $type, $actualDate, false);
		
		$this->resources = $this->db->get_resources($userid, $lab_id);		// Used to provide a pull down to change resources
		$this->labs = $this->db->get_lab_list();		// Used in resource pull down and to determine start/end/interval times
		
		if ($this->machid == null && $this->lab_id == null) {
			$this->lab_id = $this->resources[0]['lab_id'];
		}
		$this->isresource = ($this->lab_id == null);
		
		if ($this->isresource && $this->machid == null) {
			$this->machid = $this->resources[0]['machid'];	// If we dont have a machid from the querystring, take the first one in the list
			$this->lab_id = $this->resources[0]['lab_id'];
		}
		else if (!$this->isresource && $this->lab_id == null) {
			$this->lab_id = $this->labs[0]['lab_id'];
		}
		else if ($this->isresource && $this->machid != null) {
			// Set the lab_id for this machid
			for ($i = 0; $i < count($this->resources); $i++) {
				if ($this->resources[$i]['machid'] == $this->machid) {
					$this->lab_id = $this->resources[$i]['lab_id'];
					$this->name = $this->resources[$i]['name'];
					break;
				}
			}
		}
		else if (!$this->isresource && $this->lab_id != null) {
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
		
		$this->load_reservations();
	}
	
	/**
	* Calls the appropriate function to load the reservations fitting this calendar data
	*/
	function load_reservations() {		
		global $conf;
		$firstResDate = $this->firstDate;
		$lastResDate = $this->lastDate;
		if ($this->type == MYCALENDARTYPE_MONTH) {
			$datestamp = $this->firstDate;
			$date_vars = explode(' ',date('d m Y t w W',$datestamp));
			$last_month_num_days = date('t', mktime(0,0,0, $date_vars[1]-1, $date_vars[0], $date_vars[2]));
			$week_start = $conf['app']['calFirstDay'];
			$firstWeekDay = (7 + (date('w', $datestamp) - $week_start)) % 7;
			$lastWeekDay = date('w',$this->lastDate) + 1;
			$firstResDate = mktime(0,0,0, $date_vars[1]-1, ($last_month_num_days - $firstWeekDay), $date_vars[2]);
			$lastResDate = mktime(0,0,0, $date_vars[1]+1, (7 + $week_start - $lastWeekDay) % 7, $date_vars[2]);
		}
		
		$this->reservations = $this->db->get_all_reservations($firstResDate, $lastResDate, (($this->isresource) ? $this->machid : $this->lab_id), $this->isresource);	
	}
	
	/**
	* Prints the given calendar out based on type
	*/
	function print_calendar($isAdminCpanel=false, $print_view=false) {
		global $conf;
		
		$is_private = $conf['app']['privacyMode'] && !Auth::isAdmin();
		
		if ($this->type != MYCALENDARTYPE_SIGNUP) {
			$paramname = $this->isresource ? 'machid' : 'lab_id';
			$paramvalue = $this->isresource ? $this->machid : $this->lab_id;
			$prefix = $this->isresource ? 'm' : 's';
			print_date_span($this->firstDate, $this->lastDate, $this->type, array($paramname), array($paramvalue), $this->name);
			
			if ($print_view!=="1") {
				print_view_links($this->actualDate, $this->type, array($paramname), array($paramvalue));
			}
			echo "<br>";
			print_equipment_jump_link($this->resources, $this->labs, $this->machid, $this->lab_id, $this->actualDate, $this->type, $this->isresource);
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
					if ($this->isresource) {
						print_day_equipment_reservations($this->reservations, $this->firstDate, $this->totalDays, $this->lab_id, $this->start_time, $this->end_time, $this->time_span, $is_private);
					}
					else {
						print_day_reservations($this->reservations, $this->firstDate, $this->totalDays, false, $is_private);
					}
					break;
				case MYCALENDARTYPE_MONTH :
					print_month_reservations($this->reservations, $this->firstDate, array('first_name', 'last_name'), false, $is_private);
			}
			
			print_details_div();
		}
		else {
			print_signup_sheet($this->reservations, $this->firstDate, 1, $this->start_time, $this->end_time, $this->time_span, $this->name, $is_private);		
		}
	}
}
?>