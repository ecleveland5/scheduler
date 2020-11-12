<?php
/**
* Reservation class
* Provides access to reservation data
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 11-24-04
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
* ResDB class
*/
include_once('db/ResDB.class.php');
/**
* Reservation parent class
*/
include_once('Reservation.class.php');

class Blackout extends Reservation {
	
	/**
	 * Constructor calls parent constructor, telling it is a blackout
	 * @param string $res_id reservation id for the blackout
	 */
	function __construct($res_id = null, $lab_id = null) {
		$is_pending = false;
		$is_blackout = true;
		Parent::__construct($res_id, $is_blackout, $is_pending, $lab_id);
	}
}
