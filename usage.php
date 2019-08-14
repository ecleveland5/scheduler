<?php
/**
* Allow an administrator to search all reservations
* This file either prints a form or performs a search
*  on form criteria, printing results in a table.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 11-10-04
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
list($s_sec, $s_msec) = explode(' ', microtime());	// Start execution timer
/**
* Template class
*/
include_once(__DIR__ . '/lib/Template.class.php');
/**
* UsageDB class
*/
include_once(__DIR__ . '/lib/db/UsageDB.class.php');
/**
* Include output functions
*/
include_once(__DIR__ . '/templates/usage.template.php');

// Check to make sure this is the administrator
if (!Auth::isAdmin()) {
	CmnFns::do_error_box(translate('This is only accessible to the administrator'));
}

$t = new Template(isset($_POST['search']) ? translate('Search Results') : translate('Search Resource Usage'));
    
$t->printHTMLHeader();		// Print HTML header
    
$t->printWelcome();			// Print welcome message
    
$t->startMain();			// Start main table

$db = new UsageDB();			// Connect to database

$link = CmnFns::getNewLink();	// Get Link object

// Perform function based on if search button has been pressed
if ( isset($_POST['search']) || isset($_GET['text']) ) {
	search($_POST['outputtype']);
}
else {
	showForm( 	$db->get_min_max(), 
				$db->get_table_data('user', array ('user_id', 'first_name', 'last_name'), array ('last_name', 'first_name')),
				$db->get_table_data('resources', array ('machid', 'name'), array ('name')),
				$db->get_lab_list(),
				$db->get_accounts()
			);
}
    
$t->endMain();			// End main table
    
$t->printHTMLFooter();	// Print HTML footer
/****** END MAIN CODE ******/
list($e_sec, $e_msec) = explode(' ', microtime());		// End execution timer
$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
echo '<!--Usage printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';

/**
* Perform search and print out results
* This function will perform the search for given
* criteria and print out formatted results.
* @param string $type output type
* @global $_POST['user_id'] array array of user_id's
* @global $_POST['piid'] array array of piID's
* @global $_POST['machid'] array array of machID's
* @global $_POST['startYear'] int starting year
* @global $_POST['startMonth'] int starting month
* @global $_POST['startDay'] int starting day
* @global $_POST['endYear'] int ending year
* @global $_POST['endMonth'] int ending month
* @global $_POST['endDay'] int ending day
* @global $_POST['startTime'] double starting time
* @global $_POST['endTime'] double ending time
*/
function search($type) {
    global $db;
	global $link;
	
	$html = ($type == 'html');
    
    // Store form vars for easy access
	$lab_ids= $_POST['lab_id'];	// Array of lab_ids
    $user_ids  = $_POST['user_id'];   // Array of memberID's
    $machids    = $_POST['machid'];     // Array of machID's
    $startDateMin  = mktime(0,0,0, intval($_POST['startMonthMin']), intval($_POST['startDayMin']), intval($_POST['startYearMin']));
	$startDateMax  = mktime(0,0,0, intval($_POST['startMonthMax']), intval($_POST['startDayMax']), intval($_POST['startYearMax']));
    $endDateMin    = mktime(0,0,0, intval($_POST['endMonthMin']), intval($_POST['endDayMin']), intval($_POST['endYearMin']));
	$endDateMax    = mktime(0,0,0, intval($_POST['endMonthMax']), intval($_POST['endDayMax']), intval($_POST['endYearMax']));
    $startTimeMin  = intval($_POST['startTimeMin']);
	$startTimeMax  = intval($_POST['startTimeMax']);
    $endTimeMin    = intval($_POST['endTimeMin']);
	$endTimeMax    = intval($_POST['endTimeMax']);
	
	//die($startDateMin . ' ' . $startDateMax . ' ' . $endDateMin . ' ' . $endDateMax); 
		
	$res = $db->get_reservations($lab_ids, $user_ids, $machids, $startDateMin, $startDateMax, $endDateMin, $endDateMax, $startTimeMin, $startTimeMax, $endTimeMin, $endTimeMax);
	
	$rs_hours = $db->get_equipment_times($machids);
	
    // Number of records returned
    $recs = count($res);
    // Print number of results found and a link to the text version
    echo '<h3 align="center">' . translate('Search Results found', array($recs)) . "</h3>\n"
    	. '<h5 align="center">' . $link->getLink($_SERVER['PHP_SELF'], translate('Try a different search')) . "</h5>\n";
   
    print_change_output($_POST);
	
	echo "<hr noshade size=\"1\">\n";

    // If there were no results found, exit
    if ($recs<=0)
        return;
    
    // Set up initial booleans
    $newUser = false;
    $newMach = false;
    $totalHours = 0;
    $resNo = 1;

    // Get first row
    $rs = $res[0];

    // Set up initial previous user/machine variables
    $prevUser = $rs['user_id'];
    $prevMach = $rs['machid'];
    
	/* Text file variables */
    // Create text output
    // Make global to share with other functions
	if ($type == 'text') {				
        $GLOBALS['dblStr'] = str_repeat('=',50) . "\n";
        $GLOBALS['sglStr'] = str_repeat('-',50) . "\n";
	}
	if ($type != 'html') {		// Plain-text view
		echo '<pre>';
		echo translate('Search Run On') . ' ' . date('M jS, Y - h:i:s a') . "\r\n\r\n";
	}	
    
	// Print out first table with this information
    printUserInfo($rs['first_name'], $rs['last_name'], $rs['user_id'], $type);
    printTableHeader($rs['first_name'], $rs['last_name'], $rs['name'], $type, $rs['labTitle']); 
    
	if ($type == 'csv')		// Print record id line for csv output
		print_csv_header();
	
    // Repeat for each record
    for ($i = 0; $i < count($res); $i++) {
			
		$rs = $res[$i];		// Current reservation
		
        // If we are at a new user, set them to prevUser
        if ($prevUser != $rs['user_id']) {
			$prevUser = $rs['user_id'];
			$newUser = true;
		}
		
        // If we are at a new resource, set it to prevMach
		if ($prevMach != $rs['machid']) {
			$prevMach = $rs['machid'];
			$newMach = true;
		}
	
	    // Keep running total of hours on this machine
	    $totalHours = $totalHours + ( ($rs['end_date']/60 + $rs['endTime']) - ($rs['start_date']/60 +$rs['startTime']) );
	    // Calculate what percentage that is of total machine time
	    $percent = sprintf('%.02f', ($totalHours/$rs_hours[$rs['machid']]) * 100);
	
	    // If we are making a new table (by advancing to new user or resource)
		if ($newUser || $newMach) {
			// Write total hours row and close table
			// Write out total hours for this machine
            printTableFooter($totalHours, $type, $percent);
            
			$totalHours = 0;	// Reset total hours            
            $resNo = 1;			// Reset total reservations
			
			// If it is a new user, write a comment, a extra break, and the user info
			if ($newUser) {
                // Write extra break to text output
                if ($type == 'text')
                    echo "\r\n\r\n";
                
                if ($html)
                    echo '<p>&nbsp</p>';
				
                printUserInfo($rs['first_name'], $rs['last_name'], $rs['user_id'], $type);
			}
            
			// Set both newUser and newResource to false
			$newUser = false;
			$newMach = false;
					
			// Write next table header
			printTableHeader($rs['first_name'], $rs['last_name'], $rs['name'], $type, $rs['labTitle']);		
		}
		
        // Store variables
        $start_date = CmnFns::formatDate($rs['start_date']);
		$end_date = CmnFns::formatDate($rs['end_date']);
        $created = CmnFns::formatDateTime($rs['created']);
        $modified = !empty($rs['modified']) ? CmnFns::formatDateTime($rs['modified']) : translate('N/A');
        $startTime = CmnFns::formatTime($rs['startTime']);
        $endTime = CmnFns::formatTime($rs['endTime']);
        $totTime = (($rs['end_date']/60 + $rs['endTime']) - ($rs['start_date']/60 +$rs['startTime']));
        
		print_reservation_data($type, $link, $resNo++, $start_date, $end_date, $created, $modified, $startTime, $endTime, $totTime, $rs['resid'], $rs['first_name'], $rs['last_name'], $rs['name'], $rs['user_id'], $rs['labTitle'], $rs['account'], $rs['account pi']);
    }
    unset($rs);
    
    // On last record, print out total hours
	// Write out total hours for this machine
	printTableFooter($totalHours, $type, $percent);
	
	if (!$html)
		echo '</pre>';
}
?>