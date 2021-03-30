<?php
	/**
	 * Interface form for placing/modifying/viewing a reservation
	 * This file will present a form for a user to
	 *  make a new reservation or modify/delete an old one.
	 * It will also allow other users to view this reservation.
	 * @author Nick Korbel <lqqkout13@users.sourceforge.net>
	 * @author David Poole <David.Poole@fccc.edu>
	 * @author Ernie Cleveland <eclevela@umd.edu>
	 * @version 04-16-09
	 * @package phpScheduleIt
	 *
	 * Copyright (C) 2003 - 2009 phpScheduleIt
	 * License: GPL, see LICENSE
	 */
	
	include_once('bootstrap.php');
	include_once(BASE_DIR . '/lib/User.class.php');
	include_once(BASE_DIR . '/lib/Reservation.class.php');
	include_once(BASE_DIR . '/templates/cpanel.template.php');
	global $auth;
	
	$t = new Template();
	$pager = CmnFns::getNewPager();
	$pager->setTextStyle('font-size: 10px;');
	
	$t->setTitle("My Reservations : Maryland NanoCenter Scheduler");
	$t->printHTMLHeader();
	$t->printWelcome();
	$t->startMain();
	
	startQuickLinksCol();
	showQuickLinks();		// Print out My Quick Links
	startDataDisplayCol();
	
	$user = new User($auth->getCurrentID());
	$num = $user->getNumReservations();	// Get number of records
	$pager->setTotRecords($num);				// Pager method calls
	$orders = array('start_date', 'name', 'last_name', 'startTime', 'endTime');
	$res = $user->getUserReservationData($pager, $orders);
	
	showReservationTable($res, $user->db->get_err(), $user->db->get_resources_in_use());	// Print out My Reservations
	
	$pager->printPages();
	
	// End main table
	$t->endMain();
	
	// Print HTML footer
	$t->printHTMLFooter();
	