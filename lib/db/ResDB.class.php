<?php
/**
* ResDB class handles all database functions for reservations
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
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
* Provide all access to database to manage reservations
*/
class ResDB extends DBEngine {

	/**
	* Returns all data about a specific resource
	* @param string $machid id of resource to look up
	* @return array of all resource data
	*/
	function get_equipment_data($machid) {
		$return = array();

		$result = $this->db->getRow('SELECT * FROM ' . $this->get_table('resources') . ' WHERE machid=?', array($machid));
		$this->check_for_error($result);

		if (count($result) <= 0)
			$return = translate('That record could not be found.');
		else
			$return = $this->cleanRow($result);

		return $return;
	}

	/**
	* Returns machid of resources for a given lab
	* @param string $schedid id of lab to look up
	* @return array of resource ids
	*/
	function get_equipment_ids($lab_id) {
		$return = array();
		//echo $lab_id;
		$result = $this->db-simpleQuery('SELECT machid, name FROM ' . $this->get_table('resources') . ' WHERE  lab_id = "' . $lab_id . '"');
		$this->check_for_error($result);

		if (count($result) <= 0)
			$return = translate('There are no resources found.');
		else
			$return = $this->cleanRow($result);

		return $return;
	}

	/**
	* Return all data about a given reservation
	* @param string $resid reservation id
	* @return array of all reservation data
	*/
	function get_reservation($resid) {
		$return = array();

		$result = $this->db->getRow('SELECT r.*, concat(d.first_name, " ", d.last_name) AS deleted_by, d.email as deleted_by_email FROM ' . $this->get_table('reservations') . ' r left JOIN `user` d ON r.deleted_by = d.user_id WHERE r.resid=?', array($resid));
		$this->check_for_error($result);

		if (count($result) <= 0) {
			$this->err_msg = translate('That record could not be found.');
			return false;
		}

		return $this->cleanRow($result);
	}

	/**
	* Return usage data about a given reservation
	* @param string $resid reservation id
	* @return array of all usage data
	*/
	function get_usage($resid) {
		$return = array();

		$result = $this->db->getRow('SELECT * FROM `usage` WHERE resid=?', array($resid));
		$this->check_for_error($result);

		if (count($result) <= 0) {
			$this->err_msg = translate('That record could not be found.');
			return false;
		}

		return $this->cleanRow($result);
	}

	/**
	* Checks to see if a given mach/date/start/end is already booked
	* @param Object $res reservation we are checking
	* @return bool whether time is taken or not
	*/
	function check_res(&$res) {
		$values = array (
					$res->get_machid(),
					$res->get_start_date(), $res->get_start_date(), $res->get_start(), $res->get_end_date(), $res->get_end_date(), $res->get_end(),
					$res->get_start_date(), $res->get_start_date(), $res->get_start(), $res->get_end_date(), $res->get_end_date(), $res->get_end(),
					$res->get_start_date(), $res->get_start_date(), $res->get_start(), $res->get_start_date(), $res->get_start_date(), $res->get_start(),
					$res->get_end_date(), $res->get_end_date(), $res->get_end(), $res->get_end_date(), $res->get_end_date(), $res->get_end()					
				);
		// If it starts between the 2 dates, ends between the 2 dates, or surrounds the 2 dates, get it
		$query = 'SELECT COUNT(resid) AS num FROM ' . $this->get_table('reservations')
				. ' WHERE machid=?'
				. ' AND ('
					// Is surrounded by
					//(starts on a later day OR starts on same day at a later time) AND (ends on an earlier day OR ends on the same day at an earlier time)					
					. ' ( (start_date > ? OR (start_date = ? AND startTime > ?)) AND ( end_date < ? OR (end_date = ? AND endTime < ?)) )'
					// Surrounds
					//(starts on an earlier day OR starts on the same day at an earlier time) AND (ends on a later day OR ends on the same day at a later time)
					 . ' OR ( (start_date < ? OR (start_date = ? AND startTime < ?)) AND (end_date > ? OR (end_date = ? AND endTime > ?)) )'
					// Conflicts with the starting time
					//(starts on an earlier day OR starts on the same day at an earlier time) AND (ends on a later day than the starting day OR ends on the same day as the starting day but at a later time)
					. ' OR ( (start_date < ? OR (start_date = ? AND startTime <= ?)) AND (end_date > ? OR (end_date = ? AND endTime > ?)) ) '
					// Conflicts with the ending time
					//(starts on an earlier day than this ends OR starts on the same day as this ends but at an earlier time) AND (ends on a day later than the ending day OR ends on the same day as the ending day but at a later time) 
					. ' OR ( (start_date < ? OR (start_date = ? AND startTime < ?)) AND (end_date > ? OR (end_date = ? AND endTime >= ?)) )'
				. ' ) AND deleted=0';

		$id = $res->get_id();
		if ( !empty($id) ) {		// This is only if we need to check for a modification
			$query .= ' AND resid <> ?';
			array_push($values, $id);
		}
		
		$result = $this->db->getRow($query, $values);
		$this->check_for_error($result);
		return ($result['num'] > 0);	// Return if there are already reservations

	}
	
	/**
	 * Add a new reservation to the database
	 * @param Object $res reservation that we are placing
	 * @param boolean $is_parent if this is the parent reservation of a group of recurring reservations
	 * @param array $userinfo array of users to invite
	 * @param string $accept_code acceptance code to be used for reservation accept/decline
	 * @return string $id New reservation id
	 */
	function add_res(&$res, $is_parent, $userinfo, $accept_code) {
		$id = $this->get_new_id();

		$values = array (
					$id,
					$res->get_machid(),
					$res->get_lab_id(),
					$res->get_start_date(),
					$res->get_end_date(),
					$res->get_start(),
					$res->get_end(),
					time(),
					null,
					($is_parent ? $id : $res->get_parentid()),
					$res->is_blackout,
					$res->get_pending(),
					$res->get_summary(),
					0,
					$res->get_account_id(),
					null,
					null,
					Auth::getCurrentID(),
					0,
					0,
					0,
					null,
					null
				);
		
		//$query = 'INSERT INTO ' . $this->get_table('reservations') . ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
		$query = 'INSERT INTO ' . $this->get_table('reservations') . ' 
					(resid, machid, lab_id, start_date, end_date, startTime, endTime, created, modified, parentid,
					 is_blackout, is_pending, summary, completed, account_id, technical_note, billing_note, created_by, modified_by, deleted,
					 deleted_by, deleted_tstamp, deleted_reason)
				  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
		$q = $this->db->prepare($query);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
		
		$values = null;
		$values[] = array($id, $res->user_id, 1, 0, 1, 1, null);
		for ($i = 0; $i < count($userinfo); $i++) {
			$userid = explode('|',$userinfo[$i]);
			$values[] = array($id, $userid[0], 0, 1, 0, 0, $accept_code);
		}

		$query = 'INSERT INTO ' . $this->get_table('reservation_users') . ' VALUES(?,?,?,?,?,?,?)';
		$q = $this->db->prepare($query);
		$result = $this->db->executeMultiple($q, $values);
		$this->check_for_error($result);

		unset($values, $query);
		
		return $id;
	}

	/**
	* Modify current reservation time
	* If this reservation is part of a recurring group, all reservations in the
	*  group will be modified that havent already passed
	* @param Object $res reservation that we are modifying
	* @param array $users_to_add array of userids to invite to this reservation
	* @param array $users_to_remove array of userids to remove from this reservation
	* @param string $accept_code acceptance code to be used for reservation accept/decline
	*/
	function mod_res(&$res, $users_to_add, $users_to_remove, $accept_code, $account_id) {
		$values = array (
					$res->get_start_date(),
					$res->get_end_date(),
					$res->get_start(),
					$res->get_end(),
					mktime(),
					$res->get_summary(),
					$res->get_pending(),
					$res->get_account_id(),
					
					Auth::getCurrentID(),
					
					$res->get_id(),
				);

		$query = 'UPDATE ' . $this->get_table('reservations')
                . ' SET '
				. ' start_date=?,'
				. ' end_date=?,'
				. ' startTime=?,'
                . ' endTime=?,'
                . ' modified=?,'
				. ' summary=?,'
				. ' is_pending=?,'
				. ' account_id=?,'
				. ' modified_by=?'
                . ' WHERE resid=?';

		$q = $this->db->prepare($query);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);

		// Update the owner of the reservation
		$query = 'UPDATE ' . $this->get_table('reservation_users') . ' SET user_id=? WHERE resid=? AND owner = 1';
		$q = $this->db->prepare($query);
		$result = $this->db->execute($q, array($res->get_user_id(), $res->get_id()));
		$this->check_for_error($result);		

		if (!empty($users_to_add)) {
			$values = array();
			$id = $res->get_id();

			foreach ($users_to_add as $user_id) { 
				$values[] = array($id, $user_id, 0, 1, 0, 0, $accept_code);
			}
	
			$query = 'INSERT INTO ' . $this->get_table('reservation_users') . ' VALUES(?,?,?,?,?,?,?)';
			$q = $this->db->prepare($query);
			$result = $this->db->executeMultiple($q, $values);
			$this->check_for_error($result);
		}
		
		if (!empty($users_to_remove)) {
			$values = array($res->get_id());
			$query = 'DELETE FROM ' . $this->get_table('reservation_users') . ' WHERE user_id IN (' . $this->make_del_list($users_to_remove) . ') AND resid=?';
			$q = $this->db->prepare($query);
			$result = $this->db->execute($q, $values);
			$this->check_for_error($result);
		}
		unset($values, $query);
	}

	/**
	* Deletes a reservation from the database
	* If this reservation is part of a recurring group, all reservations
	*  in the group will be deleted that havent already passed
	* @param string $id reservation id
	* @param string $parentid id of parent reservation
	* @param boolean $del_recur whether to delete recurring reservations or not
	* @param int $date timestamp of current date
	*/
	function del_res($id, $parentid, $del_recur, $date) {
		$values = array($id);
		$sql = 'SELECT resid FROM ' . $this->get_table('reservations') . ' WHERE resid=?';
		//$sql = 'DELETE ru.*, r.*'
		//		. ' FROM ' . $this->get_table('reservation_users') . ' as ru, ' . $this->get_table('reservations') . ' as r'
		//		. ' WHERE ru.resid=r.resid AND ru.resid=?';

		if ($del_recur) {			// Delete all recurring reservations
			//$sql .= ' OR ru.resid = r.parentid OR r.parentid = ? AND r.start_date >= ?';
			$sql .= ' OR parentid = ? AND start_date >= ?';
			array_push($values, $parentid, $date);
		}
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		
		while ($rs = $result->fetchRow()) {
			$resids[] = $rs['resid'];
		}
		
		$result->free();
		
		$del_list = $this->make_del_list($resids);
		
		//YYYY-MM-DD HH:MM:SS
		$sql = 'UPDATE ' . $this->get_table('reservations') . ' SET deleted = 1, deleted_tstamp = "'.date("Y-m-d H:i:s").'", deleted_by = '. AUTH::getCurrentID() .' WHERE resid IN (' . $del_list . ')';
		/*
		$sql = 'DELETE FROM ' . $this->get_table('reservations') . ' WHERE resid IN (' . $del_list . ')';
		*/
		//echo $sql;
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q);
		
		/*
		$sql = 'DELETE FROM ' . $this->get_table('reservation_users') . ' WHERE resid IN (' . $del_list . ')';
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q);

		$this->check_for_error($result);
		*/
	}
	
    /**
	* Approve a reservation
	* @param string $id reservation id
	*/
	function approve_res(&$res, $mod_recur) {
		$values = array(0, $res->get_id());

		$query = 'UPDATE ' . $this->get_table('reservations')
                . ' SET is_pending = ?'
                . ' WHERE resid = ?';
		
		if ($mod_recur) {
			$query .= ' OR parentid = ?';
			array_push($values, $res->get_parentid());
		}

		$q = $this->db->prepare($query);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
		
		unset($values, $query);
	}
	
	/**
	* Sign In entry in Usage table
	*
	*/
	function signin_res($res){
		if(!$this->is_signedin($res->get_id())){
			$values = array($res->get_id(), $res->get_user_id(), $res->get_machid());
			$query = 'INSERT INTO `usage` (resid, user_id, machid, signin) VALUES(?,?,?,now())';
			//echo "<br>".$query."<br>";
			//var_dump ($values);
			$q = $this->db->prepare($query);
			$result = $this->db->execute($q, $values);
			$this->check_for_error($result);
			
			unset($values, $query);
		}else{
			echo "This reservation is already signed in.<br>";
		}
	}
	
	/**
	* Sign Out entry in Usage table
	*
	*/
	function signout_res($res, $use_desc, $notes, $prob){
		$values = array($use_desc, $prob, $notes, $res->get_id());
		$query = 'UPDATE `usage`'
                . ' SET '
				. ' signout=now(),'
				. ' description=?,'
                . ' problems=?,'
                . ' notes=?'
                . ' WHERE resid=?';

		//echo "<br>".$query."<br>";
		$q = $this->db->prepare($query);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
		
		unset($values, $query);	
	}
	
	/**
	* Add a technical note to the reservation
	*
	* @param string $resid reservation id to look up
	* @param string $note the note that is to be added
	**/
	function add_technical_note($resid, $note){
		$values = array($note, $resid);
		$query = 'UPDATE `reservations`'
				.' SET'
				.' technical_note=?'
				.' WHERE resid=?';
		$q = $this->db->prepare($query);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
		unset($values, $query);
	}

	/**
	* Add a billing note to the reservation
	*
	* @param string $resid reservation id to look up
	* @param string $note the note that is to be added
	**/
	function add_billing_note($resid, $note){
		$values = array($note, $resid);
		$query = 'UPDATE `reservations`'
				.' SET'
				.' billing_note=?'
				.' WHERE resid=?';
		$q = $this->db->prepare($query);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
		unset($values, $query);
	}

	/**
	* Return true if res is signed in, false otherwise
	* @param string $resid reservation id to look up
	*/
	function is_signedin($resid) {
		$query = "SELECT * FROM `usage` WHERE resid = '" . $resid . "' AND signin <> NULL AND signout = NULL";
		$result	= $this->db->execute($query);

		if(mysql_num_rows($result)==0){
			return false;
		}else{
			return true;
		}
	}

	/**
	* Return all data needed in the emails
	* @param string $id reservation id to look up
	* @return array of data to be used in an email
	*/
	function get_email_info($id) {
		$query = 'SELECT r.*, rs.name, rs.rphone, rs.location'
            . ' FROM '
			. $this->get_table('resources') . ' as rs, '
			. $this->get_table('reservations') . ' as r'
			. ' WHERE r.resid=?'
			. ' AND rs.machid=r.machid';
		$result = $this->db->getRow($query, array($id));

		$this->check_for_error($result);
		return $this->cleanRow($result);
	}
	
	/**
	 * This function returns the account data associated with this reservation
	 * @param $account_id The ID of the account
	 * @return array Associative array of account data
	 */
		function get_res_account_data($account_id) {
		$query = 'SELECT * FROM accounts WHERE account_id=?';
		$result = $this->db->getRow($query, array($account_id));
		$this->check_for_error($result);
		return $this->cleanRow($result);
	}

	/**
	* Get an array of all reservation ids and dates for a recurring group
	*  of reservations, including the parent
	* @param string $parentid id of parent reservation for recurring group
	* @param int $start_date timestamp of current date
	* @return array of all reservation ids and dates
	*/
	function get_recur_ids($parentid, $start_date) {
		$return = array();

		$sql = 'SELECT resid, start_date FROM '
				. $this->get_table('reservations')
				. ' WHERE (parentid = ?'
				. ' OR resid = ?) AND parentid IS NOT NULL'
				. ' AND start_date >= ?'
				. ' ORDER BY start_date ASC';
		$result = $this->db->query($sql, array($parentid, $parentid, $start_date));

		$this->check_for_error($result);

		if ($result->numRows() <= 0) {
			$this->err_msg = translate('This reservation is not recurring.');
			return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[] = $this->cleanRow($rs);
		}

		$result->free();

		return $return;
	}
		
	/**
	* Returns all of the users and the data for this reservation
	* @param string $resid reservation id
	* @return array of user/reservation data
	*/
	function get_res_users($resid) {
		$return = array();

		$sql = 'SELECT ru.*, users.first_name, users.last_name, users.email FROM '
				. $this->get_table('reservation_users') . ' as ru, ' . $this->get_table('user') . ' as users'
				. ' WHERE ru.resid=? AND users.user_id = ru.user_id';

		$result = $this->db->query($sql, array($resid));

		$this->check_for_error($result);

		if ($result->numRows() <= 0) {
			$this->err_msg = translate('That record could not be found.');
			return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[] = $rs;
		}

		$result->free();

		return $return;
	}
	
	/**
	* Changes the members status of the reservation
	* @param string $user_id id of member to change
	* @param string $resid id of reservation to change
	*/
	function confirm_user($user_id, $resid, $parentid, $update_all) {
		$values = array(0, $user_id);
		$sql = 'UPDATE ' . $this->get_table('reservation_users') . ' SET invited=? WHERE user_id=? ';
		if ($update_all && $parentid != null) {
			$r = array();
			$result = $this->db->query('select resid from ' . $this->get_table('reservations') . ' where parentid=?', array($parentid));
			$this->check_for_error($result);
			while ($rs = $result->fetchRow()) {
				$r[] = $rs['resid'];
			}
			$sql .= ' AND resid IN (' . $this->make_del_list($r) . ')';
		}
		else {
			$sql .= ' AND resid=?';
			$values[] = $resid;
		} 
		$result = $this->db->query($sql, $values);

		$this->check_for_error($result);
	}
	
	/**
	* Removes a user from a reservation
	* @param string $user_id id of member to change
	* @param string $resid id of reservation to change
	*/
	function remove_user($user_id, $resid, $parentid, $update_all) {
		$values = array($user_id);
		$sql = 'DELETE FROM ' . $this->get_table('reservation_users') . ' WHERE user_id=? ';
		if ($update_all && $parentid != null) {
			$r = array();
			$result = $this->db->query('select resid from ' . $this->get_table('reservations') . ' where parentid=?', array($parentid));
			$this->check_for_error($result);
			while ($rs = $result->fetchRow()) {
				$r[] = $rs['resid'];
			}
			$sql .= ' AND resid IN (' . $this->make_del_list($r) . ')';
		}
		else {
			$sql .= ' AND resid=?';
			$values[] = $resid;
		} 
		$result = $this->db->query($sql, $values);

		$this->check_for_error($result);
	}
}
?>