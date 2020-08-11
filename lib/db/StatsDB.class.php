<?php
/**
* StatsDB class
* Provides all db functions for stats.php
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 03-17-05
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

class StatsDB extends DBEngine {

	var $lab_id = '';
	
	/**
	* Gets the quick stats numbers for a given table
	* @param string $table table to look up
	* @return table record count
	*/
	function get_quick_stats($table) {
		$vals = array();
		$query = 'SELECT COUNT(*) as num FROM ' . $this->get_table($table);
		if ($table == 'reservations') {
			$query .= ' WHERE is_blackout <> 1 AND is_pending <> 1 AND lab_id = ?';
			$vals[0] = $this->lab_id;
		}
		else if ($table == 'resources') {
			$query .= ' WHERE lab_id = ?';
			$vals[0] = $this->lab_id;
		}

		$result = $this->db->getRow($query, $vals);

		$this->check_for_error($result);
		
		return $result['num'];
	}
	
	/**
	* Gets all of the reservations for this lab
	* @param none
	* @return array of all reservatoin data
	*/
	function get_all_stats() {
		$return = array();
		
		$query = 'SELECT res.*, ru.user_id'
				. ' FROM ' . $this->get_table('reservations') . ' as res,'
				. $this->get_table('user') . ' as l,'
				. $this->get_table('reservation_users') . ' as ru'
				. ' WHERE ru.user_id=l.user_id AND res.is_blackout <> 1 AND res.is_pending <> 1 AND res.lab_id = ? AND ru.resid = res.resid AND ru.owner = 1';

		$result = $this->db->query($query, array($this->lab_id));
		$this->check_for_error($result);
	
		if ($result->numRows() <= 0) {
			$this->err_msg = translate('No results');
			return false;
		}
				
		while ($rs = $result->fetchRow()) {
			$return[] = $this->cleanRow($rs);
		}
		
		$result->free();
		
		return $return;
	}
	
	/**
	* Gets the list of resources for this lab
	* @param none
	* @return array of resource data
	*/
	function get_resources() {
		$return = array();
		
		$query = 'SELECT machid, name FROM ' . $this->get_table('resources') . ' WHERE lab_id = ? ORDER BY name';
		$result = $this->db->query($query, array($this->lab_id));
		$this->check_for_error($result);
		
		while ($rs = $result->fetchRow()) {
			$return[] = $this->cleanRow($rs);
		}
		
		$result->free();
		
		return $return;
	}
}
?>