<?php
/**
* Billing class
* Provides access to billing data
* @author Ernie Cleveland <eclevela@umd.edu>
*/
/**
* Base directory of application
*/
@define('BASE_DIR', dirname(__FILE__) . '/..');
/**
* ResDB class
*/
include_once('db/BillingDB.class.php');
include_once(BASE_DIR . '/templates/billing.template.php');

class Billing {
	var $id			= NULL;
	var $usage_id	= NULL;
	var $amount		= NULL;
	var $description= NULL;
	var $account_id	= NULL;
	var $entry_date	= NULL;

	var $sent		= NULL;
	var $paid		= NULL;
	
	
	/**
	* Billing constructor
	* Sets id (if applicable)
	* Sets the account status type
	* @param string $id id of account to load
	*/
	function Billing($id = null) {
		$this->db = new BillingDB();
		
		if (!empty($id)) {
			$this->id = $id;
			$this->load_by_id();
		}
	}

	/**
	* Loads all account properties from the database
	* @param none
	*/
	function load_by_id() {
		$billing = $this->db->get_billing_data($this->id);	// Get values from DB
		
		if (!$billing) {
			$this->err_msg = $this->db->get_err();
			return;
		}
		else
			$this->is_valid = true;
		
		
	}



	/**
	* 
	*
	**/
	function add_bill_data($id, $data) {
		$this->id	= $id;

		if ($this->has_errors()){			// Print any errors generated above and kill app
			$this->print_all_errors(true);
		}else{		
			$this->print_success('created', $dates);
		}
	}



	/**
	* Prints a message nofiying the user that their reservation was placed
	* @param string $verb action word of what kind of reservation process just occcured
	* @param array $dates dates that were added or modified.  Deletions are not printed.
	*/
	function print_success($verb) {
		echo '<script language="JavaScript" type="text/javascript">' . "\n"
			. 'window.opener.document.location.href = window.opener.document.URL;' . "\n"
			. '</script>';
		$date_text = '';
		for ($i = 0; $i < count($dates); $i++) {
			$date_text .= CmnFns::formatDate($dates[$i]) . '<br/>';
		}
		CmnFns::do_message_box('Your ' . $this->word . ' was successfully ' . $verb
					//. (($this->type != 'd') ? ' ' . translate('for the follwing dates') . '<br /><br />' : '.')
					//. $date_text . '<br/>'
					. '<br/><a href="javascript: window.close();">' . translate('Close') . '</a>'
					, 'width: 90%;');
	}



	/**
	* Check if a user has permission to use a account
	* @param object $user object for this reservations user
	* @param bool whether to kill the app if the user does not have permission
	* @return whether user has permission to use resource
	*/
	function check_perms(&$user, $kill = true) {
		if (Auth::isAdmin())                    // Admin always has permission
		   return true;
		
		if ((Auth::getCurrentID() == null) || ($user->get_id() != Auth::getCurrentID())) {
		   $has_perm = false;                    // Check user is allowed to modify this reservation
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
	function print_billing() {
		global $conf;
		
		$is_private = $conf['app']['privacyMode'] && !Auth::isAdmin();
		
		if (!Auth::isAdmin() && !$this->is_blackout && intval($this->start_date) < intval(mktime(0,0,0, date('m'), date('d') + $this->sched['dayOffset'])) )  {
			$this->type = RES_TYPE_VIEW;
		}
		if (Auth::getCurrentID() != $this->user_id && !Auth::isAdmin()) { $this->type = RES_TYPE_VIEW; };

		$rs = $this->db->get_account_data($this->account_id);
		if ($this->type == RES_TYPE_ADD && $rs['approval'] == 1) {
			$this->is_pending = true;		// On the initial add, make sure that the is_pending flag is correct
		}
		print_title($rs);
		begin_account_form($this->type == RES_TYPE_ADD, $this->is_blackout);
		begin_container();
		print_basic_panel($this, $rs, $is_private);		// Contains resource/user info, time select, summary, repeat boxes
		if ($this->is_blackout || $is_private) {
			print_advanced_panel($this, null, null, null, false);	// No advanced for either case
		}
		else {
			print_advanced_panel($this, $this->db->get_table_data('user', array('first_name','last_name','user_id','email'), array('last_name','first_name')), (($this->user_id == Auth::getCurrentID() || Auth::isAdmin()) && $this->type != RES_TYPE_VIEW) );
		}
		end_container();
		print_buttons_and_hidden($this);
		end_reserve_form();
		print_jscalendar_setup($this, $rs);
	}


	function get_account_data($account_id){
		return $this->db->get_account_data($account_id);
	}

}
?>