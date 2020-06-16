<?php
/**
* LabDB class
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
@define('BASE_DIR', dirname(__FILE__) . '/../..');
/**
* DBEngine class
*/
include_once(BASE_DIR . '/lib/DBEngine.class.php');

define('BLACKOUT_ONLY', 1);			// Define constants
define('RESERVATION_ONLY', 2);
define('ALL', 3);
define('READ_ONLY', 4);

/**
* Provide all database access/manipulation functionality
* @see DBEngine
*/
class LabDB extends DBEngine {
	var $labType;
	var $lab_id;

	function __construct($lab_id, $labType) {
		parent::__construct();				// Call parent constructor
		$this->labType = $labType;
		$this->lab_id = $lab_id;
	}

	/**
	* Get all reservation data
	* This function gets all reservation data
	* between a given start and end date
	* @param int $start_date beginning date to return reservations from
	* @param int $end_date beginning date to return reservations from
	* @param array $machids machids to filter
	* @return array of reservation data formatted: $array[date|machid][#] = array of data
	*  or an empty array
	*/
	function get_all_res($start_date, $end_date, $machids) {
		$return = array();
		$mach_ids = $this->make_del_list($machids);

		// If it starts between the 2 dates, ends between the 2 dates, or surrounds the 2 dates, get it
		$sql = 'SELECT res.*, res_users.*, user.first_name, user.last_name, user.email
		FROM ' . $this->get_table('reservations') . ' as res, ' . $this->get_table('reservation_users') . ' as res_users, ' . $this->get_table('user')
			. ' as user WHERE ( '
						. '( '
							. '(start_date >= ? AND start_date <= ?)'
							. ' OR '
							. '(end_date >= ? AND end_date <= ?)'
						. ' )'
						. ' OR '
						. '(start_date <= ?  AND end_date >= ?)'
			.      ' )'
			. ' AND res.resid=res_users.resid AND res_users.owner=1 AND res_users.user_id = user.user_id';

		if ($this->labType == RESERVATION_ONLY)
			$sql .= ' AND res.is_blackout <> 1 ';
		//else if ($this->labType == BLACKOUT_ONLY)
		//	$sql .= ' AND res.is_blackout = 1 ';

		$sql .= ' AND res.machid IN (' . $mach_ids . ')';

		$sql .= ' AND res.deleted=0';

		$sql .= ' ORDER BY res.start_date, res.startTime, res.end_date, res.endTime';

		$values = array($start_date, $end_date, $start_date, $end_date, $start_date, $end_date);

		$p = $this->db->prepare($sql);
		$result = $this->db->execute($p, $values);

		$this->check_for_error($result);

		while ($rs = $result->fetchRow()) {
			$index = $rs['machid'];
			$return[$index][] = $rs;
		}

		$result->free();

		return $return;
	}

    /**
     * Checks if a user has permissions for the lab
     * @param $lab_id
     * @param $user_id
     */
    function user_has_permissions($lab_id, $user_id) {
        $sql = 'SELECT COUNT(*) AS c FROM lab_permission WHERE lab_id = ? AND user_id = ?';
        $values = array($lab_id, $user_id);
        $p = $this->db->prepare($sql);
        $result = $this->db->execute($p, $values);
        $this->check_for_error($result);
        $r = $result->fetchRow();
        echo $r['c'];
        return $r['c'];
    }
}
?>