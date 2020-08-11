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
#@define('BASE_DIR', dirname(__FILE__) . '/../..');
/**
* DBEngine class
*/
include_once(BASE_DIR . '/lib/DBEngine.class.php');

/**
* Provide functionality for getting and setting user data
*/
class AccountDB extends DBEngine {

	/**
	* Return all data associated with this userid
	* @param string $userid id of user to find
	* @return array of user data
	*/
	public function get_account_data($account_id, $pager=NULL) {
		if(isset($pager)) {

		}
		$result = $this->db->getRow('SELECT * FROM ' . $this->get_table('accounts') . ' WHERE account_id=? ', array($account_id));
		$this->check_for_error($result);

		if (count($result) <= 0) {
			$this->err_msg = translate('That record could not be found.');
			return false;
		}

		return $this->cleanRow($result);
	}

	public function get_account_users($account_id){
		$sql = 'SELECT account_users.user_id, `user`.first_name, `user`.last_name, `user`.email FROM '
			. $this->get_table('account_users') . ' JOIN `user` ON `user`.user_id = account_users.user_id '
			. 'WHERE account_id=? and status = 1 and `user`.archived=0';
		$result = $this->db->query($sql, array($account_id));

		$this->check_for_error($result);

		if ($result->numRows() <= 0) {
			//$this->err_msg = translate('No results');
			$result->free();
			return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[] = $this->cleanRow($rs);
		}

		$result->free();

		return $return;
	}

	public function get_account_admins($account_id) {
		$sql = 'SELECT account_users.user_id, `user`.first_name, `user`.last_name, `user`.email FROM '
			. $this->get_table('account_users') . ' JOIN `user` ON `user`.user_id = account_users.user_id '
			. 'WHERE account_id=? AND status = 1 AND `user`.archived=0 AND account_users.is_admin = 1';
		$result = $this->db->query($sql, array($account_id));
		$this->check_for_error($result);

		if ($result->numRows() <= 0) {
			$result->free();
			return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[] = $this->cleanRow($rs);
		}
		$result->free();

		return $return;
	}

	public function get_is_admin($account_id, $user_id){
		$result = $this->db->query('SELECT is_admin FROM ' . $this->get_table('account_users')
			. ' WHERE is_admin=1 AND account_id=? AND user_id=?', array($account_id, $user_id));
		$this->check_for_error($result);

		if ($result->numRows() <= 0) {
			//$this->err_msg = translate('No results');
			return false;
		}else{
			return true;
		}
	}


	public function modify_account($account, $newData) {
		$first = true;
		$sql = 'UPDATE accounts SET ';
		foreach ($newData as $key => $value) {
			//echo $key . " " .$value. "<br>";
			if($key!='account_id' && $key!='submit'){
				if(!$first){
					$sql .= ", ";
				} else {
					$first = false;
				}
				$sql .= "`".$key . "`=\"".$value."\" ";

			}
		}
		$sql .= " WHERE account_id = " . $newData['account_id'];
		//echo $sql;
		$result = $this->db->query($sql);

		$this->check_for_error($result);
	}

	/**
	* Inserts a new account into the database
	* @param array $rs array of account data
	* @return returns the ID of the new record
	*/
	public function add_account($rs) {
		$values = array();
		$rs['status'] = "1";
		//array_push($values, $id);	// Values to insert

		$values[] = $rs['FRS'];
		$values[] = $rs['sub_FRS'];
		$values[] = $rs['account_type'];
		$values[] = $rs['pi'];
		$values[] = $rs['pi_first_name'];
		$values[] = $rs['pi_last_name'];
		$values[] = $rs['pi_email'];
		$values[] = $rs['status'];
		$values[] = $rs['admin_unit'];
		$values[] = $rs['name'];

		$values[] = $rs['start_date'];
		$values[] = $rs['end_date'];
		$values[] = $rs['comments'];
		$values[] = $rs['source'];
		$values[] = $rs['agency'];
		$values[] = $rs['confirmed'];
		$values[] = $rs['last_update'];
		$values[] = $rs['admin_contact_name'];
		$values[] = $rs['admin_contact_email'];
		$values[] = $rs['admin_contact_phone'];

		$values[] = $rs['organization'];
		$values[] = $rs['billing_address1'];
		$values[] = $rs['billing_address2'];
		$values[] = $rs['billing_city'];
		$values[] = $rs['billing_state'];
		$values[] = $rs['billing_zip'];
		$values[] = $rs['business_contact_name'];
		$values[] = $rs['business_contact_email'];
		$values[] = $rs['business_contact_phone'];
		$values[] = $rs['technical_contact_name'];

		$values[] = $rs['technical_contact_email'];
		$values[] = $rs['technical_contact_phone'];

		$q = $this->db->prepare('INSERT INTO ' .
			$this->get_table('accounts') .
			' (FRS, sub_FRS, account_type, pi, pi_first_name, pi_last_name, pi_email, status, admin_unit, name,
			start_date, end_date, comments, source, agency, confirmed, last_update, admin_contact_name,
			admin_contact_email, admin_contact_phone, organization, billing_address1, billing_address2, billing_city,
			billing_state, billing_zip, business_contact_name, business_contact_email, business_contact_phone,
			technical_contact_name, technical_contact_email, technical_contact_phone)' .
			' VALUES(?,?,?,?,?, ?,?,?,?,?,
					 ?,?,?,?,?, ?,?,?,?,?,
					 ?,?,?,?,?, ?,?,?,?,?, ?,?
					 )');

		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
		echo $this->err_msg;
		$account_id = mysqli_insert_id($this->db->connection);

		return $account_id;
	}


	/**
	 * This function authorizes a user on an account.  First it
	 * checks if the link exists already.  If yes, then it does
	 * an update; if no, it adds the user_id and account_id to
	 * the account_user table along with permission information.
	 * @param $account_id
	 * @param $user_id
	 * @param $start_date
	 * @param $end_data
	 * @param $status
	 * @param $is_admin
	 */
	public function add_account_user($account_id, $user_id, $is_admin=0, $start_date=NULL, $end_date=NULL) {

		//echo "<br>adding user: ".$user_id."<br>";
		// If user is already tied to account, update the record
		if ( $this->checkExistingAcctUser($account_id, $user_id) ) {
			//echo "<br>updating<br>";
			$this->update_account_user($account_id, $user_id, 1, $is_admin, $start_date, $end_date);
		}else{
			//echo "<br>adding<br>";

			$values = array();
			$values[] = $user_id;
			$values[] = $start_date;
			$values[] = $end_date;
			$values[] = 1;
			$values[] = $is_admin;
			$values[] = $account_id;
			$sql = 'INSERT INTO '
				. $this->get_table('account_users')
				. ' (user_id, start_date, end_date, `status`, is_admin, account_id)'
				. ' VALUES(?,?,?,?,?,?)';
			$q = $this->db->prepare($sql);

			$result = $this->db->execute($q, $values);

			$this->check_for_error($result);
		}
	}


	/**
	 * Checks for and existing record for this account-user link
	 * @return boolean
	 * @param $account_id ID of the account
	 * @param $user_id ID of the user
	 */
	public function checkExistingAcctUser($account_id, $user_id) {
		$values = array();
		array_push($values, $account_id, $user_id);

		$sql = 'SELECT * FROM '
			. $this->get_table('account_users')
			. ' WHERE account_id=? AND user_id=?';
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);

		if ($result->numRows() <= 0) {
			//$this->err_msg = translate('No results');
			return false;
		}else{
			//echo "<br>record found for user: ".$user_id." on acct id: ".$account_id."<br>";
			return true;
		}
	}

	/**
	 * This function updates n user's account access information
	 * @param $account_id ID of the account
	 * @param $user_id ID of the user
	 * @param $status boolean (1 or 0) whether or not the link is allowed
	 * @param $is_admin boolean (1 or 0) admin rights
	 * @param $start_date text
	 * @param $end_date text
	 * TODO update start and end dates to type date
	 */
	public function update_account_user($account_id, $user_id, $status, $is_admin, $start_date, $end_date) {
		$values = array();
		if ( $this->checkExistingAcctUser($account_id, $user_id) ) {
			//echo "<br>updating user: ". $user_id."<br>";
			$values[] = $status;
			$values[] = $is_admin;
			$values[] = $start_date;
			$values[] = $end_date;
			$values[] = $account_id;
			$values[] = $user_id;
			$sql = 'UPDATE ' . $this->get_table('account_users') .' SET status=?, is_admin=?, start_date=?, end_date=? '
				. 'WHERE account_id=? AND user_id=?';
			$q = $this->db->prepare($sql);

			$result = $this->db->execute($q, $values);
			$this->check_for_error($result);
		}
	}

	/**
	 * This function compliments up the add/update functions by
	 * removing users who are no longer linked to the specified
	 * account.  Bulk removal of all records where account_id is
	 * matched AND the user_id is NOT in the array $keptUsers.
	 * @param $account_id
	 * @param $keptUsers array of users NOT to be removed
	 */
	public function clear_account_users($account_id, $keptUsers) {
		$values = array();
		$first = true;

		array_push($values, $account_id);

		$sql = 'DELETE FROM '. $this->get_table('account_users') .
			' WHERE account_id = ?';

		if (count($keptUsers) > 0) {
			$sql .= ' AND user_id NOT IN (';

			foreach ($keptUsers as $id) {
				if(!$first){
					$sql .= ", ";
				}
				$sql .= $id;
				$first = false;
			}
			$sql .= ')';
		}

		//echo "clearing users<br>" . $sql."<br>";

		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
	}

	public function get_billing_data($account_id){
		$values = array();
		array_push($values, $account_id);
		$sql = 'SELECT * FROM '.$this->get_table('billing_imported'). ' WHERE account_id=?
			ORDER BY billed ASC, `User Last Name` ASC, `User First Name` ASC, date ASC, `Transaction ID`';
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);

		if ($result->numRows() <= 0) {
			//$this->err_msg = translate('No results');
			$result->free();
			return false;
		}

		while ($rs = $result->fetchRow()) {
			$return[] = $this->cleanRow($rs);
		}
		//var_dump($return);
		$result->free();

		return $return;
	}

}
?>