<?php
	/**
	 * MyCalendar class
	 * This file contians the API functions for displaying
	 *  reservation data in a particular format for a user
	 * @author Nick Korbel <lqqkout13@users.sourceforge.net>
	 * @author Richard Cantzler <rmcii@users.sourceforge.net>
	 * @version 07-07-05
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
	 * Include MyCalendarDB class
	 */
	include_once('db/MyCalendarDB.class.php');
	/**
	 * Include Calendar class
	 */
	include_once('Calendar.class.php');
	/**
	 * Include MyCalendar template files
	 */
	include_once(BASE_DIR . '/templates/mycalendar.template.php');
	
	class MyCalendar {
		protected $db;
		public $user_id;
		public $type;
		public $actual_date;
		public $first_date;
		public $last_date;
		public $date_vars;
		public $total_days;
		public $reservations;
		
		/**
		 * Sets up initial variable values
		 * @param string $user_id id of the calendar user
		 * @param null $type
		 * @param null $actual_date todays date
		 * @param bool $load_reservations
		 */
		function __construct($user_id, $type = null, $actual_date = null, $load_reservations = true) {
			global $auth;
			
			$this->user_id = ($user_id == null) ? $auth->getCurrentID() : $user_id;
			$this->type = ($type == null) ? MYCALENDARTYPE_DAY : $type;
			
			$this->actual_date = $actual_date;
			$this->determineFirstDate();
			$this->initDateVars();
			
			if ($load_reservations) {
				$this->db = new MyCalendarDB();
				$this->loadReservations();
			}
		}
		
		/**
		 * Calls the appropriate function to load the reservations fitting this calendar data
		 * @param none
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
			
			$this->reservations = $this->db->get_all_reservations($firstResDate, $lastResDate, $this->user_id);
		}
		
		/**
		 * Prints the given calendar out based on type
		 * @param none
		 */
		function printCalendar() {
			//$this->print_calendars("changeMyCal(%d,%d,%d,{$this->type})");
			
			printDateSpan($this->first_date, $this->last_date, $this->type);
			printViewLinks($this->actual_date, $this->type);
			
			switch ($this->type) {
				case MYCALENDARTYPE_DAY :
				case MYCALENDARTYPE_WEEK :
					printDayReservations($this->reservations, $this->first_date, $this->total_days);
					break;
				case MYCALENDARTYPE_MONTH :
					printMonthReservations($this->reservations, $this->first_date);
			}
			
			printDetailsDiv();
		}
		
		/**
		 * Create previous/current/next month calendars and print the proper ones
		 * @param string $javascript javascript string to assign to the calendars
		 */
		function print_calendars($javascript) {
			list($month, $year) = explode('-', date('m-Y', $this->actual_date));
			$prev = new Calendar(false, $month -1, $year);
			$curr = new Calendar(false, $month, $year);
			$next = new Calendar(false, $month + 1, $year);
			
			$prev->javascript = $curr->javascript = $next->javascript = $javascript;
			
			if ($this->type == MYCALENDARTYPE_MONTH) { $curr = null; }	// No need to print out the current month if we are in month view
			
			printMycalendars($prev, $next, $curr);
		}
		
		/**
		 * Determines the first date of the calender based on values passed in the querystring
		 * @return datestamp of the first date to print out
		 */
		function determineFirstDate() {
			$temp_date = null;
			$first_date = null;
			
			$date_split = explode('-', date('m-d-Y', $this->actual_date));
			
			if ($this->type == MYCALENDARTYPE_MONTH) { $date_split[1] = 1; } // For month view, we need to set the first day
			
			$temp_date = mktime(0,0,0, $date_split[0], $date_split[1], $date_split[2]);	// Store the calculated first date
			
			if ($this->type == MYCALENDARTYPE_WEEK) {
				$day_of_week = date('w', $temp_date);		// For the week view, we want to set it to always start on Sunday
				$first_date = mktime(0,0,0, $date_split[0], $date_split[1]-$day_of_week, $date_split[2]);
			}
			else {
				$first_date = $temp_date;
			}
			
			$this->first_date = $first_date;
		}
		
		/**
		 * Initialize all date variables for start/end dates
		 */
		function initDateVars() {
			
			$first_date = getdate($this->first_date);		// Array of all first date info
			
			if ($this->type == MYCALENDARTYPE_WEEK) {
				$this->total_days = 7;
				$this->last_date = mktime(0,0,0, $first_date['mon'], $first_date['mday'] + $this->total_days - 1, $first_date['year']);
			}
			else if ($this->type == MYCALENDARTYPE_MONTH) {
				$this->total_days = date('t', $this->first_date);
				$this->last_date = mktime(0,0,0, $first_date['mon'], $first_date['mday'] + $this->total_days - 1, $first_date['year']);
			}
			else {
				$this->total_days = 1;
				$this->last_date = $this->first_date;
			}
			
			$last_date = getdate($this->last_date);
			
			$this->date_vars['first_date'] = $first_date;
			$this->date_vars['last_date']  = $last_date;
		}
	}
