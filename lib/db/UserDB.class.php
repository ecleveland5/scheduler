<?php
/**
* This file contains the database class to work with the User class
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @version 10-12-04
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

/**
* Provide functionality for getting and setting user data
*/
class UserDB extends DBEngine {

	/**
	* Return all data associated with this userid
	* @param string $userid id of user to find
	* @return array of user data
	*/
	function get_user_data($userid) {
		$result = $this->db->getRow('SELECT * FROM ' . $this->get_table('user') . ' WHERE user_id=?', array($userid));
		$this->check_for_error($result);
		
		if (count($result) <= 0) {
			$this->err_msg = translate('That record could not be found.');
			return false;
		}
		
		return $this->cleanRow($result);
	}
	
	/**
	* Return an array of this users permissions
	* If the user has permission to use a resource
	*  it's id will be an index in the array
	* @param string $userid id of user to look up
	* @return array of user permissions
	*/
	function get_user_perms($userid) {
		$return = array();

		$result = $this->db->query('SELECT p.*, m.name FROM ' . $this->get_table('permission') . ' as p, ' . $this->get_table('resources') . ' as m WHERE user_id=? AND p.machid=m.machid', array($userid));
		$this->check_for_error($result);
		
		while ($rs = $result->fetchRow())
			$return[$rs['machid']] = $rs['name'];
		
		$result->free();
		
		return $return;
	
	}
	
	/**
	* Returns an array of email settings for a user
	* @param string $userid id of member to look up
	* @return array of settings for user email contacts
	*/
	function get_emails($userid) {
		$result = $this->db->getRow('SELECT e_add, e_mod, e_del, e_app, e_html FROM ' . $this->get_table('user') . ' WHERE user_id=?', array($userid));
		$this->check_for_error($result);
		
		if (count($result) <= 0) {
			$this->err_msg = translate('That record could not be found.');
			return false;
		}
		
		return $result;			
	}
	
	/**
	* Sets the user email preferences in the database
	* @param string $e_add email on new reservation creation
	* @param string $e_mod email on reservation modification
	* @param string $e_del email on reservation delete
	* @param string $e_html send email in html or plain text
	* @param string $userid userid who we are managing
	*/
    function set_emails($e_add, $e_mod, $e_del, $e_app, $e_html, $lab_pref, $userid) {
		$result = $this->db->query('UPDATE ' . $this->get_table('user')
						. ' SET e_add=?, '
						. 'e_mod=?, '
						. 'e_del=?, '
						. 'e_app=?, '
						. 'e_html=?, '
						. 'lab_pref=? '
						. 'WHERE user_id=?', array($e_add, $e_mod, $e_del, $e_app, $e_html, $lab_pref, $userid));
		
		$this->check_for_error($result);
	}
	
	/**
	* Sets a users password
	* @param string $new_password the new password to set for this user
	* @param string $userid id of user to change password
	*/
	function set_password($new_password, $userid) {
		$result = $this->db->query(
						'UPDATE ' . $this->get_table('user')
						. ' SET password=? WHERE user_id=?',
						array($this->make_password($new_password), $userid)
					);
		
		$this->check_for_error($result);
	}
	
	/**
	* Returns a list of accounts to which the user_id has access
	* @param int $user_id the id of the user
	*/
	function get_accounts_list($user_id) {
		$result = $this->db->query('SELECT au.account_id , a.status, a.FRS, a.pi_last_name, a.pi, u.last_name as pi_ln, a.name, au.is_admin FROM account_users as au LEFT JOIN accounts AS a ON au.account_id = a.account_id LEFT JOIN `user` AS u ON a.pi = u.user_id WHERE au.user_id=? AND au.status=1 AND a.deleted=0', array($user_id));
		$this->check_for_error($result);
		while ($rs = $result->fetchRow())
            $return[] = $this->cleanRow($rs);
			
		return $return;
	}
	
	/**
	* Returns if user is a lab admin
	*/
	function isLabAdmin($user_id, $lab_id) {
		$result = $this->db->query(
						'SELECT ' . $this->get_table('lab_permission')
						. ' WHERE user_id=? AND lab_id=?',
						array($user_id, $lab_id)
					);
		
		$this->check_for_error($result);

		if (count($result) <= 0) {
			$this->err_msg = translate('User is not an admin.');
			return false;
		}
		
		return $this->cleanRow($result);			
	}
	
	/**
	* Sets a users password
	* @param string $new_password the new password to set for this user
	* @param string $userid id of user to change password
	*/
	function get_admin_lab_list($userid){
		$result = $this->db->query(
						'SELECT * FROM ' . $this->get_table('lab_permission')
						. ' WHERE user_id=? ORDER BY lab_id',
						array($userid)
					);
		
		$this->check_for_error($result);
		
		if (count($result) <= 0) {
			$this->err_msg = translate('User is not an admin for any labs.');
			return false;
		}
		
		return $this->cleanRow($result);			
	}
	
	/**
	 * 
	 */
	function get_num_reservations($user_id) {
		$query = 'SELECT COUNT(*) as num FROM ' . $this->get_table('reservation_users')
				. ' JOIN ' . $this->get_table('reservations')
				. ' ON reservation_users.resid = reservations.resid'
				. ' WHERE reservation_users.user_id=? AND reservation_users.owner=1 AND is_blackout <> 1 AND deleted=0';
		
		$result = $this->db->getRow($query, array($user_id));

		// Check query
		$this->check_for_error($result);
			
		return $result['num'];              // # of records
	}
	
	/**
	 * 
	 */
	function get_my_reservation_data($user_id, &$pager, $orders) {
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
		
		$query = 'SELECT res.*,
			rs.name, rs.status,
			l.first_name, l.last_name, l.user_id
			FROM ' . $this->get_table('reservations') . ' as res, ' . $this->get_table('user') . ' as l, ' . $this->get_table('resources') . ' as rs, ' . $this->get_table('reservation_users') . ' as ru
			WHERE ru.owner = 1
			AND res.resid = ru.resid
			AND res.machid=rs.machid
			AND res.is_blackout <> 1
			AND l.user_id=?
			AND ru.user_id=l.user_id
			AND res.deleted=0';
		
		$query .= ' ORDER BY ' . $order . ' ' . $vert ;
		
		$result = $this->db->limitQuery($query, $pager->getOffset(), $pager->getLimit(), array($user_id));
		
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
	 * Get the login count for the user
	 * 
	 */
	function get_login_count($user_id) {
		$query = 'SELECT login_count from `user` WHERE user_id = ?';
		$result = $this->db->getRow($query, array($user_id));
		$this->check_for_error($result);
		return $result['login_count'];
	}
	
	/**
	 * Records when a user logs in
	 * 
	 */
	function record_login($user_id, $sessionHash, $expire) {
		$query = 'UPDATE ' . $this->get_table('user') . ' SET last_login = NOW(), login_count = login_count + 1 where user_id = ?';
		$result = $this->db->query($query, array($user_id));
		$this->check_for_error($result);
		
		$query = 'REPLACE INTO ' . $this->get_table('sessions') .' SET session_hash = ?, user_id = ?, expire = ?';
		$result = $this->db->query($query, array($sessionHash, $user_id, $expire));
		$this->check_for_error($result);
	}
	
	/**
	 * This function clears the accounts to which a particular
	 * user is associated.  
	 * @param $user_id
	 * @return unknown_type
	 */
	function clear_user_account($user_id) {
		$values = array();
		
		//array_push($values, $account_id);
		array_push($values, $user_id);
		
		$sql = 'DELETE FROM '. $this->get_table('account_users') .
				' WHERE user_id=?';
		
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
	}
	
	function get_user_type($type_id) {
		$query = 'SELECT title FROM ' . $this->get_table('user_type') . ' WHERE user_type_id = ?';
		$result = $this->db->getRow($query, array($type_id));
		$this->check_for_error($result);
		return $result['title'];
	}

    function get_lab_permissions($user_id) {
        $sql = 'SELECT lp.*, l.labTitle, l.nickname FROM ' . $this->get_table('lab_permission') . ' lp JOIN ' . $this->get_table('labs') . ' l on lp.lab_id = l.lab_id WHERE lp.user_id = ?';
        $q = $this->db->prepare($sql);
        $result = $this->db->execute($q, array($user_id));
        $this->check_for_error($result);
        $return = array();
        while ($rs = $result->fetchRow()) {
            $return[] = $this->cleanRow($rs);
        }

        $result->free();
        return $return;
    }

    function get_resource_filters($lab_id) {
        $sql = 'SELECT ruf.machid, r.name FROM ' . $this->get_table('user_resource_filters') . ' AS ruf JOIN ' .
            $this->get_table('resources') . ' AS r ON ruf.`machid` = r.`machid` WHERE r.lab_id = ?';
        $q = $this->db->prepare($sql);
        $result = $this->db->execute($q, array($lab_id));
        $this->check_for_error($result);
        $return = array();
        while ($rs = $result->fetchRow()) {
            $return[$rs['machid']] = $rs['name'];
        }
        $result->free();
        return $return;

    }

    public function add_user_resource_filter($machid, $user_id) {
        $sql = 'INSERT INTO ' . $this->get_table('user_resource_filters') . '(machid, user_id) VALUES (?,?)';
        $q = $this->db->prepare($sql);
        $result = $this->db->execute($q, array($machid, $user_id));
        $this->check_for_error($result);
    }

    public function remove_user_resource_filter($machid, $user_id) {
        $sql = 'DELETE FROM ' . $this->get_table('user_resource_filters') . ' WHERE machid = ? AND user_id = ?';
        $q = $this->db->prepare($sql);
        $result = $this->db->execute($q, array($machid, $user_id));
        $this->check_for_error($result);
    }
}
?>