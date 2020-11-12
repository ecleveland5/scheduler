<?php
/**
 * This file accesses the database and retrieves data
 *  for adminstrative purposes
 * @author Nick Korbel <lqqkout13@users.sourceforge.net>
 * @author David Poole <David.Poole@fccc.edu>
 * @author Richard Cantzler <rmcii@users.sourceforge.net>
 * @version 08-23-05
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

class AdminDB extends DBEngine {

	/**
	 * Returns array of user data
	 * @param Object $pager pager object
	 * @param string $table name of table to retrieve
	 * @param string $orders order to return values in
	 * @param boolean $limit whether this is a limited query or not
	 * @return array of user data
	 */
	function get_all_admin_data(&$pager, $table, $orders, $limit = false) {
		$return = array();
		$lim = 0;
		if ($limit) {
			$lim = $pager->getLimit();
			$offset = $pager->getOffset();
		}
		else {
			$limit = '';
			$offset = '';
		}
		return $this->get_table_data($table, array('*'), $orders, $lim, $offset);
	}

	/**
	 * Returns an array of all reservation data
	 * @param Object $pager pager object
	 * @param array $orders order than results should be sorted in
	 * @return array of all reservation data
	 */
	function get_reservation_data(&$pager, $orders, $pending=null, $today=null) {
		$return = array();

		$order = CmnFns::get_value_order($orders);
		$vert = CmnFns::get_vert_order();

		if ($order == 'start_date' && !isset($_GET['vert'])) {		// Default the date to DESC
			$vert = 'DESC';
		}

		// Clean out the duplicated order so that MSSQL is OK
		$order_str = trim(preg_replace("/(res|l).$order(,)? (DESC|ASC)?(,)?/", '', 'res.start_date DESC, res.startTime, res.endTime, l.last_name, l.first_name'));
		if (strrpos($order_str, ',') == strlen($order_str)-1) {
			$order_str = substr($order_str, 0, strlen($order_str)-1);
		}

		// Set up query to get neccessary records ordered by user request first, then logical order
		$query = 'SELECT res.resid, res.start_date, res.end_date,
			res.startTime, res.endTime,
			res.created, res.modified,
			res.billing_note, res.technical_note,
			rs.name, rs.status,
			l.first_name, l.last_name, l.user_id
			FROM ' . $this->get_table('reservations') . ' as res, ' . $this->get_table('user') . ' as l, ' . $this->get_table('resources') . ' as rs, ' . $this->get_table('reservation_users') . ' as ru
			WHERE ru.user_id=l.user_id
			AND ru.owner = 1
			AND res.resid = ru.resid
			AND res.machid=rs.machid
			AND res.is_blackout <> 1
			AND res.deleted=0';

		if( $pending ) {
			$query .= ' AND res.is_pending = 1';
		}

		if( $today ) {
			$query .= ' AND res.start_date = UNIX_TIMESTAMP(current_date())';
		}

		$query .= ' ORDER BY ' . $order . ' ' . $vert . ', ' . $order_str;  // 'res.start_date DESC, res.startTime, res.endTime, l.last_name, l.first_name';

		$result = $this->db->limitQuery($query, $pager->getOffset(), $pager->getLimit());

		$this->check_for_error($result);

		if ($result->numRows() <= 0) {
			if ($pending) {
				$this->err_msg = translate('No reservations requiring approval');
			}
			else {
				$this->err_msg = translate('No results');
			}

			return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[] = $this->cleanRow($rs);
		}

		$result->free();

		return $return;
	}

    function get_reservation_by_id($resid) {
        $query = 'SELECT res.resid, res.start_date, res.end_date,
			res.startTime, res.endTime,
			res.created, res.modified,
			res.billing_note, res.technical_note,
			rs.name, rs.status,
			l.first_name, l.last_name, l.user_id
			FROM ' . $this->get_table('reservations') . ' as res, ' . $this->get_table('user') . ' as l, ' . $this->get_table('resources') . ' as rs, ' . $this->get_table('reservation_users') . ' as ru
			WHERE ru.user_id=l.user_id
			AND ru.owner = 1
			AND res.resid = ru.resid
			AND res.machid=rs.machid
			AND res.is_blackout <> 1
			AND res.deleted=0
			AND res.resid = ?';
        $q = $this->db->prepare($query);
        $result = $this->db->execute($q, array($resid));
        $this->check_for_error($result);
        return $result->fetchRow();
    }

	/**
	 * Returns an array of all accounts data
	 * @param Object $pager pager object
	 * @param array $orders order than results should be sorted in
	 * @param bool $getAll get all records including retired accounts
	 * @return array of all accounts data
	 */
	function get_all_accounts_data(&$pager, $orders, $getAll = false) {
		$return = array();

		$order = CmnFns::get_value_order($orders);
		$vert = CmnFns::get_vert_order();

		// Set up query to get neccessary records ordered by user request first, then logical order
		if ($order === 'pi_last_name') {
			$query = 'SELECT accounts.*, `user`.last_name
				FROM accounts
				JOIN `user` on accounts.pi=`user`.user_id';
			if (!$getAll){
				$query .= ' WHERE deleted = 0';
			}
			$query .= ' ORDER BY `user`.last_name ' . $vert . ', FRS';
		}else{
			$query = 'SELECT * FROM accounts ';
			if (!$getAll){
				$query .= ' WHERE deleted = 0';
			}
			$query .= ' ORDER BY ' . $order . ' ' . $vert;
		}

		//echo $query;
		$result = $this->db->limitQuery($query, $pager->getOffset(), $pager->getLimit());
		//$pager->setTotRecords($result->numRows());
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
	 * Returns an array of all resource data
	 * @param Pager $pager pager object
	 * @param array $orders order than results should be sorted in
	 * @param bool $getDeleted
	 * @return array of all resource data
	 */
	function get_all_equipment_data(&$pager, $orders, $getDeleted = false) {
		$return = array();
		$order = CmnFns::get_value_order($orders);
		$vert = CmnFns::get_vert_order();

		// Set up query to get neccessary records ordered by user request first, then logical order
		$query = 'SELECT rs.*, s.labTitle, s.nickname
			FROM ' . $this->get_table('resources') . ' as rs, ' . $this->get_table('labs') . ' as s
			WHERE rs.lab_id = s.lab_id';
		if ($getDeleted != true) {
			$query .= ' AND rs.deleted = 0 ';
		}
		$query .= ' ORDER BY ' . $order . ' ' . $vert;

		$result = $this->db->limitQuery($query, $pager->getOffset(), $pager->getLimit());

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
	 * Returns the number of records from a given table
	 *  (for paging purposes)
	 * @param string $table table to count
	 * @param bool $getDeleted show deleted records
	 * @return number of records in the table
	 */
	function get_num_admin_recs($table, $getDeleted = false) {
		$reservation_table = false;

		$query = 'SELECT COUNT(*) as num FROM ' . $this->get_table($table);

		if ($table === 'reservations') {
			$query .= ' WHERE is_blackout <> 1';
			$reservation_table = true;
		}
		
		if ($getDeleted != true) {
			if ($reservation_table) {
				$query .= ' AND ';
			} else {
				$query .= ' WHERE ';
			}
			$query .= 'deleted = 0';
		}
		
		// Get # of records
		$result = $this->db->getRow($query);

		// Check query
		$this->check_for_error($result);

		return $result['num'];              // # of records
	}

	/**
	 * Returns the number of reservations pending approval
	 *  (for paging purposes)
	 * @param none
	 * @return number of reservations pending approval
	 */
	function get_num_pending_res() {
		$query = 'SELECT COUNT(*) as num FROM ' . $this->get_table('reservations') . ' as reservations WHERE is_pending = 1';
		$result = $this->db->getRow($query);

		// Check query
		$this->check_for_error($result);

		return $result['num'];              // # of records
	}

	/**
	 * Returns an array of data about a lab
	 * @param int $lab_id lab id
	 * @return array of data associated with that lab
	 */
	function get_lab_data($lab_id) {

		$result = $this->db->getRow('SELECT * FROM ' . $this->get_table('labs') . ' WHERE lab_id=? AND scheduler=1', array($lab_id));
		// Check query
		$this->check_for_error($result);

		if (count($result) <= 0) {
			$this->err_msg = translate('No results');
			return false;
		}

		return $this->cleanRow($result);
	}

	/**
	 * Returns an array of lab data for display on the
	 * manage labs admin page
	 * @param Object $pager pager object
	 * @param array $orders sorted order for results
	 * @return array lab associated data
	 */
	function get_lab_manage_data(&$pager, $orders) {
		$return = array();
		$order = CmnFns::get_value_order($orders);
		$vert = CmnFns::get_vert_order();
		if (empty($orders)) {
			$orders = array('nickname');
			$vert = 'ACS';
		}
		$query = 'SELECT * FROM ' . $this->get_table('labs') . ' 
				WHERE scheduler=1
				ORDER BY ' . $order . ' ' . $vert;
		$result = $this->db->limitQuery($query, $pager->getOffset(), $pager->getLimit());

		$this->check_for_error($result);
		if ($result->numRows() <=0 ) {
			$this->err_msg = 'No labs found.';
			return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[] = $this->cleanRow($rs);
		}

		$result->free();

		return $return;
	}

	/**
	 * Returns an array of users permitted in a lab
	 * @param array $rs array of user ids
	 */
	function get_lab_trained_users($lab_id) {
		$return = array();

		$query = 'SELECT lp.*, u.first_name, u.last_name
				  FROM lab_permission AS lp
				  LEFT JOIN `user` AS u ON lp.trained_by = u.user_id
				  WHERE lp.lab_id = ?
				  ORDER BY lp.user_id';
		$q = $this->db->prepare($query);
		$result = $this->db->execute($q, array($lab_id));

		$this->check_for_error($result);

		if ($result->numRows() <=0 ) {
			$this->err_msg = 'No users';
			return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[] = $this->cleanRow($rs);
		}

		$result->free();

		return $return;
	}

	/**
	 * Inserts a new lab into the database
	 * @param array $rs array of lab data
	 */
	function add_lab($rs) {
		$values = array();

		$id = $this->get_new_id();

		array_push($values, $id);	// Values to insert
		array_push($values, $rs['labTitle']);
		array_push($values, $rs['nickname']);
		array_push($values, $rs['dayStart']);
		array_push($values, $rs['dayEnd']);
		array_push($values, $rs['timeSpan']);
		array_push($values, 12);
		array_push($values, $rs['weekDayStart']);
		array_push($values, $rs['viewDays']);
		array_push($values, $rs['isHidden']);
		array_push($values, $rs['showSummary']);
		array_push($values, $rs['adminEmail']);
		array_push($values, $rs['dayOffset']);
		array_push($values, $rs['type']);

		$q = $this->db->prepare('INSERT INTO ' . $this->get_table('labs') .
			' (lab_id,labTitle,nickname,dayStart,dayEnd,timeSpan,timeFormat,weekDayStart,viewDays,isHidden,showSummary,adminEmail,dayOffset,scheduler,type)'
			.' VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,1,?)');
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);

		return $id;
	}

	/**
	 * Edits resource data in database
	 * @param array $rs array of values to edit
	 */
	function edit_lab($rs) {
		$values = array();

		array_push($values, $rs['labTitle']);
		array_push($values, $rs['nickname']);
		array_push($values, $rs['description']);
		array_push($values, $rs['director']);
		array_push($values, $rs['manager']);
		array_push($values, $rs['building']);
		array_push($values, $rs['room_number']);
		array_push($values, $rs['url']);
		array_push($values, $rs['phone']);
		array_push($values, $rs['priority']);
		array_push($values, $rs['summary']);
		array_push($values, $rs['type']);
		array_push($values, $rs['visibility']);

		array_push($values, $rs['dayStart']);
		array_push($values, $rs['dayEnd']);
		array_push($values, $rs['timeSpan']);
		array_push($values, $rs['weekDayStart']);
		array_push($values, $rs['viewDays']);
		array_push($values, $rs['isHidden']);
		array_push($values, $rs['showSummary']);
		array_push($values, $rs['adminEmail']);
		array_push($values, $rs['dayOffset']);
		//array_push($values, $rs['scheduler']);

		array_push($values, $rs['lab_id']);

		$sql = 'UPDATE '. $this->get_table('labs') . ' SET'
			. ' labTitle=?, nickname=?, description=?, director=?, manager=?, building=?, room_number=?, url=?, phone=?, priority=?, summary=?, type=?, visibility=?,'
			. ' dayStart=?, dayEnd=?, timeSpan=?,'
			. ' weekDayStart=?, viewDays=?, isHidden=?, showSummary=?, adminEmail=?, dayOffset=?, scheduler=1'
			. ' WHERE lab_id=?';

		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
	}

	/**
	 * Delete a list of labs and all of their reservations
	 * @param array $labs array of labs
	 */
	function del_lab($labs) {
		// Do not delete default lab
		$default_lab = $this->db->getOne('SELECT lab_id FROM ' . $this->get_table('labs') . ' WHERE isDefault = 1');
		if (($idx = array_search($default_lab, $labs)) !== false) {
			unset($labs[$idx]);
		}

		$lab_ids = $this->make_del_list($labs);

		// Get all the ids of reservations that are associated with these labs
		$result = $this->db->query('SELECT resid FROM ' . $this->get_table('reservations') . ' WHERE lab_id IN (' . $lab_ids . ')');
		$this->check_for_error($result);
		$results = array();
		while ($rs = $result->fetchRow()) {
			$results[] = $rs['resid'];
		}

		$resids = $this->make_del_list($results);
		$result->free();
		// Delete out of the reservation_users table
		$result = $this->db->query('DELETE FROM ' . $this->get_table('reservation_users') . ' WHERE resid IN (' . $resids . ')');
		$this->check_for_error($result);
		// Delete out of the reservations table
		$result = $result = $this->db->query('DELETE FROM ' . $this->get_table('reservations') . ' WHERE resid IN (' . $resids . ')');
		$this->check_for_error($result);

		// Delete all reservations for these labs
		//$result = $this->db->query('DELETE r, ru'
		//						. ' FROM ' . $this->get_table('reservations') . ' r LEFT JOIN ' . $this->get_table('reservation_users') . ' ru '
		//						. ' ON r.resid = ru.resid WHERE r.lab_id IN(' . $lab_ids . ')');
		$this->check_for_error($result);
		// Delete all labs
		$result = $this->db->query('DELETE FROM ' . $this->get_table('labs') . ' WHERE lab_id IN(' . $lab_ids . ')');
		$this->check_for_error($result);

		$newid = $this->db->getOne('SELECT lab_id FROM ' . $this->get_table('labs') . ' WHERE isDefault = 1');

		// Reassign all resources from old lab to default
		$result = $this->db->query('UPDATE ' . $this->get_table('resources') . ' SET lab_id = ? WHERE lab_id IN(' . $lab_ids . ')', array($newid));

		$this->check_for_error($result);
	}

	/**
	 * Sets the default lab
	 * @param string $lab_id id of default lab
	 */
	function set_default_lab($lab_id) {
		$result = $this->db->query('UPDATE ' . $this->get_table('labs') . ' SET isDefault = 0');
		$this->check_for_error($result);

		$result = $this->db->query('UPDATE ' . $this->get_table('labs') . ' SET isDefault = 1 WHERE lab_id = ?', array($lab_id));
		$this->check_for_error($result);
	}

	/**
	 * Return the number of user records found in a search
	 *  for use in paging
	 * @param string $last_name last name to search for
	 * @param string $first_name first name to search for
	 * @return number of records found
	 */
	function get_num_search_recs($first_name, $last_name, $show_deleted = null) {
		$sql = 'SELECT COUNT(*) AS num FROM ' . $this->get_table('user')
			. ' WHERE first_name LIKE "' . $first_name . '%" AND last_name LIKE "' . $last_name . '%"';
		
		if ($show_deleted !== '1') {
			$sql .= ' AND deleted=0';
		}
		
		$result = $this->db->getRow($sql);

		$this->check_for_error($result);
		return $result['num'];
	}

	/**
	 * Return the number of account records found in a search
	 * for use in paging
	 * @param string $frs FRS to search for
	 * @param boolean $getDeleted flag for getting deleted accounts.
	 * @return number of records found
	 */
	function get_num_account_search_recs($frs, $getDeleted = false) {
		$sql = 'SELECT COUNT(*) AS num FROM ' . $this->get_table('accounts')
			. ' WHERE FRS LIKE "%' . $frs . '%"';
		if (!$getDeleted){
			$sql .= 'AND deleted = 0';
		}

		$result = $this->db->getRow($sql);
		$this->check_for_error($result);
		return $result['num'];
	}
	
	/**
	 * Search for users matching this first and last name and return the results in an array
	 * @param string $first_name first name to search for
	 * @param string $last_name last name to search for
	 * @param $show_deleted
	 * @param object $pager pager object
	 * @param array $orders order to print results in
	 * @return array of user data
	 */
	function search_users($first_name, $last_name, $show_deleted, &$pager, $orders) {
		$return = array();

		$order = CmnFns::get_value_order($orders);
		$vert = CmnFns::get_vert_order();

		if ($order == 'date' && !isset($_GET['vert']))		// Default the date to DESC
			$vert = 'DESC';

		// Set up query to get necessary records ordered by user request first, then logical order
		$query = 'SELECT l.*'
			. ' FROM ' . $this->get_table('user') . ' as l'
			. '	WHERE first_name LIKE "' . $first_name . '%" AND last_name LIKE "' . $last_name . '%"';
		
		if ($show_deleted !== '1') {
			$query .= ' AND deleted=0';
		}
		$query .= ' ORDER BY ' . $order . ' ' . $vert . ', l.last_name, l.first_name';

		$result = $this->db->limitQuery($query, $pager->getOffset(), $pager->getLimit());

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
	 * Returns an array of data about a resource
	 * @param int $machID resource id
	 * @return array of data associated with that resource
	 */
	function get_equipment_data($machid) {
		$return = array();

		$result = $this->db->getRow('SELECT * FROM ' . $this->get_table('resources') . ' WHERE machid=?', array($machid));
		// Check query
		$this->check_for_error($result);

		if (count($result) <= 0) {
			$this->err_msg = translate('No results');
			return false;
		}

		$return = $this->cleanRow($result);

        /*
		$p = $this->db->prepare('SELECT * FROM resource_auto_reserve_link WHERE parent_resource_id = ?');
		$result = $this->db->execute($p, array($machid));
		$this->check_for_error($result);
		$linked_resources = array();
		while ($rs = $result->fetchRow()) {
			$linked_resources[] = $this->cleanRow($rs);
		}

		$return['linked_resources'] = $linked_resources;
        */
		return $return;
	}


	/**
	 * Returns an array of user of a resource
	 * @param int $machID resource id
	 * @return array of data associated with that resource
	 */
	function get_equipment_users($machid) {
		$return = array();

		$result = $this->db->Query('SELECT p.user_id, u.first_name, u.last_name, u.email FROM ' . $this->get_table('permission') . ' AS p JOIN `user` AS u ON p.user_id = u.user_id WHERE p.machid=? ORDER BY u.last_name, u.first_name', array($machid));
		// Check query
		$this->check_for_error($result);

		//$result->numRows();
		//echo $machid;

		if ($result->numRows() <= 0) {
			$this->err_msg = translate('No results');
			//return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[] = $rs;
		}

		$result->free();

		return $return;
	}


	/**
	 * Deletes a list of users from the database
	 * @param array $users list of users to delete
	 */
	function del_users($users) {
		$uids = $this->make_del_list($users);

		/* Update 2-20-2009
		 * To maintain historical records, deletion will be replaced by a flag in the user table
		 */

		$result = $this->db->query('UPDATE `' . $this->get_table('user') . '` SET deleted=1 WHERE user_id IN (' . $uids . ')');
		$this->check_for_error($result);

		/*		// Delete users
		 $result = $this->db->query('DELETE FROM ' . $this->get_table('user') . ' WHERE user_id IN (' . $uids . ')');
		 $this->check_for_error($result);
		 // Delete reservation participation
		 $result = $this->db->query('DELETE FROM ' . $this->get_table('reservation_users') . ' WHERE user_id IN (' . $uids . ') AND owner <> 1');
		 $this->check_for_error($result);
		 // Delete all reservations, reservation_users for these users if they owned the reservation
		 $result = $this->db->query('SELECT resid FROM ' . $this->get_table('reservation_users') . ' WHERE user_id IN (' . $uids . ') AND owner = 1');
		 $this->check_for_error($result);
		 $results = array();
		 while ($rs = $result->fetchRow()) {
			$results[] = $rs['resid'];
			}
			$resids = $this->make_del_list($results);
			$result->free();
			$result = $this->db->query('DELETE FROM ' . $this->get_table('reservation_users') . ' WHERE resid IN (' . $resids . ')');
			$this->check_for_error($result);
			//$result = $this->db->query('DELETE r, ru FROM ' . $this->get_table('reservations') . ' r LEFT JOIN ' . $this->get_table('reservation_users') . ' ru ON r.resid = ru.resid  WHERE ru.user_id IN (' . $uids . ') AND ru.owner = 1');
			$result = $result = $this->db->query('DELETE FROM ' . $this->get_table('reservations') . ' WHERE resid IN (' . $resids . ')');
			$this->check_for_error($result);
			// Delete permissions
			$result = $this->db->query('DELETE FROM ' . $this->get_table('permission') . ' WHERE user_id IN (' . $uids . ')');
			$this->check_for_error($result);
			*/
	}
	
	public function undelete_users(array $user_ids) {
		$q_marks = implode(',', array_fill(0, count($user_ids), '?'));
		$sql = 'UPDATE ' . $this->get_table('user') . ' SET deleted=0 WHERE user_id IN ('.$q_marks.')';
		$p = $this->db->prepare($sql);
		$result = $this->db->execute($p, $user_ids);
		$this->check_for_error($result);
	}

	/**
	 * Removes each user_id from the lab_permission
	 * that matches the specified lab_id
	 */
	function edit_lab_users($users, $lab_id) {
		$uids = $this->make_del_list($users);
		$result = $this->db->query('DELETE FROM lab_permission WHERE user_id IN (' . $uids . ') AND lab_id = ' . $lab_id);
		$this->check_for_error($result);
	}

	/**
	 * Inserts a new resource into the database
	 * @param array $rs array of resource data
	 */
	function add_resource($rs) {
		$values = array();

		$id = $this->get_new_id();

		array_push($values, $id);	// Values to insert
		array_push($values, $rs['lab_id']);
		array_push($values, $rs['name']);
		array_push($values, $rs['location']);
		array_push($values, $rs['rphone']);
		array_push($values, $rs['notes']);
		array_push($values, 'a');
		array_push($values, $rs['minRes']);
		array_push($values, $rs['maxRes']);
		array_push($values, intval(isset($rs['autoAssign'])));
		array_push($values, intval(isset($rs['approval'])));
		array_push($values, intval(isset($rs['allow_multi'])));

		array_push($values, $rs['umd_rate']);
		array_push($values, $rs['maryland_system_rate']);
		array_push($values, $rs['university_rate']);
		array_push($values, $rs['government_rate']);
		array_push($values, $rs['industry_rate']);
		array_push($values, $rs['owner']);

		$q = $this->db->prepare('INSERT INTO ' . $this->get_table('resources')
			. ' (`machid`, `lab_id`, `name`, `location`, `rphone`, `notes`, `status`, `minRes`, `maxRes`, `autoAssign`, `approval`, `allow_multi`, `umd_rate`, `maryland_system_rate`, `university_rate`, `government_rate`, `industry_rate`, `owner`)'
			. ' VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
		);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);

		// insert resource rates
		foreach($rs['resource_rates'] as $rate_id=>$rate) {
			$sql = 'INSERT INTO ' . $this->get_table('resource_rates')
				. ' (resource_id, '
				. ' account_type_id, '
				. ' rate, '
				. ' rate_unit)'
				. ' VALUES (?,?,?,"hour")';
			$q = $this->db->prepare($sql);
			$result = $this->db->execute($q, array($id, $rate_id, $rate));
			$this->check_for_error($result, $this->db->q);
		}

		return $id;
	}

	/**
	 * Edits resource data in database
	 * @param array $rs array of values to edit
	 */
	function edit_resource($rs) {
		$values = array();

		$sql = 'SELECT lab_id FROM ' . $this->get_table('resources') . ' WHERE machid=?';
		$old_id = $this->db->getOne($sql, array($rs['machid']));
		$this->check_for_error($old_id);

		array_push($values, $rs['lab_id']);
		array_push($values, $rs['name']);
		array_push($values, $rs['location']);
		array_push($values, $rs['rphone']);
		array_push($values, $rs['notes']);
		array_push($values, $rs['minRes']);
		array_push($values, $rs['maxRes']);
		array_push($values, $rs['umd_rate']);
		array_push($values, $rs['maryland_system_rate']);
		array_push($values, $rs['university_rate']);
		array_push($values, $rs['government_rate']);
		array_push($values, $rs['industry_rate']);
		array_push($values, intval(isset($rs['autoAssign'])));
		array_push($values, intval(isset($rs['approval'])));
		array_push($values, intval(isset($rs['allow_multi'])));
		array_push($values, $rs['owner']);
		array_push($values, $rs['staff_contact']);
		array_push($values, $rs['edit_horizon']);
		array_push($values, $rs['machid']);

		$sql = 'UPDATE '. $this->get_table('resources') . ' SET '
			. 'lab_id=?, name=?, location=?, rphone=?, notes=?, minRes=?, maxRes=?, umd_rate=?, maryland_system_rate=?, university_rate=?, government_rate=?, industry_rate=?, autoAssign=?, approval=?, allow_multi=?, owner=?, staff_contact=?, edit_horizon=? WHERE machid=?';

		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);

		// insert resource rates

        $sql = 'DELETE FROM ' . $this->get_table('resource_rates') . ' WHERE resource_id = ?';
        $q = $this->db->prepare($sql);
        $result = $this->db->execute($q, array($rs['machid']));
        $this->check_for_error($result, $this->db->q);

		foreach($rs['resource_rates'] as $rate_id=>$rate) {
			$sql = 'INSERT INTO ' . $this->get_table('resource_rates')
				. ' (resource_id, '
				. ' account_type_id, '
				. ' rate, '
				. ' rate_unit)'
				. ' VALUES (?,?,?,"hour")';
			$q = $this->db->prepare($sql);
			$result = $this->db->execute($q, array($rs['machid'], $rate_id, $rate));
			$this->check_for_error($result, $this->db->q);
		}

		if ($old_id != $rs['lab_id']) {		// Update reservations if lab changes
			$sql = 'UPDATE ' . $this->get_table('reservations') . ' SET lab_id=? WHERE machid=?';
			$result = $this->db->query($sql, array($rs['lab_id'], $rs['machid']));
			$this->check_for_error($result);
		}
	}

	/**
	 * Deletes a list of resources from the database
	 * @param array $rs list of machids to delete
	 * @param array $resource_list_shown list of machids that were displayed
	 */
	function del_resource($rs, $resource_list_shown) {
		if ($rs === null) {
			$rs = array();
		}
		$rs_list = $this->make_del_list($rs);
		
		$first = true;
		$resource_list_shown_sql_string = '';
		foreach ($resource_list_shown as $machid) {
			if (!$first) {
				$resource_list_shown_sql_string .= ',';
			}
			$resource_list_shown_sql_string.= '?';
			$first = false;
		}
		$first = true;
		if (empty($rs)) {
			$resources_to_delete_sql_string = "''";
		} else {
			$resources_to_delete_sql_string = "";
		}
		foreach ($rs as $machid) {
			if (!$first) {
				$resources_to_delete_sql_string .= ',';
			}
			$resources_to_delete_sql_string .= '?';
			$first = false;
		}
		$sql = "UPDATE " . $this->get_table('resources') . " SET deleted = IF(machid IN (".$resources_to_delete_sql_string."), 1, 0) " .
			"WHERE machid IN (".$resource_list_shown_sql_string.")";
		
		$values = array_merge($rs, $resource_list_shown);
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		//$this->check_for_error($result);
		
		
		// Get all the ids of reservations that are associated with these resources
		/*
		$result = $this->db->query('SELECT resid FROM ' . $this->get_table('reservations') . ' WHERE machid IN (' . $rs_list . ')');
		$this->check_for_error($result);
		$results = array();
		while ($rs = $result->fetchRow()) {
			$results[] = $rs['resid'];
		}

		$resids = $this->make_del_list($results);
		$result->free();
		*/
		
		// Delete out of the reservation_users table
		//$result = $this->db->query('DELETE FROM ' . $this->get_table('reservation_users') . ' WHERE resid IN (' . $resids . ')');
		//$result = $this->db->query('UPDATE ' . $this->get_table('reservation_users') . ' SET deleted = 1 WHERE resid IN (' . $resids . ')');
		//$this->check_for_error($result);

		// Delete out of the reservations table
		//$result = $result = $this->db->query('DELETE FROM ' . $this->get_table('reservations') . ' WHERE machid IN (' . $rs_list . ')');
		//$result = $this->db->query('UPDATE ' . $this->get_table('reservations') . ' SET deleted = 1 WHERE machid IN (' . $rs_list . ')');
		//$this->check_for_error($result);

		// Delete resources
		//$result = $this->db->query('DELETE FROM ' . $this->get_table('resources') . ' WHERE machid IN (' . $rs_list . ')');
		//$result = $this->db->query('UPDATE ' . $this->get_table('resources') . ' SET deleted = 1 WHERE machid IN (' . $rs_list . ')');
		//$this->check_for_error($result);

		// Delete all reservations and the associated record in reservation_users using these resources
		//$result = $this->db->query('DELETE r, ru FROM ' . $this->get_table('reservations') . ' r LEFT JOIN ' . $this->get_table('reservation_users') . ' ru ON r.resid = ru.resid WHERE r.machid IN (' . $rs_list . ')');
		//$this->check_for_error($result);

		// Delete permissions
		//$result = $this->db->query('DELETE FROM ' . $this->get_table('permission') . ' WHERE machid IN (' . $rs_list . ')');
		//$this->check_for_error($result);
	}

	/**
	 * Toggles a resource active/inactive
	 * @param string $machid id of resource to toggle
	 * @param string $status current status of the resource
	 */
	function tog_resource($machid, $status) {
		$status = ($status == 'a') ? 'u' : 'a';
		$result = $this->db->query('UPDATE ' . $this->get_table('resources') . ' SET status=? WHERE machid=?', array($status, $machid));
		$this->check_for_error($result);
	}

	/**
	 * Inserts a new account into the database
	 * @param array $rs array of account data
	 */
	function add_account_admin($rs) {
		$values = array();

		$id = $this->get_new_id();

		//array_push($values, $id);	// Values to insert
		array_push($values, $rs['FRS']);
		array_push($values, $rs['sub_FRS']);
		array_push($values, $rs['pi']);
		array_push($values, $rs['pi_first_name']);
		array_push($values, $rs['pi_last_name']);
		array_push($values, $rs['pi_email']);
		array_push($values, $rs['status']);
		array_push($values, $rs['admin_unit']);
		array_push($values, $rs['name']);

		array_push($values, $rs['start_date']);
		array_push($values, $rs['end_date']);
		array_push($values, $rs['comments']);
		array_push($values, $rs['source']);
		array_push($values, $rs['agency']);
		array_push($values, $rs['confirmed']);
		array_push($values, $rs['last_update']);
		array_push($values, $rs['fed_id']);
		array_push($values, $rs['admin_contact_name']);
		array_push($values, $rs['admin_contact_email']);
		array_push($values, $rs['admin_contact_phone']);

		array_push($values, $rs['organization']);
		array_push($values, $rs['billing_address1']);
		array_push($values, $rs['billing_address2']);
		array_push($values, $rs['billing_city']);
		array_push($values, $rs['billing_state']);
		array_push($values, $rs['billing_zip']);
		array_push($values, $rs['business_contact_name']);
		array_push($values, $rs['business_contact_email']);
		array_push($values, $rs['business_contact_phone']);
		array_push($values, $rs['technical_contact_name']);
		array_push($values, $rs['technical_contact_email']);

		array_push($values, $rs['technical_contact_phone']);
		array_push($values, $rs['account_type']);
        array_push($values, $rs['account_category']);

        $q = $this->db->prepare('INSERT INTO ' .
			$this->get_table('accounts') .
			' (FRS, sub_FRS, pi, pi_first_name, pi_last_name, pi_email, status, admin_unit, name, start_date, end_date,
			comments, source, agency, confirmed, last_update, fed_id, admin_contact_name, admin_contact_email,
			admin_contact_phone, organization, billing_address1, billing_address2, billing_city, billing_state,
			billing_zip, business_contact_name, business_contact_email, business_contact_phone, technical_contact_name,
			technical_contact_email, technical_contact_phone, account_type, account_category)' .
			' VALUES(?,?,?,?,?,?,?,?,?,?,?,
					 ?,?,?,?,?,?,?,?,?,?,
					 ?,?,?,?,?,?,?,?,?,?,?,?,?
					 )');

		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);

		return $id;
	}

	/**
	 * Edits account data in database
	 * @param array $rs array of values to edit
	 */
	function edit_account($rs) {
		$values = array();

		array_push($values, $rs['FRS']);
		array_push($values, $rs['sub_FRS']);
		array_push($values, $rs['pi']);
		array_push($values, $rs['pi_first_name']);
		array_push($values, $rs['pi_last_name']);
		array_push($values, $rs['pi_email']);
		array_push($values, $rs['status']);
		array_push($values, $rs['admin_unit']);
		array_push($values, $rs['name']);
		array_push($values, $rs['start_date']);
		array_push($values, $rs['end_date']);
		array_push($values, $rs['comments']);
		array_push($values, $rs['source']);
		array_push($values, $rs['agency']);
		array_push($values, $rs['confirmed']);
		array_push($values, $rs['last_update']);
		array_push($values, $rs['fed_id']);
		array_push($values, $rs['admin_contact_name']);
		array_push($values, $rs['admin_contact_email']);
		array_push($values, $rs['admin_contact_phone']);
		array_push($values, $rs['organization']);
		array_push($values, $rs['billing_address1']);
		array_push($values, $rs['billing_address2']);
		array_push($values, $rs['billing_city']);
		array_push($values, $rs['billing_state']);
		array_push($values, $rs['billing_zip']);
		array_push($values, $rs['business_contact_name']);
		array_push($values, $rs['business_contact_email']);
		array_push($values, $rs['business_contact_phone']);
		array_push($values, $rs['technical_contact_name']);
		array_push($values, $rs['technical_contact_email']);
		array_push($values, $rs['technical_contact_phone']);
		array_push($values, $rs['account_type']);
		array_push($values, $rs['account_id']);

		$sql = 'UPDATE '. $this->get_table('accounts') . ' SET '
			. '	FRS=?,
					sub_FRS=?,
					pi=?,
					pi_first_name=?,
					pi_last_name=?,
					pi_email=?,
					status=?,
					admin_unit=?,
					name=?,
					start_date=?,
					end_date=?,
					comments=?,
					source=?,
					agency=?,
					confirmed=?,
					last_update=?,
					fed_id=?,
					admin_contact_name=?,
					admin_contact_email=?,
					admin_contact_phone=?,
					organization=?,
					billing_address1=?,
					billing_address2=?,
					billing_city=?,
					billing_state=?,
					billing_zip=?,
					business_contact_name=?,
					business_contact_email=?,
					business_contact_phone=?,
					technical_contact_name=?,
					technical_contact_email=?,
					technical_contact_phone=?,
					account_type=?
				WHERE account_id=?';
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
	}
	/**
	 * Deletes a list of accounts from the database
	 * @param array $rs list of account_ids to delete
	 * @param array $account_list_shown array list of account ids that were shown
	 */
	function del_account($rs, $account_list_shown) {
		$first = true;
		$account_list_shown_sql_string = '';
		foreach ($account_list_shown as $accid) {
			if (!$first) {
				$account_list_shown_sql_string .= ',';
			}
			$account_list_shown_sql_string.= '?';
			$first = false;
		}
		$first = true;
		$accounts_to_delete_sql_string = '';
		foreach ($rs as $acct) {
			if (!$first) {
				$accounts_to_delete_sql_string .= ',';
			}
			$accounts_to_delete_sql_string .= '?';
			$first = false;
		}

		$sql = 'UPDATE accounts SET deleted = IF(account_id IN ('.$accounts_to_delete_sql_string.'), 1, 0) ' .
			'WHERE account_id IN ('.$account_list_shown_sql_string.')';

		$values = array_merge($rs, $account_list_shown);
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result, 'Problem archiving accounts!');
	}

	/**
	 * Toggles a account active/inactive
	 * @param string $account_id id of account to toggle
	 * @param string $status current status of the account
	 */
	function tog_account($account_id, $status) {
		$status = ($status == 1) ? 0 : 1;
		$result = $this->db->query('UPDATE ' . $this->get_table('accounts') . ' SET status=?, last_update=now() WHERE account_id=?', array($status, $account_id));
		$this->check_for_error($result);
	}

	function is_account_admin($user_id, $account_id) {
		$result = $this->db->query('SELECT count() as count FROM ' . $this->get_table('account_users') . ' WHERE account_id = ' . $account_id . ' AND user_id = ' . $user_id . ' AND is_admin = 1');
		$this->check_for_error($result);

		$rs = $result->fetchRow();
		if($rs['count'] > 0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Clears all user permissions for a given account
	 * @param string $account_id account to clear
	 */
	function reset_account_users($account_id) {
		$result = $this->db->query('UPDATE ' . $this->get_table('account_users') . ' SET status = 0, is_admin = 0 WHERE account_id=?', array($account_id));
		$this->check_for_error($result);
	}

	/**
	 *
	 */
	function edit_account_users($account_id, $user_ids, $is_admin){
		// Create values array for prepared query
		$this->reset_account_users($account_id);

		$values = array();
		for ($i = 0; $i < count($user_ids); $i++) {
			$values[$i] = array($account_id, $user_ids[$i]);
			if(in_array($user_ids[$i], $is_admin)){
				array_push($values[$i], 1);
			}else{
				array_push($values[$i], 0);
			}
		}

		$query = 'INSERT INTO ' . $this->get_table('account_users') . ' (account_id, user_id, is_admin) VALUES (?,?,?)';
		// Prepare query
		//echo $query;
		$q = $this->db->prepare($query);
		// Execute query
		$result = $this->db->executeMultiple($q, $values);
		$this->check_for_error($result);

		unset($values);
	}

	/**
	 * Clears all user permissions
	 * @param string $user_id member id to clear
	 */
	function clear_perms($user_id) {
		$result = $this->db->query('DELETE FROM ' . $this->get_table('permission') . ' WHERE user_id=?', array($user_id));
		$this->check_for_error($result);
	}

	/**
	 *
	 */
	function clear_equipment_users($machid) {
		$result = $this->db->query('DELETE FROM ' . $this->get_table('permission') . ' WHERE machid=?', array($machid));
		$this->check_for_error($result);
	}

	/**
	 * Clears all users from the lab_permission table
	 */
	function clear_lab_permissions($lab_id, $viewed_users) {
		$v = implode(',', $viewed_users);
		$sql = "DELETE FROM lab_permission WHERE lab_id = ? AND user_id in (".$v.")";
		$result = $this->db->query($sql, array($lab_id));
		$this->check_for_error($result);
	}

	/**
	 * Adds users to the lab_permission table
	 */
	function add_lab_training($lab_id, $permissions) {
		foreach ($permissions as $u=>$uid){
			$sql = "SELECT COUNT(*) AS count FROM lab_permission WHERE user_id = ? AND lab_id = ?";
			$r = $this->db->prepare($sql);
			$result = $this->db->execute($r, array($uid, $lab_id));
			//var_dump($result);
			$rs = $result->fetchRow();
			if ($rs['count']>0){
				$sql = 'UPDATE lab_permission SET safety_trained=?, trained_by=? WHERE lab_id = ? AND user_id = ?';
			}else{
				$sql = "INSERT INTO lab_permission (safety_trained, trained_by, trained_date, lab_id, user_id) VALUES (?,?,NOW(),?,?)";
			}

			$result = $this->db->query($sql, array(1 , Auth::getCurrentID(), $lab_id, $uid));
			$this->check_for_error($result);
		}
	}

	/**
	 * Adds admin users to the lab_permission table
	 */
	function add_lab_admins($lab_id, $permissions) {
		foreach ($permissions as $u=>$uid){
			$sql = "SELECT COUNT(*) AS count FROM lab_permission WHERE user_id = ? AND lab_id = ?";
			$r = $this->db->prepare($sql);
			$result = $this->db->execute($r, array($uid, $lab_id));
			//var_dump($result);
			$rs = $result->fetchRow();
			if ($rs['count']>0){
				$sql = 'UPDATE lab_permission SET is_admin=? WHERE lab_id = ? AND user_id = ?';
			}else{
				$sql = "INSERT INTO lab_permission (is_admin, lab_id, user_id) VALUES (?,?,?)";
			}

			$result = $this->db->query($sql, array(1, $lab_id, $uid));
			$this->check_for_error($result);
		}
	}

	/**
	 *
	 */
	function add_equipment_users($machid, $users) {
		foreach($users as $user){
			$result = $this->db->query('INSERT INTO ' . $this->get_table('permission') . ' (machid, user_id) VALUES (?,?) ', array($machid, $user));
			$this->check_for_error($result);
		}
	}

	/**
	 * Sets user permissions for resources
	 * @param string $user_id member's id
	 * @param array $machids array of machids to set
	 */
	function set_perms($user_id, $machids) {
		// Create values array for prepared query
		$values = array();
		for ($i = 0; $i < count($machids); $i++) {
			$values[$i] = array($user_id, $machids[$i]);
		}

		$query = 'INSERT INTO ' . $this->get_table('permission') . ' VALUES (?,?)';
		// Prepare query
		$q = $this->db->prepare($query);
		// Execute query
		$result = $this->db->executeMultiple($q, $values);
		$this->check_for_error($result);

		unset($values);
	}

	/**
	 * Returns an array of data about a announcement
	 * @param int $announcementid announcement id
	 * @return array of data associated with that announcement
	 */
	function get_announcement_data($announcementid = NULL) {

		if (is_null($announcementid)) {
			$result = $this->db->getRow('SELECT * FROM ' . $this->get_table('announcements'), array());
		} else {
			$result = $this->db->getRow('SELECT * FROM ' . $this->get_table('announcements') . ' WHERE announcementid=?', array($announcementid));
		}
		// Check query
		$this->check_for_error($result);

		if (count($result) <= 0) {
			$this->err_msg = 'No results';
			return false;
		}

		return $this->cleanRow($result);
	}

	/**
	 * Inserts a new announcement into the database
	 * @param array $rs array of announcement data
	 */
	function add_announcement($rs) {
		$id = $this->get_new_id();

		$values = array($id, $rs['announcement'], $rs['number'], $rs['start_datetime'], $rs['end_datetime'], $rs['lab_id']);
		//var_dump($values);
		$q = $this->db->prepare('INSERT INTO ' . $this->get_table('announcements')
			. '(announcementid, announcement, number, start_datetime, end_datetime, lab_id) VALUES(?,?,?,?,?,?)');

		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);

		return $id;
	}

	/**
	 * Edits announcement data in database
	 * @param array $rs array of values to edit
	 */
	function edit_announcement($rs) {
		$values = array($rs['announcement'], $rs['number'], $rs['start_datetime'], $rs['end_datetime'], $rs['lab_id'], $rs['announcementid']);

		$sql = 'UPDATE '. $this->get_table('announcements') . ' SET'
			. ' announcement=?, number=?, start_datetime=?, end_datetime=?, lab_id=?'
			. ' WHERE announcementid=?';

		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
	}

	/**
	 * Deletes announcement data from database
	 * @param array $rs array of values to edit
	 */
	function del_announcement($announcements) {

		$announcementids = $this->make_del_list($announcements);

		// Delete all reservations for these labs
		$result = $this->db->query('DELETE FROM ' . $this->get_table('announcements') . ' WHERE announcementid IN(' . $announcementids . ')');
		$this->check_for_error($result);
	}

	/**
	 * Get a list of users, emails
	 * @param none
	 * @return array of email data
	 */
	function get_user_email($view = NULL, $overrideNoEmail = false, $order = 'last_name, first_name', $order_direction = 'ASC') {
		global $conf;
		$return = array();
		// Select all users in the system
		if (is_null($view)) {
			$result = $this->db->query('SELECT first_name, last_name, email FROM ' .
                $this->get_table('user') .
                ' WHERE email <> ? AND receive_announcements = 1 AND deleted = 0 ORDER BY ' . $order . ' ' . $order_direction, array($conf['app']['adminEmail']));
		} else {
			$view = stripslashes($view);
			//if ($view=='current_nc_users') {
				//$sql = 'SELECT * FROM ((SELECT * FROM current_fablab_users) UNION (SELECT * FROM current_nisplab_users_updated)) AS t WHERE email <> ?';
			//} else {
				$sql = 'SELECT first_name, last_name, email FROM ' . $view . ' WHERE email <> ?';
			//}
			if (!$overrideNoEmail) {
				$sql .= ' AND receive_announcements = 1';
			}
			$sql .= ' AND deleted = 0 ORDER BY ' . $order . ' ' . $order_direction;
			$result = $this->db->query($sql, array($conf['app']['adminEmail']));
		}

		// Check query


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

	/*
     * Returns a list of all users [first name, last name, user id, email]
     */
	function get_user_ids($all=false){
		global $conf;
		$return = array();

		// Select all users in the system
		$sql = 'SELECT first_name, last_name, user_id, email FROM `' . $this->get_table('user') . '`';
		if (!$all){
			$sql .= ' WHERE deleted=0';
		}
		$sql .= ' ORDER BY last_name, first_name';

		$result = $this->db->query($sql);
		// Check query
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
	 * Automatically give permission to all users in the system to use this resource
	 * @param string $machid id of resource to auto-assign
	 */
	function auto_assign($machid) {
		$values = array();
		$users = array();

		$result = $this->db->getOne('SELECT COUNT(machid) AS num FROM ' . $this->get_table('permission') . ' WHERE machid = ?', array($machid));
		$this->check_for_error($result);		// Check if this resource is in the permission table

		if ($result['num'] > 0) {				// If it is, only get the users who do not already have permission
			$exclude_members = array();
			$result = $this->db->query('SELECT user_id FROM ' . $this->get_table('permission') . ' WHERE machid=?', array($machid));
			$this->check_for_error($result);

			while($rs = $result->fetchRow())
				$exclude_members[] = $this->db->quote($rs['user_id']);

			$result = $this->db->query('SELECT user_id FROM ' . $this->get_table('user') . ' WHERE user_id NOT IN (' . join(',', $exclude_members) . ')');
			$this->check_for_error($result);
			while ($rs = $result->fetchRow())
				$users[]['user_id'] = $rs['user_id'];
		}
		else {									// Else get all users
			$users = $this->get_table_data('user', array('user_id'));
		}

		for ($i = 0; $i<count($users); $i++) {
			array_push($values, array($users[$i]['user_id'], $machid));
		}

		if (count($values) > 0 ) {
			$q = $this->db->prepare('INSERT INTO ' . $this->get_table('permission') . ' VALUES (?,?)');
			$result = $this->db->executeMultiple($q, $values);

			$this->check_for_error($result);
		}
	}

	/**
	 * Reset a password for a user
	 * @param string $user_id id of user to reset password for
	 * @param string $new_password new password value for the user
	 */
	function reset_password($user_id, $new_password) {
		$result = $this->db->query('UPDATE ' . $this->get_table('user') . ' SET password=? WHERE user_id=?', array($this->make_password($new_password), $user_id));
		$this->check_for_error($result);
	}

	/**
	 * Change the is_admin status for this user to the new status value
	 * @param string $user_id ID of the member to update
	 * @param int $new_status new is_admin status value
	 */
	function change_admin_status($user_id, $new_status) {
		$result = $this->db->query('UPDATE ' . $this->get_table('user') . ' SET is_admin = ? WHERE user_id=?', array($new_status, $user_id));
		$this->check_for_error($result);
	}


	/**
	 * Get account data from accounts table.
	 * @param string $account_id ID of account to retrieve
	 **/
	function get_account_data_admin($account_id) {
		$result = $this->db->getRow('SELECT * FROM ' . $this->get_table('accounts') . ' WHERE account_id=?', array($account_id));
		$this->check_for_error($result);

		if (count($result) <= 0) {
			$this->err_msg = translate('That record could not be found.');
			return false;
		}

		return $this->cleanRow($result);
	}

	/**
	 * Get account data from accounts table.
	 *
	 * @param string $frs   FRS # of account to retrieve
	 * @param Pager  $pager Pager object
	 * @param string $orders string sort order of columns
	 * @param bool $getAll flag for retrieving deleted data
	 * @return array array of account data
	 **/
	public function get_account_data_by_frs_admin($frs, &$pager, $orders, $getAll = false) {
		$return = array();
		$order = CmnFns::get_value_order($orders);
		$vert = CmnFns::get_vert_order();

		// Set up query to get neccessary records ordered by user request first, then logical order
		if ($order === 'pi_last_name') {
			$query = 'SELECT accounts.*, `user`.last_name
					FROM accounts
					left JOIN `user` on accounts.pi=`user`.user_id
					WHERE FRS LIKE "%'.$frs.'%"';

			if (!$getAll) {
				$query .= ' AND deleted = 0';
			}

			$query .= ' ORDER BY `user`.last_name ' . $vert . ', FRS';

		} else {

			$query = 'SELECT * FROM accounts WHERE FRS LIKE "%'.$frs.'%"';

			if (!$getAll) {
				$query .= ' AND deleted = 0';
			}

			$query .= ' ORDER BY ' . $order . ' ' . $vert;
		}

		//echo $query;
		$result = $this->db->limitQuery($query, $pager->getOffset(), $pager->getLimit());

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

	function get_account_types() {
		$return = array();

		$sql = 'SELECT * FROM account_types';
		$result = $this->db->query($sql);

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


	function get_resource_rates($resource_id) {
		$return = array();

		$sql = 'SELECT * FROM resource_rates WHERE resource_id = ?';
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, array($resource_id));
		$this->check_for_error($result);

		if ($result->numRows() <= 0) {
			$this->err_msg = translate('No results');
			return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[$rs['account_type_id']] = $this->cleanRow($rs);
		}

		$result->free();
		return $return;
	}

	function get_users_list(Pager $pager, $orders = '', $show_deleted = false) {
		$lim = $pager->getLimit();
		$offset = $pager->getOffset();
		$show_deleted_clause = null;
		$show_deleted_value = array();
		if ($show_deleted === false || $show_deleted === null) {
			$show_deleted_clause = ' WHERE deleted = ? ';
			$show_deleted_value = array('0');
		}
		return $this->get_table_data($this->get_table('user'), array('*'), $orders, $lim, $offset, $show_deleted_clause, $show_deleted_value);
	}

	function update_resource_op_status($data) {
		
		foreach ($data as $machid=>$status) {
			$values = array($status, $machid);
			$query = "UPDATE " . $this->get_table('resources') . " SET operational_status = ? WHERE machid = ?";
			$q = $this->db->prepare($query);
			$result = $this->db->execute($q, $values);
			$this->check_for_error($result);
		}
	}
}