<?php
/**
* Create a new Calendar object and print it
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 06-05-04
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Calendar class
*/
include_once('lib/Calendar.class.php');
/**
* Template class
*/
include_once('lib/Template.class.php');

// Create Calendar
$myCal= new Calendar();
if (isset($_GET['lab_id']))
	$myCal->lab_id = $_GET['lab_id'];
	
$t = new Template($myCal->monthName . ' ' . $myCal->year);

$t->printHTMLHeader();
$t->startMain();
?>
<div align="center" style="margin: 0px; width: 100%;">
<?
// Print calendar
$myCal->printCalendar();
?>
</div>
<?
$t->endMain();
?>
	</body>
</html>