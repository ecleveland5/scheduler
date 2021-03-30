<?php
/**
* Account class
* Provides access to account data
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Ernie Cleveland <eclevela@umd.edu>
* @version 06-06-06
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Base directory of application
*/
//@define('BASE_DIR', dirname(__FILE__) . '/..');
/**
* ResDB class
*/
include_once('db/AccountDB.class.php');
include_once(BASE_DIR . '/templates/account.template.php');

class Account {
	var $account_id		= null;				//	Properties
	var $kfs			= null;
	var $sub_kfs		= null;
	var $pi_id			= null;				//	Links to user_id
	var $pi_first_name 	= null;
	var $pi_last_name 	= null;
	var $status			= null;				//	opened, closed
	var $archived		= 0;
	var $admin_unit		= null;
	var $name			= null;
	var $start_date		= null;
	var $end_date 		= null;
	var $comments		= null;
	var $source			= null;
	var $agency			= null;
	var $confirmed		= null;
	var $last_update	= null;
	var $admin_contact_name 	= null;
	var $admin_contact_email 	= null;
	var $admin_contact_phone 	= null;
	var $organization	= null;
	var $billing_address1	= null;
	var $billing_address2	= null;
	var $billing_city	= null;
	var $billing_state	= null;
	var $billing_zip	= null;
	var $business_contact_name	= null;
	var $business_contact_phone	= null;
	var $business_contact_email	= null;
	var $technical_contact_name	= null;
	var $technical_contact_phone	= null;
	var $technical_contact_email	= null;
	var $reviewed	= 0;
	var $reviewed_by	= null;
	var $account_type	=	0;
	var $is_valid		= false;
	var $users			= null;
	var $errors = array();

	/**
	* Account constructor
	* Sets id (if applicable)
	* Sets the account status type
	* @return true if account is found
	* @param string $id id of account to load
	* @param bool $status if this is a active or not
	*/
	function __construct($id = null, $status = false) {
		$this->db = new AccountDB();

		if (!empty($id) && $id!=0) {
			$this->account_id = $id;
			$this->loadById();
			return true;
		}else{
			$this->status = $status;
		}
	}

	/**
	* Loads all account properties from the database
	* @param none
	*/
	function loadById() {
		global $auth;
		
		$account = $this->db->get_account_data($this->account_id);	// Get values from DB

		if (!$account) {
			$this->err_msg = $this->db->get_err();
			return false;
		}else
			$this->is_valid = true;

		$this->account_id			= $account['account_id'];
		$this->kfs					= $account['kfs'];
		$this->sub_kfs				= $account['sub_kfs'];
		$this->pi_id				= $account['pi'];
		$this->pi_first_name 		= $account['pi_first_name'];
		$this->pi_last_name 		= $account['pi_last_name'];
		$this->status				= $account['status'];
		$this->admin_unit			= $account['admin_unit'];
		$this->name					= $account['name'];
		$this->start_date			= $account['start_date'];
		$this->end_date 			= $account['end_date'];
		$this->comments				= $account['comments'];
		$this->source				= $account['source'];
		$this->agency				= $account['agency'];
		$this->confirmed			= $account['confirmed'];
		$this->last_update			= $account['last_update'];
		$this->admin_contact_name 	= $account['admin_contact_name'];
		$this->admin_contact_email 	= $account['admin_contact_email'];
		$this->admin_contact_phone 	= $account['admin_contact_phone'];

		$this->organization				= $account['organization'];
		$this->billing_address1			= $account['billing_address1'];
		$this->billing_address2			= $account['billing_address2'];
		$this->billing_city				= $account['billing_city'];
		$this->billing_state			= $account['billing_state'];
		$this->billing_zip				= $account['billing_zip'];
		$this->business_contact_name	= $account['business_contact_name'];
		$this->business_contact_phone	= $account['business_contact_phone'];
		$this->business_contact_email	= $account['business_contact_email'];
		$this->technical_contact_name	= $account['technical_contact_name'];
		$this->technical_contact_phone	= $account['technical_contact_phone'];
		$this->technical_contact_email	= $account['technical_contact_email'];
		$this->reviewed					= $account['reviewed'];
		$this->reviewed_by				= $account['reviewed_by'];
		$this->account_type				= $account['account_type'];
		$this->users                    = $this->db->get_account_users($this->account_id);
	}

	/*
	*
	*
	*/
	function addAccount($data) {
		global $auth;
		
		if (!$auth->isAdmin()){
			if ( ($data['pi']=='') &&
				  ( (!isset($data['pi_last_name']) || ($data['pi_last_name']!='')) &&
				    (!isset($data['pi_first_name']) ||($data['pi_first_name']!='')) )
				 )
			   {
				$this->addError('The organization field cannot be left blank.');
			}

			if ( (!isset($data['organization'])) || ($data['organization']=='') ) {
				$this->addError('The organization field cannot be left blank.');
			}

			if ( (!isset($data['billing_address1'])) || ($data['billing_address1']=='') ) {
				$this->addError('The billing address field cannot be left blank.');
			}

			if ( (!isset($data['billing_city'])) || ($data['billing_city']=='') ) {
				$this->addError('The billing city field cannot be left blank.');
			}

			if ( (!isset($data['billing_state'])) || ($data['billing_state']=='') ) {
				$this->addError('The billing state field cannot be left blank.');
			}

			if ( (!isset($data['billing_zip'])) || ($data['billing_zip']=='') ) {
				$this->addError('The billing zip field cannot be left blank.');
			}
		}
		// Since this is an external account, we must create a unique kfs #
		if ($data['kfs'] == "") {
			if (is_numeric($data['pi'])) {
				$acc_pi = new User ($data['pi']);
				$pi_last_name = $acc_pi->getLastName();
			} else {
				$pi_last_name = $data['pi_last_name'];
			}

			$kfs = substr($data['organization'], 0, 3).'-'.$pi_last_name;
			// must check if taken, if so, add incremental number
			while(!$this->db->isKFSAvailable($kfs)){
				$data['kfs'] = $kfs;
				//$this->add_error('The kfs is in use.');
				//echo '<br><br>The kfs is in use.<br><br>';

				// get iteration number and increment
				$kfs_parts = explode('-', $kfs);
				//var_dump($kfs_parts);
				//echo "<br><br>";
				if(is_numeric($kfs_parts[sizeof($kfs_parts)-1])){
					$i = (int)$kfs_parts[sizeof($kfs_parts)-1];
					$i++;
					$kfs = '';
					$kfs_array = array();
					for ($x=0; $x < sizeof($kfs_parts)-1; $x++) {
						array_push($kfs_array, $kfs_parts[$x]);
					}
					array_push($kfs_array, $i);
					$kfs = implode('-', $kfs_array);
				}else{
					$kfs .= '-1';
				}
			}
			//echo $kfs;
			$data['kfs'] = $kfs;
		}


		//echo "<br><br>";
		//var_dump($accountData);

		if ($this->hasErrors()){			// Print any errors generated above and kill app
			$this->printAllErrors(true);
		}else{

			$id = $this->db->add_account($data);
			$this->account_id = $id;
			$this->loadById();
			//add current user to account user admin list

			$this->addAccountUser($auth->getCurrentID(), 1, NULL, NULL);
			//add PI to account user admin list if PI is in user table
			if ( (is_numeric($this->pi_id)) && ($this->pi_id != NULL) ) {

				$this->addAccountUser($this->pi_id, 1, NULL, NULL);
			}

			if (!$auth->isAdmin()) {
                $this->printAccountSuccess('created');
            }

		}

	}

	/*
	*
	*
	*/
	function modifyAccount($account, $data) {
		// check for invalid data
		//$this->validateAccountData();

		if ($this->hasErrors()){			// Print any errors generated above and kill app
			$this->printAllErrors(true);
		}else{
			// update database
			$this->db->modify_account($account, $data);

			$this->printAccountSuccess('modified');
		}
	}
	
	/**
	 *
	 * @param $account
	 * @param $user
	 */
	function retireAccount($account, $user) {
		if ($this->hasErrors()){			// Print any errors generated above and kill app
			$this->printAllErrors(true);
		}else{
			$this->printAccountSuccess('retired');
		}
	}

	/**
	 * Authorizes a user on an account.
	 * Adds the user_id and account_id to the account_user table
	 * @param $user_id
	 * @param $is_admin
	 * @param $start_date
	 * @param $end_date
	 */
	function addAccountUser($user_id, $is_admin= 0 , $start_date = NULL, $end_date = NULL) {
		$this->db->add_account_user($this->account_id, $user_id, $is_admin, $start_date, $end_date);
	}

	/**
	* Prints a message notifying the user that their reservation was placed
	* @param string $verb action word of what kind of reservation process just occcured
	*/
	function printAccountSuccess($verb) {
		echo '<script language="JavaScript" type="text/javascript">' . "\n"
			. 'window.opener.document.location.href = window.opener.document.URL;' . "\n"
			. '</script>';
		$date_text = '';
		CmnFns::do_message_box('Your account was successfully ' . $verb
			. '<br/><a href="javascript: window.close();">' . translate('Close') . '</a>', 'width: 90%;');
	}

	/**
	* Check if a user has permission to use a account
	* @param object $user object for this reservations user
	* @param bool whether to kill the app if the user does not have permission
	* @return bool whether user has permission to use resource
	*/
	function checkPerms(&$user, $kill = true) {
		global $auth;
		$current_id = $auth->getCurrentID();
		
		// Admin always has permission
		if ($auth->isAdmin())
		   return true;
		
		// Check user is allowed to modify this reservation
		if ( ($current_id == null) || ($user->get_id() != $current_id) ) {
		   $has_perm = false;
		}
		else {
		   $has_perm = $user->has_perm($this->account_id); // Get user permissions
		}

		if (!$has_perm)
		   CmnFns::do_error_box(
				   translate('You do not have permission to use this account.')
				   , 'width: 90%;'
				   , $kill);

		return $has_perm;
	}

	/**
	* Prints out the account table
	* @param none
	*/
	function printAccount($new = false) {
		global $auth;
		$edit = false;

		if (!$new) {
			$rs = $this->db->get_account_data($this->account_id);

			if ( $auth->isAdmin() === true) {
				$edit = true;
			}

			//printAccountTitle($rs);

			if ($edit) {
				$users = $auth->getUserList();
				printAccountEdit($rs, $users, $edit);
				printAccountButtonsHidden($this);
			} else {
				echo "Sorry, you do not have permission to view/edit this account.";
			}
		} else {
				$users = $auth->getUserList();
				printAccountEdit(NULL, $users, $edit);
		}
	}

	/*
	 * Static function to get public data of an account
	 * @param string account id
	 * @return array of account data
	 */
	function getAccountData($pager = NULL){
		return $this->db->get_account_data($this->account_id, $pager);
	}

	function getAccountId(){
		return $this->account_id;
	}

	function getName(){
		return $this->name;
	}

	function getAccountUsers(){
		return $this->db->get_account_users($this->account_id);
	}

	function getAccountAdmins() {
		return $this->db->get_account_admins($this->account_id);
	}

	function emailAccountAdmins($status) {
		$admins = $this->getAccountAdmins();
		$to = '';
		foreach ($admins as $admin) {
			$to .= $admin['email'].", ";
		}
		$subject = "--IMPORTANT-- Your NanoCenter Billing Account Status Has Changed";
		$message = "
			<html>
			<head>
			  <title>Your NanoCenter Billing Account Status Has Changed</title>
			<head>
			<body>
				<p>Hi, <br />
				  You are receiving this email because you are listed as an admin user
				  on the following NanoCenter account: <strong>" . $this->kfs . "</strong>
				</p>

				<p>The status of this account has changed to
		";
		if ($status==1) {
			$message .= " <font color='#900'><strong>inactive</strong></font>.";
		} else if ($status == 0) {
			$message .= " <font color='#009'><strong>active</strong></font>.";
		}
		$message .= "
				</p>

				<p>
				If you feel this is an error, please do not reply to this message.
				Please contact Alice Mobaidin in the NanoCenter
				Office at 301-405-6047 or at <a href='mailto:mobaidin@umd.edu'>mobaidin@umd.edu</a>.
				</p>

				<p>
				Regards, <br />
				Maryland NanoCenter Scheduler
				</p>
			</body>
			</html>
		";
		$headers ='MIME-Version: 1.0' . "\r\n" .
							'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
							'From: nanocenter@umd.edu' . "\r\n" .
							'Reply-To: no-reply@umd.edu' . "\r\n";
		mail($to, $subject, $message, $headers);
	}

	function isValid(){
		return $this->is_valid;
	}

	function isPermitted($user_id){
		for($i = 0; $i < count($this->users); $i++){
			if(in_array($user_id, $this->users[$i]))
				return true;
		}
		return false;
		//return in_array($user_id, $this->users);
	}

	function isAdmin($user_id){
		return $this->db->get_is_admin($this->account_id, $user_id);
	}

	function getField($field){
		return $this->$field;
	}

	/**
	* Whether there were errors processing this reservation or not
	* @return int if there were errors or not processing this reservation
	*/
	function hasErrors() {
		return count($this->errors) > 0;
	}

	/**
	* Add an error message to the array of errors
	* @param string $msg message to add
	*/
	function addError($msg) {
		array_push($this->errors, $msg);
	}

	/**
	* Return the last error message generated
	* @return string the last error message
	*/
	function getLastError() {
		if ($this->hasErrors())
			return $this->getLastError();
		else
			return null;
	}

	/**
	* Prints out all the error messages in an error box
	* @param boolean $kill whether to kill the app after printing messages
	*/
	function printAllErrors($kill) {
		if ($this->hasErrors()) {
			$div = '<hr size="1"/>';
			CmnFns::do_error_box(
				'<a href="javascript: history.back();">' . translate('Please go back and correct any errors.') . '</a><br /><br />' . join($div, $this->errors) . '<br /><br /><a href="javascript: history.back();">' . translate('Please go back and correct any errors.') . '</a>'
				, 'width: 90%;'
				, $kill);
			}
	}

	/**
	 *
	 * @return mixed Array of billing or false if not account/system admin
	 */
	function getBillingData() {
		global $auth;
		
		if($this->isAdmin($auth->getCurrentID()) || $auth->isAdmin()){
			return $this->db->get_billing_data($this->getAccountId());
		}else{
			return false;
		}
	}
}
