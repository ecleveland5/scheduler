<?php
/**
* This file contains the User class for viewing
*  and manipulating user data
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 07-12-05
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Base directory of application
*/
@define('BASE_DIR', dirname(__FILE__) . '/..');
/**
* UserDB class
*/
include_once('db/UserDB.class.php');

class User {
	var $db;
	var $userid;
	var $umd_uid;
	var $username;
	var $email;
	var $salutation;
	var $first_name;
	var $last_name;
	var $rank;
	var $cell_phone;
	var $home_phone;
	var $work_title;
	var $work_address;
	var $work_address2;
	var $work_city;
	var $work_state;
	var $work_country;
	var $work_zip;
	var $timestamp_added;
	var $last_login;
	var $university;
	var $biography;
	var $affiliations;
	var $visibility;
	var $webpage;
	var $organization;
	var $department;
	var $advisor;
	var $comments;
	var $ps;
	var $type_id;
	var $exec_type_id;
	var $research_interests;
	var $password;
	var $rights;
	var $department_id;
	var $relationship;
	var $supervisor;
	var $register_status;
	var $intranet_access;
	var $receive_announcements;
	var $publish_email_on_site;
	var $is_collaborator;
	var $collaboration_info;
	var $login_count;
    var $work_phone;
    var $inst;
    var $position;
    var $perms;
	var $emails;
	var $logon_name;
	var $is_admin;
	var $lab_pref;
	var $researcher_id;
	var $orcid;
	var $cnst_member;
	var $is_valid = false;
	var $err_msg = null;

	/**
	* Sets the userid variable
	* @param string $userid users id
	*/
	function __construct($userid = null) {
		$this->userid = $userid;
		$this->db = new UserDB();
		
		if (!empty($this->userid)) {		// Load values
			$this->load_by_id();
		}
	}
	
	/**
	* Returns all data associated with this user's profile
	*  using their ID as the identifier
	* @param none
	* @return array of user data
	*/
	function load_by_id() {
		$u = $this->db->get_user_data($this->userid);
		//var_dump ($u);
		if (!$u) {
			$this->err_msg = $this->db->get_err();
			return;
		}	else {
			$this->is_valid = true;
		}
		
		$this->salutation 	= $u['salutation'];
		$this->first_name		= $u['first_name'];
		$this->last_name		= $u['last_name'];
		$this->umd_uid		 	= $u['umd_uid'];
		$this->email				= $u['email'];
		$this->rank					= $u['rank'];
		$this->cell_phone		= $u['cell_phone'];
		$this->home_phone		= $u['home_phone'];
		$this->work_title		= $u['work_title'];
		$this->work_address	= $u['work_address'];
		$this->work_address2= $u['work_address2'];
		$this->work_city		= $u['work_city'];
		$this->work_state		= $u['work_state'];
		$this->work_country	= $u['work_country'];
		$this->work_zip			= $u['work_zip'];
		$this->timestamp_added	= $u['timestamp_added'];
		$this->last_login		= $u['last_login'];
		$this->university		= $u['university'];
		$this->biography		= $u['biography'];
		$this->affiliations	= $u['affiliations'];
		$this->visibility		= $u['visibility'];
		$this->webpage			= $u['webpage'];
		$this->organization	= $u['organization'];
		$this->department		= $u['department'];
		$this->advisor			= $u['advisor'];
		$this->comments			= $u['comments'];
		$this->ps						= $u['p/s'];
		$this->type_id			= $u['type_id'];
		$this->exec_type_id	= $u['exec_type_id'];
		$this->research_interests	= $u['research_interests'];
		$this->password			= $u['password'];
		$this->rights				= $u['rights'];
		$this->department_id= $u['department_id'];
		$this->relationship	= $u['relationship'];
		$this->supervisor		= $u['supervisor'];
		$this->register_status	= $u['register_status'];
		$this->intranet_access	= $u['intranet_access'];
		$this->receive_announcements	= $u['receive_announcements'];
		$this->publish_email_on_site	= $u['publish_email_on_site'];
		$this->is_collaborator	= $u['is_collaborator'];
		$this->collaboration_info	= $u['collaboration_info'];
		$this->login_count	= $u['login_count'];
		$this->work_phone		= $u['work_phone'];
		$this->inst					= $u['institution'];
		$this->logon_name 	= (isset($u['logon_name']) ? $u['logon_name'] : '');
		$this->is_admin 		= $u['is_admin'];
		$this->lab_pref			= $u['lab_pref'];
		$this->researcher_id= $u['researcher_id'];
		$this->orcid				= (isset($u['orcid']) ? $u['orcid'] : '');
		$this->cnst_member	= $u['cnst_member'];
		$this->perms	 			= $this->get_perms();
		$this->emails 			= $this->get_emails();
		unset($u);
	}
	
	/**
	* Returns all permissions for this user
	* @param none
	* @return array of user permissions with the resource id as the key and 1 as the value
	*/
	function get_perms() {
		global $conf;
		return ($conf['app']['use_perms'] ? $this->db->get_user_perms($this->userid) : array());
	}
	
	/**
	* Checks if the user has permission to use a resource
	* @param string $machid id of resource to check
	* @return boolean whether user has permission or not
	*/
	function has_perm($machid) {
		global $conf;
		return ($conf['app']['use_perms'] ? isset($this->perms[$machid]) : true);
	}
	
	/**
	* Gets the email contact setup for this user
	* @param none
	* @return array of email settings
	*/
	function get_emails() {	
		if (!$emails = $this->db->get_emails($this->userid))
			$this->err_msg = $this->db->get_err();
		return $emails;
	}
	
	/**
	* Returns whether the user wants the type of email contact or not
	* @param string $type email contact type.
	*  Valid types are 'e_add', 'e_mod', 'e_del' for adding/modifying/deleting reservations, respectively
	* @return boolean whether user wants the email or not
	*/
	function wants_email($type) {
		return ($this->emails[$type] == 'y');
	}
	
	/**
	 * Whether the user wants html or plain text emails
	 * @return bool whether they want html email or not
	 */
	function wants_html() {
		return ($this->emails['e_html'] == 'y');
	}
	
	
	/**
 	* The lab preference of the user
 	* @return string lab id of the preferred lab
 	*/
	function get_lab_pref() {
		return ($this->lab_pref);
	}
	
	/**
	* Sets the users email preferences
	* @param string $e_add value to set e_add field to
	* @param string $e_mod value to set e_mod field to
	* @param string $e_del value to set e_del field to
  * @param string $e_app value to set e_app field to
	* @param string $e_html value to set e_html field to
	* @param int $lab_pref value to set lab_pref field to
	*/
	function set_emails($e_add, $e_mod, $e_del, $e_app, $e_html, $lab_pref) {
		$this->db->set_emails($e_add, $e_mod, $e_del, $e_app, $e_html, $lab_pref, $this->userid);
	}
	
	/**
	 * Return all user data in an array
	 * @return array array of all user data
	 */
	function get_user_data() {
		return array (
				'user_id' 							=> $this->userid,
				'salutation'						=> $this->salutation,
				'umd_uid'								=> $this->umd_uid,
				'email'									=> $this->email,
				'first_name'						=> $this->first_name,
				'last_name'							=> $this->last_name,
				'rank'									=> $this->rank,
				'work_phone'						=> $this->work_phone,
				'work_title'						=> $this->work_title,
				'work_address'					=> $this->work_address,
				'work_address2'					=> $this->work_address2,
				'work_city'							=> $this->work_city,
				'work_state'						=> $this->work_state,
				'work_country'					=> $this->work_country,
				'work_zip'							=> $this->work_zip,
				'timestamp_added'				=> $this->timestamp_added,
				'last_login'						=> $this->last_login,
				'university'						=> $this->university,
				'biography'							=> $this->biography,
				'affiliations'					=> $this->affiliations,
				'visibility'						=> $this->visibility,
				'webpage'								=> $this->webpage,
				'organization'					=> $this->organization,
				'department'						=> $this->department,
				'advisor'								=> $this->advisor,
				'comments'							=> $this->comments,
				'ps'										=> $this->ps,
				'type_id'								=> $this->type_id,
				'exec_type_id'					=> $this->exec_type_id,
				'research_interests'		=> $this->research_interests,
				'password'							=> $this->password,
				'rights'								=> $this->rights,
				'department_id'					=> $this->department_id,
				'relationship'					=> $this->relationship,
				'supervisor'						=> $this->supervisor,
				'register_status'				=> $this->register_status,
				'intranet_access'				=> $this->intranet_access,
				'receive_announcements'	=> $this->receive_announcements,
				'publish_email_on_site'	=> $this->publish_email_on_site,
				'is_collaborator'				=> $this->is_collaborator,
				'collaboration_info'		=> $this->collaboration_info,
				'login_count'						=> $this->login_count,
				'institution'						=> $this->inst,
				'position'							=> $this->position,
				'perms'									=> $this->perms,
				'logon_name'						=> $this->logon_name,
				'is_admin'							=> $this->is_admin,
				'lab_pref'							=> $this->lab_pref,
				'researcher_id'					=> $this->researcher_id,
				'orcid'									=> $this->orcid,
				'cnst_member'						=> $this->cnst_member
			);
	}
	
	/**
	 * Returns an array of attributes based on the array of field names.
	 * @param array $attr_list array of field names.
	 * @return array
	 */
	function get_user_attribute($attr_list = array()) {
		$return = array();
		if (!empty($attr_list) && is_array($attr_list)) {
			foreach($attr_list as $attr) {
				if (isset($this->{$attr})) {
					$return[$attr] = $this->{$attr};
				} else {
					$return[$attr] = '';
				}
			}
		}
		return $return;
	}
	
	/**
	* Sets a users password
	* @param string $new_password the new password to set for this user
	*/
	function set_password($new_password) {
		$this->db->set_password($new_password, $this->userid);
	}
	
	/**
	* Returns whether this user is valid or not
	* @param none
	* @return boolean if user is valid or not
	*/
	function is_valid() {
		return $this->is_valid;
	}
	
	/**
	* Returns the error message generated
	* @param none
	* @return error message as string
	*/
	function get_error() {
		return $this->err_msg;
	}
	
	/**
	* Return this user's id
	* @param none
	* @return user id
	*/
	function get_id() {
		return $this->userid;
	}
	
	/**
	* Return the users first name
	* @param none
	* @return user first name
	*/
	function get_first_name() {
		return $this->first_name;
	}
	
	/**
	* Return the users last name
	* @param none
	* @return user last name
	*/
	function get_last_name() {
		return $this->last_name;
	}
	
	/**
	* Return the user's full name
	* @param none
	* @return the users full name as one string
	*/
	function get_name($lastFirst=FALSE, $show_salutation=FALSE) {
		$return = '';
		if (!empty($this->salutation) && $show_salutation) {
			$return .= $this->salutation.' ';
		}
		if ($lastFirst) {
			$return .= $this->last_name . ', ' . $this->first_name;
		}else{
			$return .= $this->first_name . ' ' . $this->last_name;
		}
		return $return;
	}
	
	/**
	* Returns the email address
	* @param none
	* @return email address of this user
	*/
	function get_email() {
		return $this->email;
	}
	
	/**
	* Returns user's phone
	* @param none
	* @return user's phone number as string
	*/
	function get_phone() {
		return $this->work_phone;
	}
	
	/**
	* Returns the users institution
	* @param none
	* @return user's institution
	*/
	function get_inst() {
		return $this->inst;
	}
	
	/**
	* Returns the user's position
	* @param none
	* @return user's position
	*/
	function get_position() {
		return $this->position;
	}
	
	/**
	* Returns the user's logon_name
	* @param none
	* @return user's logon_name
	*/
	function get_logon_name() {
		return $this->logon_name;
	}

	/**
	* Returns the user's type id
	* @param none
	* @return user's type id
	*/
	function get_type_id() {
		return $this->type_id;
	}
	
	/**
	 * Returns the user's type
	 */
	public function get_user_type() {
		return $this->db->get_user_type($this->type_id);
	}

	/**
	* Returns if the user has admin privleges or not
	* @param none
	* @return bool if they have admin rights
	*/
	function get_isadmin() {
		return $this->is_admin;
	}

	/**
	* Returns a list of accounts this user has access to
	* @param none
	*/
	function get_accounts_list() {
		return $this->db->get_accounts_list($this->userid);
	}

	/**
	* Returns a list of labs this user is an admin for
	* @param none
	*/
	function get_admin_lab_list() {
		return $this->db->get_admin_lab_list($this->userid);
	}

	/**
	* Returns if the user has admin privleges or not
	* @param none
	* @return bool if they have admin rights
	*/
	function isLabAdmin($lab_id) {
		return $this->db->isLabAdmin($lab_id);
	}
	
	/**
	 * Returns the total number of reservations this user
	 * has in the database
	 * @return int number of records
	 */
	function get_num_reservations() {
		return $this->db->get_num_reservations($this->userid);
	}
	
	/**
	 * Returns the reservation data for this user
	 * @param Object $pager pager object
	 * @param array $orders order the results should be sorted in
	 * @return array of all resource data
	 */
	function get_my_reservation_data(&$pager, $orders) {
		return $this->db->get_my_reservation_data($this->get_id(), $pager, $orders);
	}
	
	/**
	 * Records when a user logs in
	 * 
	 */
	function record_login() {
		$this->db->record_login($this->get_id());
	}
	
	/**
	 * 
	 */
	public function get_advisor() {
		if ($advisor = $this->db->get_user_data($this->advisor)) {
			return $advisor;
		} else {
			return false;
		}
	}

    public function get_lab_permissions() {
        return $this->db->get_lab_permissions($this->userid);
    }

    public function get_resource_filters($lab_id) {
        // check for user cookies
        if (is_array($_COOKIE['resource_filters']) && !empty($_COOKIE['resource_filters'])) {
            return $_COOKIE['resource_filters'];
        }
        return $this->db->get_resource_filters($lab_id);
    }

    public function add_user_resource_filter($machid) {
        $this->db->add_user_resource_filter($machid, $this->userid);
    }

    public function remove_user_resource_filter($machid) {
        $this->db->remove_user_resource_filter($machid, $this->userid);
    }
}
?>