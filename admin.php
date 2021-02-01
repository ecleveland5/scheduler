<?php
/**
* This file provides the interface for all administrative tasks
* No data manipulation is done in this file
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 06-24-04
* @package Admin
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
include_once('lib/Admin.class.php');
include_once('templates/cpanel.template.php');

$tool = filter_input(INPUT_GET, 'tool', FILTER_SANITIZE_STRING);

$admin = new Admin($tool);

$t = new Template(translate('System Administration'));

// Print HTML header
$t->printHTMLHeader();

// Make sure this is the admin
if (!Auth::isAdmin()) {
    CmnFns::do_error_box(translate('This is only accessable to the administrator') . '<br />'
        . '<a href="ctrlpnl.php">' . translate('Back to My Control Panel') . '</a>');
}

// Print welcome message
$t->printWelcome();

// Start main table
$t->startMain();
startQuickLinksCol();
showQuickLinks();		// Print out My Quick Links
startDataDisplayCol();

if (!$admin->is_error()){
	$admin->execute();
}else
	CmnFns::do_error_box($admin->get_error_msg());

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();
?>