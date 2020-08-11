<?php
/**
* MyCalendarDB class
* Provides backend DB functions for the MyCalendar class
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author Richard Cantzler <rmcii@users.sourceforge.net>
* @version 07-07-05
* @package DBEngine
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Base directory of application
*/
#@define('BASE_DIR', dirname(__FILE__) . '/../..');
/**
* DBEngine class
*/
include_once(BASE_DIR . '/lib/DBEngine.class.php');

/**
* Provide all database access/manipulation functionality
* @see DBEngine
*/
class ResCalendarDB extends DBEngine {
	
	function __construct() {
		parent::__construct();				// Call parent constructor
	}
	
	/**
	* Get all reservation data
	* This function gets all reservation data
	* between a given start and end date
	* @param int $firstDate first date to return reservations from
	* @param int $lastDate last date to return reservations from
	* @param string $machid id of the user to look up reservations for
	* @param bool $is_resource if we are looking up resource data or not
	* @return array of reservation data or an empty array
	*/
	function get_all_reservations($firstDate, $lastDate, $id, $is_resource) {
		$return = array();

		// If it starts between the 2 dates, ends between the 2 dates, or surrounds the 2 dates, get it
		$sql = 'SELECT res.*, res_users.*, resources.name, resources.location, users.first_name, users.last_name FROM ' . $this->get_table('reservations') . ' as res, ' . $this->get_table('reservation_users') . ' as res_users, ' . $this->get_table('resources') . ' as resources, ' . $this->get_table('user') .' as users'
			. ' WHERE ( '
						. '( '
							. '(start_date >= ? AND start_date <= ?)'
							. ' OR '
							. '(end_date >= ? AND end_date <= ?)'
						. ' )'
						. ' OR '
						. '(start_date <= ?  AND end_date >= ?)'
			.      ' )'
			. ' AND res.resid=res_users.resid'
			. ' AND res.is_blackout <> 1'
			. ' AND res_users.owner = 1'
			. ' AND resources.machid = res.machid'
			. ' AND users.user_id = res_users.user_id'
			. ' AND res.deleted=0';
		
		$sql .= (($is_resource) ? ' AND resources.machid = ?' : ' AND res.lab_id = ?');

		$sql .= ' ORDER BY res.start_date, res.startTime, res.end_date, res.endTime';

		$values = array($firstDate, $lastDate, $firstDate, $lastDate, $firstDate, $lastDate, $id);
		
		$p = $this->db->prepare($sql);
		$result = $this->db->execute($p, $values);
		
		$this->check_for_error($result);
		
		while ($rs = $result->fetchRow()) {
			$return[] = $rs;
		}
		
		$result->free();
		
		return $return;
	}
	
	/**
	* Get a list of all resources
	* @param none
	* @return array of all resources
	*/
	function get_resources($user_id = '', $lab_id = '') {
		$return = array();

		// Get all resources that are not on hidden labs
		$sql = 'SELECT resources.* FROM ' . $this->get_table('resources') . ' 
				JOIN labs ON resources.lab_id = labs.lab_id 
				WHERE labs.isHidden <> 1 ';
		if($lab_id != ''){
			$sql .= ' AND resources.lab_id = ' . $lab_id;
		}
		$sql .=' ORDER BY labs.labTitle, resources.name';
		
		$result = $this->db->query($sql);
		
		$this->check_for_error($result);
		
		while ($rs = $result->fetchRow()) {
			$return[] = $rs;
		}
		
		$result->free();
		
		return $return;
	}
}
?>