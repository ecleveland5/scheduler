<?php
/**
* UsageDB class
* Provides database functions for usage.php
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 03-02-05
* @package DBEngine
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Base directory of application
*/
@define('BASE_DIR', dirname(__FILE__) . '/../..');
/**
* DBEngine class
*/
include_once(BASE_DIR . '/lib/DBEngine.class.php');

class UsageDB extends DBEngine {

	/**
	* Get the min and max dates of all reservations in the database
	* @param none
	* @return array of min and max dates
	*/
	function get_min_max() {
		// Get min and max dates of reservations in database
		$query = 'SELECT MIN(start_date) as startmin, MAX(start_date) as startmax, MIN(end_date) as endmin, MAX(end_date) as endmax
							FROM ' . $this->get_table('reservations') . ' WHERE is_blackout <> 1';
		$result = $this->db->getRow($query);

		// Check query
		$this->check_for_error($result);
			
		if (empty($result['startmin'])) {		// If there are no reservations, set all dates to today
			list(
				$return['startmin']['mon'], $return['startmin']['day'], $return['startmin']['year'],
				$return['startmax']['mon'], $return['startmax']['day'], $return['startmax']['year'],
				$return['endmin']['mon'], $return['endmin']['day'], $return['endmin']['year'],
				$return['endmax']['mon'], $return['endmax']['day'], $return['endmax']['year']
				) 
				= preg_split('/-/', date('m-d-Y-m-d-Y-m-d-Y-m-d-Y'));
		}
		else {							// Else, set min and max values
			list($return['startmin']['mon'], $return['startmin']['day'], $return['startmin']['year']) 
					= preg_split('/-/', date('m-d-Y', $result['startmin']));
			list($return['startmax']['mon'], $return['startmax']['day'], $return['startmax']['year'])
					= preg_split('/-/', date('m-d-Y', $result['startmax']));
			list($return['endmin']['mon'], $return['endmin']['day'], $return['endmin']['year']) 
					= preg_split('/-/', date('m-d-Y', $result['endmin']));
			list($return['endmax']['mon'], $return['endmax']['day'], $return['endmax']['year'])
					= preg_split('/-/', date('m-d-Y', $result['endmax']));
		}
		
		return $return;
	}

	/**
	* Finds all reservations matching the given criteria
	* @param array $lab_ids lab_ids
	* @param array $user_ids user_ids
	* @param array $machid machids
	* @param int $startDateMin minimum start date timestamp
	* @param int $startDateMax maximum start date timestamp
	* @param int $endDateMin minimum end date timestamp
	* @param int $endDateMax maximum end date timestamp
	* @param int $startTimeMin minimum start time value
	* @param int $startTimeMax maximum start time value
	* @param int $endTimeMin minimum end time value
	* @param int $endTimeMax maximum end time value
	* @return mixed array of reservations or false if none are found
	*/
	function get_reservations($lab_ids, $user_ids, $machids, $startDateMin, $startDateMax, $endDateMin, $endDateMax, $startTimeMin, $startTimeMax, $endTimeMin, $endTimeMax) {
		
		// Limit columns as much as possible to speed up query
		$query = 'SELECT l.user_id, l.first_name, l.last_name, 
                  rs.machid, rs.name, 
                  s.lab_id, s.labTitle, 
                  res.*, 
                  ru.user_id, ru.owner,
                  a.FRS as \'account\',
                  concat(pi.first_name, \' \', pi.last_name) as \'account pi\'
                  FROM ' . $this->get_table('reservations') . ' as res 
                  join ' . $this->get_table('reservation_users') . ' as ru on res.`resid` = ru.`resid`
                  join `user` as l on ru.`user_id` = l.`user_id`
                  join ' . $this->get_table('resources') . ' as rs on res.`machid` = rs.`machid`
                  join ' . $this->get_table('labs') . ' as s on res.`lab_id` = s.`lab_id`
                  join ' . $this->get_table('accounts') . ' as a on res.`account_id` = a.`account_id`
                  left join ' . $this->get_table('user') . ' as pi on a.`pi` = pi.`user_id`';

		// Begin setting up WHERE clause of query using passed in dates/times
		$where = ' WHERE (res.start_date>=?)
					AND (res.start_date<=?)
					AND (res.end_date>=?)
					AND (res.end_date<=?)
					AND (res.startTime>=?)
					AND (res.startTime<=?)
					AND (res.endTime>=?)
					AND (res.endTime<=?)
					AND (res.is_blackout <> 1)
					AND (res.is_pending <> 1)
					AND (res.resid = ru.resid) 
					AND (ru.owner = 1) 
					AND res.deleted = 0';
		// Begin setting up values array for query
		$values = array($startDateMin, $startDateMax, $endDateMin, $endDateMax, $startTimeMin, $startTimeMax, $endTimeMin, $endTimeMax);
	
		// Construct ORDER clause
		$order = ' ORDER BY l.last_name, l.first_name,
					rs.name,
					res.start_date, res.startTime, res.end_date, res.endTime ';
		/**********************************************
		* Determine what labs to search for
		* 
		* If the first item in the array is string "all",
		* then search on all labs.
		* Else get each lab_id passed in and search just
		* for those labs.
		**********************************************/
		// Add "AND" to where clause
		$where .= ' AND ';
	
		if ($lab_ids[0] != 'all') {
			// Join on specific memebers
			$where .= '(s.lab_id=?';
			// Push this value onto values array
			array_push($values, $lab_ids[0]);
		}
	
		if ( (count($lab_ids)>1) && ($lab_ids[0] != 'all') ) {
			for ($i=1; $i<count($lab_ids); $i++) {
				$where .= ' OR s.lab_id=?';
				// Push this value onto values array
				array_push($values, $lab_ids[$i]);
			}
		}
		// Add "AND" if WHERE clause is not empty
		$where .= ($lab_ids[0] != 'all') ? ') AND ' : '';
		// Join user/reservations on user_id
		$where .= ' (s.lab_id=res.lab_id) ';
		/**********************************************
		* Determine what users to search for
		* 
		* If the first item in the array is string "all",
		* then search on all users.
		* Else get each user_id passed in and search just
		* for those members.
		**********************************************/
		// Add "AND" to where clause
		$where .= ' AND ';
	
		if ($user_ids[0] != 'all') {
			// Join on specific memebers
			$where .= '(l.user_id=?';
			// Push this value onto values array
			array_push($values, $user_ids[0]);
		}
	
		if ( (count($user_ids)>1) && ($user_ids[0] != 'all') ) {
			for ($i=1; $i<count($user_ids); $i++) {
				$where .= ' OR l.user_id=?';
				// Push this value onto values array
				array_push($values, $user_ids[$i]);
			}
		}
		// Add "AND" if WHERE clause is not empty
		$where .= ($user_ids[0] != 'all') ? ') AND ' : '';
		// Join user/reservation_users on user_id
		$where .= ' (l.user_id=ru.user_id) ';
		/**********************************************
		* Determine what resources to search for
		* 
		* If the first item in the array is string "all",
		* then search on all resources.
		* Else get each machid passed in and search just
		* for those resources.
		**********************************************/
		// Add "AND" to where clause
		$where .= ' AND ';
		
		if ($machids[0] != 'all') {
			// Join on specific pis
			$where .= '(rs.machid=?';
			array_push($values, $machids[0]);
		}
	
		if ( (count($machids)>1) && ($machids[0] != 'all') ) {
			for ($i = 1; $i < count($machids); $i++) {
				$where .= ' OR rs.machid=?';
				array_push($values, $machids[$i]);
			}
		}
		
		// Add "AND" if WHERE clause is not empty
		$where .= ($machids[0] != 'all') ? ') AND ' : '';
		// Join resources/reservations on machid
		$where .= ' (res.machid=rs.machid) ';
		// Put query together
		$query .= $where . $order;
		
		// Prepare query
		$q = $this->db->prepare($query);
		//var_dump($query);
		// Execute query
		$result = $this->db->execute($q, $values);

		// Check query
		$this->check_for_error($result);
		
		$return = array();
		while ($rs = $result->fetchRow())
			$return[] = $this->cleanRow($rs);
		
		return $return;
	}
	
	/**
	* Return the total reservation times for each resource
	* @param array $machids machids to return
	* @return mixed resource array of machid => total time
	*/
	function get_equipment_times($machids) {
		$return = array();
		$mach_ids = $this->make_del_list($machids);
		$in = ($machids[0] != 'all') ? ' WHERE machid IN (' . $mach_ids . ') AND' : ' WHERE ';
		$query = 'SELECT sum(((end_date/60)+endTime)-((start_date/60)+startTime)) as sum, machid, is_blackout FROM ' . $this->get_table('reservations') . $in . ' (is_blackout <> 1) AND (is_pending <> 1) GROUP BY machid';
		$result = $this->db->query($query);
		
		$this->check_for_error($result);
		
		while ($rs = $result->fetchRow())
			$return[$rs['machid']] = $rs['sum'];

		return $return;
	}
}
?>