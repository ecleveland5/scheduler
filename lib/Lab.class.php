<?php
/**
* Scheduler class
* This file contians the scheduler application where
*  users have an interface for reserving resources,
*  viewing other reservations and modifying their own.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Richard Cantzler <rmcii@users.sourceforge.net>
* @version 08-18-05
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
* Include LabDB class
*/
include_once('db/LabDB.class.php');
/**
* Include Calendar
*/
include_once('Calendar.class.php');
/**
* Include Lab template files
*/
include_once(BASE_DIR . '/templates/lab.template.php');

class Lab {

    var $_date;
    var $machids;
    var $res;
    var $blackouts;
    var $db;
    var $user;
    var $labType; // BLACKOUT_ONLY, RESERVATION_ONLY, ALL, READ_ONLY
    var $lab_id;
    var $viewDays;
    var $startDay;
    var $endDay;
    var $timeSpan;
    var $weekStartDay;
    var $showSummary;
    var $dayOffset;
    var $title;
    var $admin;
    var $isValid = false;

    /**
     * Sets up initial variable values
     * @param string $lab_id ID of the lab
     * @param int $labType type of lab to print out
     * @param User $user current logged in user object
     */
    function __construct($lab_id, $labType = ALL) {
        $this->lab_id = $lab_id;
        $this->labType = $labType;                // Set lab type
        $this->db = new LabDB($lab_id, $labType);            // Set database class

        if ($lab_id === null) {
            $this->lab_id = $this->db->get_default_id();
            $this->db->lab_id = $this->lab_id;
        }

        $this->isValid = $this->db->check_lab_id($this->lab_id);

        if ($this->isValid) {
            $data = $this->db->get_lab_data($this->lab_id);
            $this->viewDays = $data['viewDays'];
            $this->startDay = $data['dayStart'];
            $this->endDay   = $data['dayEnd'];
            $this->timeSpan = $data['timeSpan'];
            $this->weekDayStart = $data['weekDayStart'];
            $this->showSummary	= $data['showSummary'];
            $this->title    = $data['labTitle'];
            $this->admin    = $data['adminEmail'];
            $this->dayOffset= $data['dayOffset'];

            if ($labType == READ_ONLY)
                $this->user = new User();
            else
                $this->user = new User(Auth::getCurrentID());    // Set User class

            $this->_date = $this->get_date_vars();        // Get all date info we need
            $this->machids = $this->db->get_mach_ids($this->lab_id);    // Get all resource info
            $machids = array();
            if($this->machids !== false) {
                foreach($this->machids as $mach) {
                    $machids[] = $mach['machid'];
                }
            }
            $this->res = $this->db->get_all_res($this->_date['firstDayTs'], $this->_date['lastDayTs'], $machids);
        }
    }

    /**
     * Prints the actual lab by calling all necessary class
     *  and lab template functions
     * @param array $filters list of resources to display
     * @param string $user_id ID of the current user
     */
    function print_lab($filters) {
        global $conf;

        print_date_span($this->_date, $this->title);

        //print_jump_links($this->_date);

        print_lab_list($this->db->get_lab_list(), $this->lab_id);
        //var_dump($filters);
        //print_filter_resources($this->machids, $filters, $this->user->get_id());
        //$this->print_calendars();

        if ($this->labType == ALL)
            print_color_key();

        // Break first day we are viewing into an array of date pieces
        $temp_date = getdate($this->_date['firstDayTs']);
        $hour_header = get_hour_header($this->get_time_array(), $this->startDay, $this->endDay, $this->timeSpan);    // Get the headers (same for all tables)

        // Repeat this for each day we need to show
        for ($dayCount = 0; $dayCount < $this->viewDays; $dayCount++) {
            // Timestamp for whatever day we are currently viewing
            $this->_date['current'] = mktime(0,0,0, $temp_date['mon'], $temp_date['mday'] + $dayCount, $temp_date['year']);
            start_day_table($this->get_display_date(), $hour_header);    // Start the table for this day
            $this->print_reservations($filters);    // Print reservations for this day
            end_day_table();                // End the table for this day
        }
        print_summary_div();

    }

    /**
    * Prints out 3 calendars (prev month, this month, next month) at the top of the lab
    * @param none
    */
    function print_calendars() {
        $prev = new Calendar(false, $this->_date['month'] -1, $this->_date['year']);
        $curr = new Calendar(false, $this->_date['month'], $this->_date['year']);
        $next = new Calendar(false, $this->_date['month'] + 1, $this->_date['year']);
        $prev->lab_id = $curr->lab_id = $next->lab_id = $this->lab_id;

        print_calendars($prev, $curr, $next);
    }

    /**
    * Print out the reservations for each resource on each day
    * @param array $filters array of resource ids to show
    */
    function print_reservations(array $filters = array()) {
        global $conf;

        if (!$this->machids)
            return;

        $current_date = $this->_date['current'];        // Store current_date so we dont have to access the array every time
        //var_dump($this->res);
        // Repeat this whole process for each resource
        for ($count = 0; $count < count($this->machids); $count++) {
            $prevTime = $this->startDay;        // Previous time holder
            $totCol = intval(($this->endDay - $this->startDay) / $this->timeSpan);    // Total columns holder

            // Store info about this current resource in local vars
            $id = $this->machids[$count]['machid'];
            $name = $this->machids[$count]['name'];
            $status = $this->machids[$count]['status'];
            $approval = $this->machids[$count]['approval'];
            $show = false;        // Default resource visibility to not shown

            // If the date has not passed, resource is active and user has permission,
            //  or the user is the admin allow reservations to be made
            if (($current_date >= mktime(0,0,0, date('m'), date('d') + $this->dayOffset) && ($status == 'a' && $this->user->has_perm($id)))
                || Auth::isAdmin()
            ) {
                $show = true;
            }
                // insert code to limit visibility of resources not authorized
            $color = 'cellColor' . ($count%2);

            if (empty($filters) || array_key_exists($this->machids[$count]['machid'], $filters)) {
                $this->machids[$count]['hide'] = false;
            } else {
                $this->machids[$count]['hide'] = true;
            }

            print_name_cell($current_date, $id, $name, $show, $this->labType == BLACKOUT_ONLY, $this->lab_id, $approval, $color, $this->machids[$count]['hide']);

            $index = $id;
            if (isset($this->res[$index])) {
                for ($i = 0; $i < count($this->res[$index]); $i++) {
                    $rs = $this->res[$index][$i];
                    //var_dump($rs);
                    // If it doesnt start sometime today, end sometime today, or surround today, just skip over it
	                
                    if (
                        !(($rs['start_date'] >= $current_date && $rs['start_date'] <= $current_date)
                        || ($rs['end_date'] >= $current_date && $rs['end_date'] <= $current_date)
                        || ($rs['start_date'] <= $current_date && $rs['end_date'] >= $current_date))
                       ) {
                        //echo '<p>' . CmnFns::formatDateTime($rs['start_date']) . ' ' . CmnFns::formatDateTime($current_date)  . ' ' . CmnFns::formatDateTime($rs['end_date']) . '</p>';
                        continue;
                    }

                    // Just skip the reservation if the ending date/time is todays start time
                    if ($rs['end_date'] == $current_date && $rs['endTime'] == $this->startDay) { continue; }

                    // If the reservation starts before or ends after todays date, just pretend it ends today so it shows correctly
                    if ($rs['start_date'] < $current_date) {
                        $rs['startTime'] = $this->startDay;
                    }
                    if ($rs['end_date'] > $current_date) {
                        $rs['endTime'] = $this->endDay;
                    }

                    // Print out row of reservations
                    $thisStart = $rs['startTime'];
                    $thisEnd = $rs['endTime'];

                    if ($thisStart < $this->startDay && $thisEnd > $this->startDay)
                        $thisStart = $this->startDay;
                    else if ($thisStart < $this->startDay && $thisEnd <= $this->startDay)
                        continue;    // Ignore reservation, its off the lab

                    if ($thisStart < $this->endDay && $thisEnd > $this->endDay)
                        $thisEnd = $this->endDay;
                    else if ($thisStart >= $this->endDay && $thisEnd > $this->startDay)
                        continue;    // Ignore reservation, its off the lab

                    $colspan = intval(($thisEnd - $thisStart) / $this->timeSpan);
                    $this->move_to_starting_col($rs, $thisStart, $prevTime, $this->timeSpan, $id, $current_date, $show, $color);

                    if ($rs['is_blackout'] == 1) {
                      $this->write_blackout($rs, $colspan);
                    } else {
                      $this->write_reservation($rs, $colspan);
                    }

                    // Set prevTime to this reservation's ending time
                    $prevTime = $thisEnd;
                }
            }
            //echo '<p>endDay: ' . $this->endDay . ', prevTime: ' . $prevTime . ', timeSpan: ' . $this->timeSpan . '</p>';
            $this->finish_row($this->endDay, $prevTime, $this->timeSpan, $id, $current_date, $show, $color);
        }
    }

    /**
    * Return the formatted date
    * @return string formatted date
    */
    function get_display_date() {
        return translate_date('lab_daily', $this->_date['current']);
    }

    /**
    * Sets up all date variables needed in the scheduler
    * @param none
    * @return array of all needed date variables
    */
    function get_date_vars() {
        $default = false;
        $getDate = filter_input(INPUT_GET, 'date');
        $jumpMonth = filter_input(INPUT_POST, 'jumpMonth');
	    $jumpDay = filter_input(INPUT_POST, 'jumpDay');
	    $jumpYear = filter_input(INPUT_POST, 'jumpYear');
        $dv = array();

        // For Back, Current, Next Week clicked links
        //    pull values into an array month,day,year
        $indate = (!is_null($getDate)) ? explode('-',$getDate) : array(date("m"), date("d"), date("Y"));

        // Set date values if a date has been passed in (these will always be set to a valid date)
        if ( !empty($indate) || isset($_POST['jumpForm']) ) {
            $dv['month']  = (!is_null($jumpMonth)) ? date('m', mktime(0,0,0,$jumpMonth,1)) : date('m', mktime(0,0,0,$indate[0],1));
            $dv['day']    = (!is_null($jumpDay)) ? date('d', mktime(0,0,0,$dv['month'], $jumpDay)) : date('d', mktime(0,0,0, $dv['month'], $indate[1]));
            $dv['year']   = (!is_null($jumpYear)) ? date('Y', mktime(0,0,0, $dv['month'], $dv['day'], $jumpYear)) : date('Y', mktime(0,0,0, $dv['month'], $dv['day'], $indate[2]));
        } else {
            // Else set values to user defined starting day of week
            $d = getdate();
            $dv['month']  = $d['mon'];
            $dv['day']    = $d['mday'];
            $dv['year']   = $d['year'];
            $default = true;
        }

        // Make timestamp for today's date
        $dv['todayTs'] = mktime(0,0,0, $dv['month'], $dv['day'], $dv['year']);

        // Get proper starting day
        $dayNo = date('w', $dv['todayTs']);

        if ($default)
            $dv['day'] = $dv['day'] - ($dayNo - $this->weekDayStart);        // Make sure week starts on correct day

        // If default view and first day has passed, move up one week
        //if ($default && (date(mktime(0,0,0,$dv['month'], $dv['day'] + $this->viewDays, $dv['year'])) <= mktime(0,0,0)))
        //    $dv['day'] += 7;

        $dv['firstDayTs'] = mktime(0,0,0, $dv['month'], $dv['day'], $dv['year']);

        // Make timestamp for last date
        // by adding # of days to view minus the day of the week to $day
        $dv['lastDayTs'] = mktime(0,0,0, $dv['month'], ($dv['day'] + $this->viewDays - 1), $dv['year']);
        $dv['current'] = $dv['firstDayTs'];

        return $dv;
    }


    /**
    * Get associative array of available times and rowspans
    * This function computes and returns an associative array
    * containing a time value and it's rowspan value as
    * $array[time] => rowspan
    * @param none
    * @return array of time value and it's associated rowspan value
    * @global $conf
    * @see CmnFns::formatTime()
    */
    function get_time_array() {
        global $conf;

        $startDay = $startingTime = $this->startDay;
        $endDay   = $endingTime   = $this->endDay;
        $interval = $this->timeSpan;
        $timeHash = array();

        // Compute the available times
        $prevTime = $startDay;

        if ( (($startDay % 60) != 0) && ($interval < 60) ) {
            $time = CmnFns::formatTime($startDay);
            $timeHash[$time] = intval((60-($startDay%60))/$interval);
            $prevTime += $interval*$timeHash[$time];
        }

        while ($prevTime < $endingTime) {
            if ($interval < 60) {
                $time = CmnFns::formatTime($prevTime);
                $timeHash[$time] = intval(60 / $interval);
                $prevTime += 60;        // Always increment by 1 hour
            }
            else {
                $colspan = 1;                // Colspan is always 1
                $time = CmnFns::formatTime($prevTime);
                $timeHash[$time] = $colspan;
                $prevTime += $interval;
            }
        }
        return $timeHash;
    }

    /**
    * Print out links to jump to new dates
    * @param none
    */
    function print_jump_links() {
        print_jump_links($this->_date['firstDayTs'], $this->viewDays, ($this->viewDays != 7), $this->lab_id);
    }

    /**
    * Return color_select for given reservation
    * @param array $rs array of reservation information
    */
    function get_reservation_colorstr($rs) {
        global $conf;

        $is_mine = false;
        $is_past = false;
        $color_select = 'other_res';        // Default color (if anything else is true, it will be changed)

        if ($this->labType != READ_ONLY) {
            if ($rs['user_id'] == $_SESSION['sessionID']) {
                $is_mine = true;
                $color_select = 'my_res';
            }
        }

        if (mktime(0,0,0, date('m'), date('d') + $this->dayOffset) > $this->_date['current']) {        // If todays date is still before or on the day of this reservation
            $is_past = true;
            $color_select = ($is_mine) ? 'my_past_res' : 'other_past_res';        // Choose which color array to use
        }

        // pending reservation
        if ( $rs['is_pending'] ) {
            $color_select = "pending";
        }

        return $color_select;
    }

    /**
    * Calculates and calls the template function to print out leading columns
	* @param array $rs array of reservation information
    * @param int $start starting time of reservation
    * @param int $prev previous ending reservation time
    * @param int $span time span for reservations
    * @param string $machid id of the resource on this table row
    * @param int $ts timestamp for the reservation start date
    * @param bool $clickable if this row's cells can be clicked to start a reservation
	* @param string $color class of column background
    */
    function move_to_starting_col($rs, $start, $prev, $span, $machid, $ts, $clickable, $color) {
        global $conf;
        $cols = (($start-$prev) / $span) - 1;
        print_blank_cols($cols, $prev, $span, $ts, $machid, $this->lab_id, $this->labType, $clickable, $color);
    }

    /**
    * Calculates and calls template function to print out trailing columns
    * @param int $end ending time of day
    * @param int $prev previous ending reservation time
    * @param int $span time span for reservations
    * @param string $machid id of the resource on this table row
    * @param int $ts timestamp for the reservation start date
    * @param bool $clickable if this row's cells can be clicked to start a reservation
	* @param string $color class of column background
    */
    function finish_row($end, $prev, $span, $machid, $ts, $clickable, $color) {
        global $conf;
        $cols = (($end-$prev) / $span) - 1;
        $is_past = (mktime(0,0,0, date('m'), date('d') + $this->dayOffset) > $this->_date['current']);        // If todays date is still before or on the day of this reservation

		print_blank_cols($cols, $prev, $span, $ts, $machid, $this->lab_id, $this->labType, $clickable, $color);
        print_closing_tr();
    }

    /**
    * Calls template function to write out the reservation cell
    * @param array $rs array of reservation information
    * @param int $colspan column span value
    */
    function write_reservation($rs, $colspan) {
        global $conf;

        $is_mine = false;
        $is_past = false;
        $is_private = $conf['app']['privacyMode'] && !Auth::isAdmin();
        $color_select = 'other_res';        // Default color (if anything else is true, it will be changed)

        if ($this->labType != READ_ONLY) {
            if ($rs['user_id'] == $_SESSION['sessionID']) {
                $is_mine = true;
                $color_select = 'my_res';
            }
        }

        if (mktime(0,0,0, date('m'), date('d') + $this->dayOffset) > $this->_date['current']) {        // If todays date is still before or on the day of this reservation
            $is_past = true;
            $color_select = ($is_mine) ? 'my_past_res' : 'other_past_res';        // Choose which color array to use
        }

        // pending reservation
        if ( $rs['is_pending'] ) {
          $color_select = "pending";
        }

        $summary = ($conf['app']['prefixNameOnSummary']) ? "{$rs['first_name']} {$rs['last_name']}\n<i>" . htmlspecialchars($rs['summary']) . '</i>' : htmlspecialchars($rs['summary']);

        // If this is the user who made the reservation or the admin,
        //  and time has not passed, allow them to edit it
        //  else only allow view
        $mod_view = ( Auth::isAdmin() || (($is_mine) && !$is_past)) ? RES_TYPE_MODIFY : RES_TYPE_VIEW;    // To use in javascript edit/view box
        $show_summary = (($this->labType != READ_ONLY || ($this->labType == READ_ONLY && $conf['app']['readOnlySummary'])) && $this->showSummary && !$is_private);
        $viewable = ($this->labType != READ_ONLY || ($this->labType == READ_ONLY && $conf['app']['readOnlyDetails']));
        write_reservation($colspan, $color_select, $mod_view, $rs['resid'],$summary , $viewable, $show_summary, $this->labType == READ_ONLY, $rs['is_pending']);
    }

    /**
    * Calls template function to write out the blackout cell
    * @param array $rs array of reservation information
    * @param int $colspan column span value
    */
    function write_blackout($rs, $colspan) {
        global $conf;
		$is_private = $conf['app']['privacyMode'] && !Auth::isAdmin();
        $show_summary = (($this->labType != READ_ONLY || ($this->labType == READ_ONLY && $conf['app']['readOnlySummary'])) && $this->showSummary && !$is_private);

        write_blackout($colspan, Auth::isAdmin(), $rs['resid'], htmlspecialchars($rs['summary']),  $show_summary);
    }

    /**
    * Prints out an error message for the user
    * @param none
    */
    function print_error() {
        CmnFns::do_error_box(translate('That lab is not available.') . '<br/><a href="javascript: history.back();">' . translate('Back') . '</a>', '', false);
    }

    function user_has_permissions($user_id) {
        $this->db->user_has_permissions($this->lab_id, $user_id);
    }
}
?>