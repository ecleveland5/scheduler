<?php
	list($s_sec, $s_msec) = explode(' ', microtime());	// Start execution timer
	include_once('bootstrap.php');
	include_once(BASE_DIR . '/lib/ResCalendar.class.php');
	include_once(BASE_DIR . '/templates/cpanel.template.php');

	global $auth;
	$print_view = filter_input(INPUT_GET, 'print_view', FILTER_SANITIZE_STRING);
	$type = intval(filter_input(INPUT_GET, 'view', FILTER_SANITIZE_NUMBER_INT));
	$machid = filter_input(INPUT_GET, 'machid', FILTER_SANITIZE_STRING);
	$lab_id = filter_input(INPUT_GET, 'lab_id', FILTER_SANITIZE_STRING);
	
	$t = new Template(translate('Resource Calendar'));
	$calendar = new ResCalendar($auth->getCurrentID(), $type, getCalendarActualDate(), $machid, $lab_id);
	
	if ($type === null) {
		$type = MYCALENDARTYPE_DAY;
	}
	
	$t->printHTMLHeader();
	
	if ($print_view!==null && $type===MYCALENDARTYPE_DAY) {
		$calendar->printCalendar(false, $print_view);
	} else {
		
		// Print welcome box
		$t->printWelcome();
		
		// Begin main table
		$t->startMain();
		
		startQuickLinksCol();
		showQuickLinks();        // Print out My Quick Links
		startDataDisplayCol();
		
		$calendar->printCalendar();
		
		// End main table
		$t->endMain();
		
		list($e_sec, $e_msec) = explode(' ', microtime());        // End execution timer
		$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
		echo '<!--Lab printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';
		// Print HTML footer
		$t->printHTMLFooter();
	}
	
	/**
	 * Sets the 'actualDate' field of the MyCalendar object
	 * @param none
	 * @return datestamp of the viewed date
	 */
	function getCalendarActualDate() {
		$date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);
		if ($date !== null) {
			$date_split = explode('-', $date);
		}
		else {
			$date_split = explode('-', date('m-d-Y'));
		}
		
		return mktime(0,0,0, $date_split[0], $date_split[1], $date_split[2]);
	}
?>