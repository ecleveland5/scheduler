<?php
	/**
	 * Reservation class
	 * Provides access to reservation data
	 * @author Nick Korbel <lqqkout13@users.sourceforge.net>
	 * @author David Poole <David.Poole@fccc.edu>
	 * @author Ernie Cleveland <eclevela@umd.edu>
	 * @version 08-18-05
	 * @package phpScheduleIt
	 *
	 * Copyright (C) 2003 - 2005 phpScheduleIt
	 * Copyright (C) 2005 - 2018 University of Maryland
	 * License: GPL, see LICENSE
	 */
	
	/**
	 * Base directory of application
	 */
	//@define('BASE_DIR', dirname(__FILE__) . '/..');
	/**
	 * ResDB class
	 */
	include_once('db/ResDB.class.php');
	/**
	 * User class
	 */
	include_once('User.class.php');
	/**
	 * Account class
	 */
	
	include_once('Account.class.php');
	/**
	 * PHPMailer
	 */
	include_once('PHPMailer.class.php');
	/**
	 * Reservation templates
	 */
	include_once(BASE_DIR . '/templates/reserve.template.php');
	
	class Reservation {
		var $id;
		var $start_date;
		var $end_date;
		var $start_time;
		var $end_time;
		var $resource_id;
		var $user_id;
		var $created;
		var $modified;
		var $type;
		var $is_repeat;
		var $repeat;
		var $min_reservation_time;
		var $max_reservation_time;
		var $parent_id;
		var $is_blackout;
		var $is_pending;
		var $summary;
		var $lab_id;
		var $lab_data;
		var $users;
		var $sign_in;
		var $sign_out;
		var $account_id;
		var $technical_note;
		var $billing_note;
		var $billing_rate;
		var $deleted;
		var $deleted_by;
		var $deleted_by_email;
		var $errors;
		var $word;
		var $db;
		var $resource;
		
		/**
		 * Reservation constructor
		 * Sets id (if applicable)
		 * Sets the reservation action type
		 * Sets the database reference
		 * @param string $id id of reservation to load, null if new reservation
		 * @param bool $is_blackout true if this is a blackout reservation type
		 * @param bool $is_pending true if this is a pending reservation
		 * @param string $lab_id id of the lab to which this reservation belongs.
		 */
		function __construct($id = null, $is_blackout = false, $is_pending = false, $lab_id = null) {
			$this->db = new ResDB();
			
			if (!empty($id)) {
				$this->id = $id;
				$this->loadById();
			} else {
				$this->is_blackout = $is_blackout;
				$this->is_pending = $is_pending;
				$this->lab_id = $lab_id;
			}
			
			$this->word = $is_blackout ? 'blackout' : 'reservation';
			$this->lab_data = $this->db->get_lab_data($this->lab_id);
			$this->errors = array();
		}
		
		/**
		 * Loads all reservation properties from the database
		 * @param none
		 */
		function loadById() {
			$res = $this->db->get_reservation($this->id);	// Get values from DB
			
			if (!$res)		// Quit if reservation doesnt exist
				CmnFns::do_error_box($this->db->get_err());
			
			$this->start_date       = $res['start_date'];
			$this->end_date         = $res['end_date'];
			$this->start_time       = $res['startTime'];
			$this->end_time         = $res['endTime'];
			$this->resource_id      = $res['machid'];
			$this->created	        = $res['created'];
			$this->modified         = $res['modified'];
			$this->deleted          = $res['deleted_tstamp'];
			$this->deleted_by       = $res['deleted_by'];
			$this->deleted_by_email = $res['deleted_by_email'];
			$this->parent_id        = $res['parentid'];
			$this->summary	        = $res['summary'];
			$this->lab_id	        = $res['lab_id'];
			$this->is_blackout	    = $res['is_blackout'];
			$this->is_pending	    = $res['is_pending'];
			$this->account_id	    = $res['account_id'];
			$this->technical_note	= $res['technical_note'];
			$this->billing_note	    = $res['billing_note'];
			//$this->billing_rate     = $res['billing_rate'];
			$this->lab_data         = $this->db->get_lab_data($this->lab_id);
			$this->resource         = $this->db->get_equipment_data($this->resource_id);
			//$this->resource         = new Equipment($this->mach_id);
			$this->users = $this->db->get_res_users($this->id);
			
			for ($i = 0; $i < count($this->users); $i++) {
				if ($this->users[$i]['owner'] == 1) {
					$this->user_id = $this->users[$i]['user_id'];
					break;
				}
			}
		}
		
		
		/**
		 * Checks if the reservation is deletable based on various criteria
		 * @return bool
		 */
		function isDeletable() {
			global $auth;
			
			if ( ($this->start_date-(60*60*24) > time()) || $auth->isAdmin() ) {
				return true;
			}
			return false;
		}
		
		/**
		 * Deletes the current reservation from the database
		 * If this is a recurring reservation, it may delete all reservations in group
		 * @param boolean $del_recur whether to delete all recurring reservations in this group
		 */
		function deleteReservation($del_recur) {
			//$this->load_by_id();
			$this->type = RES_TYPE_DELETE;
			
			$this->is_repeat = $del_recur;
			
			$user = new User($this->user_id);		// Set up User object
			$this->checkResourcePermissions($user);				// Make sure they are who they claim to be
			
			$users_to_inform = array();				// Notify all users that this reservation is being deleted
			for ($i = 0; $i < count($this->users); $i++) {
				$users_to_inform[] = $this->users[$i]['email'];
			}
			
			$this->db->del_res($this->id, $this->parent_id, $del_recur, mktime(0,0,0));
			
			// Mail the user if they want to be notified
			if (!$this->is_blackout) {
				$this->sendEmail('e_del', $user, null, $users_to_inform);
			}
			
			CmnFns::write_log($this->word . ' ' . $this->id . ' deleted.', $this->user_id, $_SERVER['REMOTE_ADDR']);
			
			if ($this->is_repeat) {
				CmnFns::write_log('All ' . $this->word . 's associated with ' . $this->id . ' (having parentid ' . $this->parent_id . ') were also deleted', $this->user_id, $_SERVER['REMOTE_ADDR']);
			}
			
			$this->printReservationSuccess('deleted');
		}
		
		/**
		 * Add a new reservation to the database
		 *  after verifying that user has permission
		 *  and the time is available
		 * @param string $mach_id id of resource to reserve
		 * @param array $user_info array of users and their associated properties of this reservation
		 * @param string $owner_id id of reservation owner/creator
		 * @param int $start_date datestamp of the starting date
		 * @param int $end_date datestamp of the ending date
		 * @param int $start starting time of reservation
		 * @param int $end ending time of reservation
		 * @param array $repeat repeat reservation values
		 * @param int $min minimum reservation time
		 * @param int $max maximum reservation time
		 * @param string $summary reservation summary
		 * @param string $lab_id id of lab to make reservation on
		 * @param string $account_id id of account to be used for billing
		 */
		function addReservation($mach_id, $user_info, $owner_id, $start_date, $end_date, $start, $end, $repeat, $min, $max, $summary, $lab_id, $account_id) {
			$this->resource_id		= $mach_id;
			$this->user_id 		    = $owner_id;
			$this->start_date 	    = $start_date;
			$this->end_date 	    = $end_date;
			$this->start_time		= $start;
			$this->end_time			= $end;
			$this->repeat 		    = $repeat;
			$this->type     	    = RES_TYPE_ADD;
			$this->summary		    = $summary;
			$this->lab_id 		    = $lab_id;
			$this->account_id 	    = $account_id;
			$this->billing_note     = '';
			$this->technical_note   = '';
			
			// Store the original dates because they will be changed if we repeat
			$orig_start_date 	    = $this->start_date;
			$orig_end_date 		    = $this->end_date;
			$accept_code 		    = $this->db->get_new_id();
			$dates 				    = array();
			$tmp_valid 			    = false;
			$tmp_date               = $start_date;
			
			if (!$this->is_blackout) {
				$user = new User($this->user_id);	// Set up a new User object
				$this->checkResourcePermissions($user);		// Only need to check once
				$this->checkMinMax($min, $max);
			}
			$this->checkReservationTimes();
			
			// Print any errors generated above and kill app
			if ($this->hasErrors()) {
				$this->printAllErrors(true);
			}
			
			// First valid reservation will be parentid (no parentid if solo reservation)
			$is_parent = $this->is_repeat;
			
			for ($i = 0; $i < count($repeat); $i++) {
				$this->start_date = $repeat[$i];
				if ($this->is_repeat) {
					// End date will always be the same as the start date for recurring reservations
					$this->end_date = $this->start_date;
				}
				
				// Store the original date to use in the email
				if ($i == 0) {
					$tmp_date = $this->start_date;
				}
				
				$is_valid = $this->checkReservationTime();
				
				if ($is_valid) {
					
					// Only one recurring needs to work
					$tmp_valid = true;
					
					// Send to Database!!!
					$this->id = $this->db->add_res($this, $is_parent, $user_info, $accept_code);
					
					if (!$this->is_blackout) {		// Notify the user if they want (only 1 email)
						$this->lab_data = $this->db->get_lab_data($this->lab_id);
						$this->sendEmail('e_add', $user, $dates);
					}
					
					/*
					// Send out invites, if needed
					if (!$this->is_pending && count($userinfo) > 0) {
						$this->invite_users($userinfo, $dates, $user, $accept_code);
					}
					*/
					
					if (!$is_parent) {
						// Add recurring dates (first date isn't recurring)
						array_push($dates, $this->start_date);
					} else {
						// The first reservation is the parent id
						$this->parent_id = $this->id;
					}
					CmnFns::write_log($this->word . ' ' . $this->id . ' added.  machid:' . $this->resource_id .
						', dates:' . $this->start_date . ' - ' . $this->end_date . ', start:' . $this->start_time . ', end:' .
						$this->end_time, $this->user_id, $_SERVER['REMOTE_ADDR']);
				}
				// Parent has already been stored
				$is_parent = false;
			}
			
			// Print any errors generated when adding the reservations
			if ($this->hasErrors()) {
				$this->printAllErrors(!$this->is_repeat);
			}
			
			// Restore first date for use in email
			$this->start_date = $tmp_date;
			if ($this->is_repeat) {
				// Add to list of successful dates
				array_unshift($dates, $this->start_date);
			}
			
			sort($dates);
			
			// Restore original reservation dates
			$this->start_date = $orig_start_date;
			$this->end_date = $orig_end_date;
			
			if (!$this->is_repeat || $tmp_valid) {
				$this->printReservationSuccess('created', $dates);
			}
		}
		
		/**
		 * Modifies a current reservation, setting new start and end times
		 *  or deleting it
		 * @param string $owner_id the user_id of the reservation owner
		 * @param array $user_info array of users and their associated properties of this reservation
		 * @param array $removed_users array of users who will be removed from this reservation
		 * @param int $start_date datestamp of the starting date
		 * @param int $end_date datestamp of the ending date
		 * @param int $start new start time
		 * @param int $end new end time
		 * @param bool $del whether to delete it or not
		 * @param int $min minimum reservation time
		 * @param int $max maximum reservation time
		 * @param boolean $mod_recur whether to modify all recurring reservations in this group
		 * @param string $summary reservation summary
		 */
		function modifyReservation($owner_id, $user_info, $removed_users, $start_date, $end_date, $start, $end, $del, $min, $max, $mod_recur, $account_id, $summary = null) {
			$recurs = array();
			
			$this->loadById();			// Load reservation data
			$this->type = RES_TYPE_MODIFY;
			$this->summary = $summary;
			$this->start_date = $start_date;
			$this->end_date = $end_date;
			$this->start_time = $start;			// Assign new start and end times
			$this->end_time	 = $end;
			$this->user_id = $owner_id;
			$this->account_id = $account_id;
			
			$orig_start_date = $this->start_date;		// Store the original dates because they will be changed if we repeat
			$orig_end_date = $this->end_date;
			
			$accept_code = $this->db->get_new_id();
			
			if ($del) {						// First, check if this should be deleted
				$this->deleteReservation($mod_recur);
				return;
			}
			
			// Store arrays of users that need to be added and removed
			$users_to_add = $users_to_remove = $post_users = $unchanged_users = array();
			$email_to_add = $email_to_remove = array();
			
			for ($i = 0; $i < count($user_info); $i++) {
				$info = explode('|', $user_info[$i]);
				$users_to_add[] = $info[0];
				$email_to_add[] = $user_info[$i];
				$post_users[] = $info[0];
			}
			
			for ($i = 0; $i < count($this->users); $i++) {
				if ($this->users[$i]['owner'] == 1 || $this->users[$i]['invited'] != 1) { continue; }	// We dont add or remove the owner or any participating users
				$users_to_remove[$i] = $userid = $this->users[$i]['user_id'];
				$email_to_remove[$i] = $this->users[$i]['email'];
				for ($j = 0; $j < count($post_users); $j++) {
					if ($userid == $post_users[$j]) {
						unset($users_to_remove[$i]);	// This user is still there, so dont remove them
						unset($email_to_remove[$i]);
						unset($users_to_add[$j]);		// User is already there, so no need to add them
						$unchanged_users[] = $email_to_add[$j];	// We need to tell this user about the reservation mod
						unset($email_to_add[$j]);
						break;
					}
				}
			}
			
			// Append all of the explicit 'remove from reservation' users
			for ($i = 0; $i < count($removed_users); $i++) {
				list($user_id, $email) = explode('|', $removed_users[$i]);
				$users_to_remove[] = $user_id;
				$email_to_remove[] = $email;
			}
			
			if (!$this->is_blackout) {
				$user = new User($this->user_id);		// Set up a User object
				$this->checkResourcePermissions($user);		    // Check permissions
				$this->checkMinMax($min, $max);		// Check min/max reservation times
			}
			
			$this->checkReservationTimes();			// Check valid times
			
			$this->is_repeat = $mod_recur;	// If the mod_recur flag is set, it must be a recurring reservation
			$dates = array();
			
			// First, modify the current reservation
			if ($this->hasErrors())        // Print any errors generated above and kill app
				$this->printAllErrors(true);
			
			$tmp_valid = false;
			
			if ($this->is_repeat) {         // Check and place all recurring reservations
				$recurs = $this->db->get_recur_ids($this->parent_id, mktime(0,0,0));
				for ($i = 0; $i < count($recurs); $i++) {
					$this->id   = $recurs[$i]['resid'];		// Load reservation data
					$this->start_date = $recurs[$i]['start_date'];
					if ($this->is_repeat) {
						// End date will always be the same as the start date for recurring reservations
						$this->end_date = $this->start_date;
					}
					$is_valid   = $this->checkReservationTime();       // Check overlap (dont kill)
					
					if ($is_valid) {
						$tmp_valid = true;          // Only one recurring needs to pass
						$this->db->mod_res($this, $users_to_add, $users_to_remove, $accept_code, $account_id);		// And place the reservation
						array_push($dates, $this->start_date);
						CmnFns::write_log($this->word . ' ' . $this->id . ' modified.  machid:' . $this->resource_id .', dates:' . $this->start_date . ' - ' . $this->end_date . ', start:' . $this->start_time . ', end:' . $this->end_time, $this->user_id, $_SERVER['REMOTE_ADDR']);
					}
				}
			} else {
				if ($this->checkReservationTime()) {       // Check overlap
					$this->db->mod_res($this, $users_to_add, $users_to_remove, $accept_code, $account_id);		// And place the reservation
					array_push($dates, $this->start_date);
				}
			}
			
			// Restore original reservation dates
			$this->start_date = $orig_start_date;
			$this->end_date = $orig_end_date;
			
			// Print any errors generated when adding the reservations
			if ($this->hasErrors())
				$this->printAllErrors(!$this->is_repeat);
			
			// Notify the user of blackout per their email settings.
			if (!$this->is_blackout) {
				try {
					$this->sendEmail('e_mod', $user, null, $unchanged_users);
				} catch (Exception $e) {
				
				}
			}
			
			// Notify users who were added
			if (!$this->is_pending && count($email_to_add) > 0) {
				$this->inviteUsers($email_to_add, $dates, $user, $accept_code);
			}
			
			// Notify users who were removed
			if (!$this->is_pending && count($email_to_remove) > 0) {
				$this->remove_users_email($email_to_remove, $dates, $user);
			}
			
			// Show reservation modification success
			if (!$this->is_repeat || $tmp_valid)
				$this->printReservationSuccess('modified', $dates);
		}
		
		/**
		 * This allows an admin to submit a technical note about this
		 * reservation for review at later dates.
		 *
		 * @param string $reservation_id The id of the reservation to be edited
		 * @param string $note The note that is to be entered into the database
		 **/
		function addTechnicalNote($reservation_id, $note){
			$this->db->add_technical_note($reservation_id,$note);
		}
		
		
		/**
		 * This allows an admin to submit a billing note about this
		 * reservation for review at later dates.
		 *
		 * @param string $reservation_id The id of the reservation to be edited
		 * @param string $note The note that is to be entered into the database
		 **/
		function addBillingNote($reservation_id, $note){
			$this->db->add_billing_note($reservation_id,$note);
		}
		
		
		/**
		 * Approves reservation and sends out an email to the owner
		 * Any reservation invitations are sent at this point
		 * @param bool $mod_recur if we should update all reservations in this group
		 */
		function approveReservation($mod_recur) {
			
			$this->type = RES_TYPE_APPROVE;
			
			$this->db->approve_res($this, $mod_recur);
			$where = 'WHERE resid = ?';
			$values = array($this->id);
			if ($mod_recur) {
				$where .= ' OR parentid = ?';
				array_push($values, $this->parent_id);
			}
			
			$dates = array();
			$ds = $this->db->get_table_data('reservations', array('start_date'), array('start_date'), null, null, $where, $values);
			for ($d = 0; $d < count($ds); $d++) {
				$dates[] = $ds[$d]['start_date'];
			}
			
			$user = new User($this->user_id);		// Set up a new User object
			
			$this->sendEmail('e_app', $user, $dates);
			
			// Send out invites, if needed
			if (count($this->users) > 0) {
				$accept_code = $this->db->get_new_id();
				$user_info = array();
				for ($i = 0; $i < count($this->users); $i++) {
					if ($this->users[$i]['owner'] != 1) {
						$user_info[] = $this->users[$i]['user_id'] . '|' . $this->users[$i]['email'];
					}
				}
				$this->inviteUsers($user_info, $dates, $user, $accept_code);
			}
			
			$this->printReservationSuccess('approved', $dates);
		}
		
		/**
		 * Signs In a user to their reservation.
		 *
		 *
		 */
		function signInReservation(){
			$this->db->signin_res($this);
			$this->printReservationSuccess('signed in');
		}
		
		
		/**
		 * Signs a user out of their reservation.
		 *
		 * @param $use_desc
		 * @param $notes
		 * @param $prob
		 */
		function signOutReservation($use_desc, $notes, $prob){
			$this->db->signout_res($this, $use_desc, $notes, $prob);
			$this->printReservationSuccess('signed out');
		}
		
		/**
		 * Prints a message nofiying the user that their reservation was placed
		 * @param string $verb action word of what kind of reservation process just occcured
		 * @param array $dates dates that were added or modified.  Deletions are not printed.
		 */
		function printReservationSuccess($verb, $dates = array()) {
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
		 * Checks if the account field was left empty
		 * todo: need to verify user permission on account
		 * @return bool
		 */
		function isAccountValid(){
			
			if($this->getAccountId()==0){
				$this->addError('Please enter an account id.');
				return false;
			}
			
			// todo: $this->account->checkAccountPermissions
			
			return true;
		}
		
		/**
		 * Verify that the user selected appropriate times and dates
		 * @return bool if the times and dates selected are all valid
		 */
		function checkReservationTimes() {
			
			$this->lab_data = $this->db->get_lab_data($this->lab_id);
			
			if ( intval($this->start_date) < intval(mktime(0,0,0, date('m'), date('d') + $this->lab_data['dayOffset'])) )  {
				$dates_valid = false;
				$this->addError(translate('That starting date has already passed'));
			}
			// It is valid if the start date is less than or equal to the end date or (if the dates are equal), the start time is less than the end time
			$is_valid = ( (intval($this->start_date) < intval($this->end_date)) || ( intval($this->start_date) == intval($this->end_date) ) && (intval($this->start_time) < intval($this->end_time)) );
			if (!$is_valid)
				$this->addError(translate('Start time must be less than end time') . '<br /><br />'
					. translate('Current start time is') . ' ' . CmnFns::formatDateTime($this->start_date + 60 * $this->start_time) . '<br />'
					. translate('Current end time is') . ' ' . CmnFns::formatDateTime($this->end_date + 60 * $this->end_time)
				);
			return ($is_valid);
		}
		
		/**
		 * Check to make sure that the reservation falls within the specified reservation length
		 * @param int $min minimum reservation length
		 * @param int $max maximum reservation length
		 * @param bool $kill true if app should die on error
		 * @return bool true if the time is valid
		 */
		function checkMinMax($min, $max, $kill = true) {
			$is_valid = true;
			
			if ($this->start_date < $this->end_date) {
				// calculate total time for multi-day reservations.
				// get # of minutes in first day
				$first_day_minutes = 1440 - $this->start_time;
				$between_minutes = ($this->end_date - $this->start_date - (1440*60))/60;
				$last_day_minutes = $this->end_time;
				$this_length = $first_day_minutes + $between_minutes + $last_day_minutes;
				if ($this_length > $max) {
					$is_valid = false;
				}
			} else {
				$this_length = ($this->end_time - $this->start_time);
				$is_valid = ($this_length >= ($min)) && (($this_length) <= ($max));
			}
			
			if (!$is_valid) {
				$this->addError(translate('Reservation length does not fall within this resource\'s allowed length.') . '<br /><br >'
					. translate('Your reservation is') . ' ' . CmnFns::minutes_to_hours($this_length) . '<br />'
					. translate('Minimum reservation length') . ' ' . CmnFns::minutes_to_hours($min) . '<br />'
					. translate('Maximum reservation length') . ' ' . CmnFns::minutes_to_hours($max)
				);
				CmnFns::do_error_box(
					translate('Reservation length does not fall within this resource\'s allowed length.') . '<br /><br >'
					. translate('Your reservation is') . ' ' . CmnFns::minutes_to_hours($this_length) . '<br />'
					. translate('Minimum reservation length') . ' ' . CmnFns::minutes_to_hours($min) . '<br />'
					. translate('Maximum reservation length') . ' ' . CmnFns::minutes_to_hours($max)
					, 'width: 90%;'
					, $kill);
			}
			
			return $is_valid;
		}
		
		/**
		 * Checks to see if a time is already reserved
		 * @return bool whether the time is reserved or not
		 */
		function checkReservationTime() {
			$is_valid = !($this->db->check_res($this));
			
			if (!$is_valid) {
				$this->addError(translate('reserved or unavailable', array(CmnFns::formatDateTime($this->start_date + (60*$this->start_time)), CmnFns::formatDateTime($this->end_date + (60*$this->end_time)))));
			}else{
				$is_valid = $this->isAccountValid();
				if (!$is_valid) {
					$this->addError("The account used for this reservation is not active at this time.");
				}
			}
			return $is_valid;
		}
		
		/**
		 * Check if a user has permission to use a resource
		 * @param User $user object for this reservations user
		 * @param bool whether to kill the app if the user does not have permission
		 * @return bool whether user has permission to use resource
		 */
		function checkResourcePermissions(&$user, $kill = true) {
			global $auth;
			
			if ($auth->isAdmin()) {
				return true;
			}
			
			// Check user is allowed to modify this reservation
			// Get user permissions
			
			if ($user->hasResourcePermission($this->resource_id)) {
				return true;
			} else {
				CmnFns::do_error_box(
					translate('You do not have permission to use this resource.')
					, 'width: 90%;'
					, $kill);
			}
			return false;
		}
		/**
		 * Prints out the reservation table
		 * @param none
		 */
		function printReservation() {
			global $conf;
			global $auth;
			$is_admin = $auth->isAdmin();
			
			$is_private = $conf['app']['privacyMode'] && !$is_admin;
			
			if (!$is_admin && !$this->is_blackout && intval($this->start_date) < intval(mktime(0,0,0, date('m'), date('d') + $this->lab_data['dayOffset'])) )  {
				$this->type = RES_TYPE_VIEW;
			}
			
			if ($auth->getCurrentID() != $this->user_id && !$is_admin) { $this->type = RES_TYPE_VIEW; };
			
			$rs = $this->db->get_equipment_data($this->resource_id);
			if ($this->type == RES_TYPE_ADD && $rs['approval'] == 1) {
				$this->is_pending = true;		// On the initial add, make sure that the is_pending flag is correct
			}
			printTitle($rs);
			beginReserveForm($this->type == RES_TYPE_ADD, $this->is_blackout);
			beginContainer();
			
			printBasicPanel($this, $rs, $is_private);		// Contains resource/user info, time select, summary, repeat boxes
			
			/*
			if ($this->is_blackout || $is_private) {
				print_advanced_panel($this, null, null, false);	// No advanced for either case
			} else {
				if ($is_admin) {
					print_advanced_panel($this,
						$this->db->get_table_data('user',
							array('first_name','last_name','user_id','email'),
							array('last_name','first_name'),
							null,
							null,
							'WHERE `user`.deleted = 0'),
						$this->user_id === $auth->getCurrentID() || $is_admin && $this->type !== RES_TYPE_VIEW);
				}
			}
			*/
			endContainer();
			printButtonsAndHidden($this);
			endReserveForm();
			
			if ($is_admin) {
				printJSCalendarSetup($this, $rs);
			}
		}
		
		/**
		 * Sends an email notification to the user
		 * This function sends an email notifiying the user
		 * of creation/modification/deletion of a reservation
		 * @param string $type type of modification made to the reservation
		 * @param User $user The user object of this reservation
		 * @param array $repeat_dates array of dates reserved on
		 * @param array $users_to_notify array of emails to CC about the reservation mod
		 * @throws phpmailerException
		 * @global $conf
		 */
		function sendEmail($type, $user, $repeat_dates = null, $users_to_notify = null) {
			global $conf;
			
			// Dont bother if nobody wants email
			if (!$user->wantsEmail($type) && !$conf['app']['emailAdmin'])
				return;
			
			$rs = $this->db->get_equipment_data($this->resource_id);
			
			// Email addresses
			$adminEmail = $this->lab_data['adminEmail'];
			$owner = $this->db->get_table_data('user', array('email'), null, null, null, ' WHERE user_id=?', array($rs['owner']));
			$account_data = $this->db->get_res_account_data($this->account_id);
			$ownerEmail = $owner[0]['email'];
			$techEmail  = $conf['app']['techEmail'];
			$url        = CmnFns::getScriptURL();
			
			// Format date
			$start_date   = CmnFns::formatDate($this->start_date);
			$end_date	  = CmnFns::formatDate($this->end_date);
			$start  = CmnFns::formatTime($this->getStartTime());
			$end    = CmnFns::formatTime($this->getEndTime());
			
			$defs = array(
				translate('Reservation #'),
				translate('Date'),
				translate('Resource'),
				translate('Start Time'),
				translate('End Time'),
				translate('Location'),
				translate('Account')
			);
			
			switch ($type) {
				case 'e_add' : $mod = 'created';
					break;
				case 'e_mod' : $mod = 'modified';
					break;
				case 'e_del' : $mod = 'deleted';
					break;
				case 'e_app' : $mod = 'approved';
					break;
			}
			
			$to     = $user->getEmail();		// Who to mail to
			$subject= "Reservation $mod for " . $user->getFullName() . " on " . $rs['name'];
			$subject.= " on " . $start_date;
			$uname  = $user->getFirstName();
			
			$rs['location'] = !empty($rs['location']) ? $rs['location'] : translate('N/A');
			$rs['rphone'] = !empty($rs['rphone']) ? $rs['rphone'] : translate('N/A');
			
			if ($mod == 'approved') {
				$text = translateEmail('reservation_activity_7', $uname, $this->id, $start_date, $start, $end_date, $end, $rs['name'], $rs['location'], translate($mod));
			} else {
				$text = translateEmail('reservation_activity_1', $uname, translate($mod), $this->id, $start_date, $start, $end_date, $end, $rs['name'], $rs['location'], translate($mod));
			}
			
			if ($this->is_repeat && count($repeat_dates) > 1) {
				// Start at index = 1 because at index 0 is the parent date
				$text .= translateEmail('reservation_activity_2');
				for ($d = 1; $d < count($repeat_dates); $d++)
					$text .= CmnFns::formatDate($repeat_dates[$d]) . "\r\n<br/>";
				$text .= "\r\n<br/>";
			}
			
			if ($type != 'e_add' && $this->is_repeat) {
				$text .= translateEmail('reservation_activity_3', translate($mod));
			}
			
			if (!empty($this->summary)) {
				$text .= stripslashes(translateEmail('reservation_activity_4', ($this->summary)));
			}
			
			$text .= translateEmail('reservation_activity_5', ($ownerEmail!='') ? $ownerEmail : $adminEmail, (isset($rs['rphone']) ? $rs['rphone'] : 'the lab staff'), $conf['app']['title'], $url, $url);
			
			if (!empty($techEmail)) $text .= translateEmail('reservation_activity_6', $techEmail, $techEmail);
			
			if ($user->wantsHtmlEmail()) {
				$msg = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <style type="text/css">
	<!--
	body {
		font-size: 11px;
    	font-family: Verdana, Arial, Helvetica, sans-serif;
		background-color: #F0F0F0;
	}
	a {
		color: #104E8B;
		text-decoration: none;
	}
	a:hover {
		color: #474747;
		text-decoration: underline;
	}
	table tr.header td {
		padding-top: 2px;
		padding-botton: 2px;
		background-color: #CCCCCC;
		color: #000000;
		font-weight: bold;
		font-size: 10px;
		padding-left: 10px;
		padding-right: 10px;
		border-bottom: solid 1px #000000;
	}
	table tr.values td {
		border-bottom: solid 1px #000000;
		padding-left: 10px;
		padding-right: 10px;
		font-size: 10px;
	}
	-->
	</style>
</head>

<body style="font-size: 11px;font-family: Verdana, Arial, Helvetica, sans-serif;background-color: #F0F0F0;">

$text

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="header" style="padding-top: 2px;padding-botton: 2px;background-color: #CCCCCC;color: #000000;font-weight: bold;font-size: 10px;padding-left: 10px;padding-right: 10px;border-bottom: solid 1px #000000;">
    <td>{$defs[0]}</td>
    <td>{$defs[1]}</td>
    <td>{$defs[2]}</td>
    <td>{$defs[3]}</td>
    <td>{$defs[4]}</td>
    <td>{$defs[5]}</td>
    <td>{$defs[6]}</td>
  </tr>
  <tr class="values" style="border-bottom: solid 1px #000000;padding-left: 10px;padding-right: 10px;font-size: 10px;">
    <td>$this->id</td>
    <td>$start_date</td>
    <td>{$rs['name']}</td>
    <td>$start</td>
    <td>$end</td>
    <td>{$rs['location']}</td>
    <td>{$account_data['FRS']}</td>
  </tr>
</table>
  </body>
</html>
EOT;
			}
			else {
				$text = strip_tags($text);		// Strip out HTML tags
				$msg = $text;
				
				$fields = array (	// array[x] = [0] => title, [1] => field value, [2] => length
					array($defs[0], $this->id, ((strlen($this->id) < strlen($defs[0])) ? strlen($defs[0]) : strlen($this->id))),
					array($defs[1], $start_date, ((strlen($start_date) < strlen($defs[1])) ? strlen($defs[1]) : strlen($start_date))),
					array($defs[2], $rs['name'], ((strlen($rs['name']) < strlen($defs[2])) ? strlen($defs[2]) : strlen($rs['name']))),
					array($defs[3], $start, ((strlen($start) < strlen($defs[3])) ? strlen($defs[3]) : strlen($start))),
					array($defs[4], $end, ((strlen($end) < strlen($defs[4])) ? strlen($defs[4]) : strlen($end))),
					array($defs[5], $rs['location'], ((strlen($rs['location']) < strlen($defs[5])) ? strlen($defs[5]) : strlen($rs['location']))),
					array($defs[6], $account_data['FRS'], ((strlen($account_data['FRS']) < strlen($defs[6])) ? strlen($defs[6]) : strlen($account_data['FRS'])))
				);
				$total_width = 0;
				
				foreach ($fields as $a) {	// Create total width by adding all width fields plus the '| ' that occurs before every cell and ' ' after
					$total_width += (2 + $a[2] + 1);
				}
				$total_width++;		// Final '|'
				
				$divider = '+' . str_repeat('-', $total_width - 2) . '+'; 		// Row dividers
				
				$msg .= $divider . "\n";
				$msg .= '| ' . translate("Reservation $mod") . (str_repeat(' ', $total_width - strlen(translate("Reservation $mod")) - 4)) . " |\n";
				$msg .= $divider . "\n";
				foreach ($fields as $a) {		// Repeat printing all title fields, plus enough spaces for padding
					$msg .= "| $a[0]" . (str_repeat(' ', $a[2] - strlen($a[0]) + 1));
				}
				$msg .= "|\n";					// Close the row
				$msg .= $divider . "\n";
				foreach ($fields as $a) {		// Repeat printing all field values, plus enough spaces for padding
					$msg .= "| $a[1]" . (str_repeat(' ', $a[2] - strlen($a[1]) + 1));
				}
				$msg .= "|\n";					// Close the row
				$msg .= $divider . "\n";
			}
			
			$send = false;
			
			// Send email using PHPMailer
			$mailer = new PHPMailer();
			$mailer->ClearAllRecipients();
			if($ownerEmail!=""){
				$mailer->AddBCC($ownerEmail, 'Equipment Owner');
				//echo $ownerEmail . " was added";
			}
			
			if ($user->wantsEmail($type)) {
				$send = true;
				$mailer->AddAddress($to, $uname);
				if ($conf['app']['emailAdmin']) {
					// Add the admin to the CC if they want it
					$mailer->AddBCC($adminEmail, translate('Administrator'));
				}
			}
			else if ($conf['app']['emailAdmin']) {
				$send = true;
				$mailer->AddAddress($adminEmail, translate('Administrator'));
			}
			
			if (!empty($users_to_inform)) {
				for ($i = 0; $i < count($users_to_inform); $i++) { $mailer->AddCC($users_to_inform[$i]); }
			}
			
			$mailer->From = $adminEmail;
			$mailer->FromName = $conf['app']['title'];
			$mailer->Subject = $subject;
			$mailer->Body = $msg;
			$mailer->IsHTML($user->wantsHtmlEmail());
			
			if ($send) $mailer->Send();
			
			unset($rs, $headers, $msg, $fields);
		}
		
		/**
		 * Sends an email to all invited users with a link to accept or deny the reservation
		 * @param array $userinfo array of users to invite
		 * @param array $dates array of dates for this reservation
		 * @param User $user the User object of the reservation owner
		 * @param string $accept_code the acceptance code to be used in the email
		 */
		function inviteUsers($userinfo, $dates, &$user, $accept_code) {
			global $conf;
			$mailer = new PHPMailer();
			
			$mailer->From = $user->getEmail();
			$mailer->FromName = $user->getFirstName() . ' ' . $user->getLastName();
			$mailer->Subject = $conf['app']['title'] . ' ' . translate('Reservation Invitation');
			$mailer->IsHTML(false);
			
			$rs = $this->db->get_equipment_data($this->resource_id);
			$url        = CmnFns::getScriptURL();
			
			// Format dates
			$start_date   = CmnFns::formatDate($this->start_date);
			$end_date	  = CmnFns::formatDate($this->end_date);
			$start  = CmnFns::formatTime($this->getStartTime());
			$end    = CmnFns::formatTime($this->getEndTime());
			
			$dates_text = '';
			for ($d = 1; $d < count($dates); $d++)
				$dates_text .= CmnFns::formatDate($dates) . ",";
			
			foreach ($userinfo as $info) {
				// Create and send the email
				list($user_id, $email) = explode('|', $info);
				
				$accept_url = $url . "/manage_invites.php?id={$this->id}&user_id=$user_id&accept_code=$accept_code&action=" . INVITE_ACCEPT;
				$decline_url= $url . "/manage_invites.php?id={$this->id}&user_id=$user_id&accept_code=$accept_code&action=" . INVITE_DECLINE;
				
				$mailer->ClearAllRecipients();
				$mailer->AddAddress($email);
				$mailer->Body = translateEmail('reservation_invite', $user->getFullName(), $rs['name'], $start_date, $start, $end_date, $end, $this->summary, $dates_text, $accept_url, $decline_url, $conf['app']['title'], $url);
				$mailer->Send();
			}
		}
		
		/**
		 * Send an email informing the users they have been dropped from the reservation
		 * @param array $emails array of email addresses
		 * @param array $dates that have been dropped
		 * @param User $user User object of the owner
		 */
		function remove_users_email($emails, $dates, &$user) {
			global $conf;
			$mailer = new PHPMailer();
			
			$mailer->From = $user->getEmail();
			$mailer->FromName = $user->getFirstName() . ' ' . $user->getLastName();
			$mailer->Subject = $conf['app']['title'] . ' ' . translate('Reservation Participation Change');
			$mailer->IsHTML(false);
			
			$rs = $this->db->get_equipment_data($this->resource_id);
			$url        = CmnFns::getScriptURL();
			
			// Format dates
			$start_date   = CmnFns::formatDate($this->start_date);
			$end_date	  = CmnFns::formatDate($this->end_date);
			$start  = CmnFns::formatTime($this->getStartTime());
			$end    = CmnFns::formatTime($this->getEndTime());
			
			$dates_text = '';
			for ($d = 1; $d < count($dates); $d++)
				$dates_text .= CmnFns::formatDate($dates) . ",";
			
			foreach ($emails as $email) {
				$mailer->ClearAllRecipients();
				$mailer->AddAddress($email);
				$mailer->Body = translateEmail('reservation_removal', $rs['name'], $start_date, $start, $end_date, $end, $this->summary, $dates_text);
				$mailer->Send();
			}
		}
		
		/**
		 * This function updates a users reservation status
		 * This can accept/decline a reservation for a user
		 * @param string $user_id id of the member to change the status for
		 * @param string $action action code to perform
		 * @param bool $update_all if this action applies to all reservations in the group
		 */
		function updateUsers($user_id, $action, $update_all) {
			switch ($action) {
				case INVITE_ACCEPT :
					$this->db->confirm_user($user_id, $this->id, $this->parent_id, $update_all);
					break;
				case INVITE_DECLINE :
					$this->db->remove_user($user_id, $this->id, $this->parent_id, $update_all);
					break;
				default :
					return false;
					break;
			}
			return true;
		}
		
		/**
		 * Returns the type of this reservation
		 * @param none
		 * @return string the 1 char reservation type
		 */
		function getReservationType() {
			return $this->type;
		}
		
		/**
		 * Returns the ID of this reservation
		 * @param none
		 * @return string this reservations id
		 */
		function getReservationId() {
			return $this->id;
		}
		
		/**
		 * Returns the start time of this reservation
		 * @param none
		 * @return int start time (in minutes)
		 */
		function getStartTime() {
			return $this->start_time;
		}
		
		/**
		 * Returns the end time of this reservation
		 * @param none
		 * @return int ending time (in minutes)
		 */
		function getEndTime() {
			return $this->end_time;
		}
		
		/**
		 * Returns the timestamp for this reservation's date
		 * @param none
		 * @return int reservation timestamp
		 */
		function getStartDate() {
			return $this->start_date;
		}
		
		/**
		 * Returns the created timestamp of this reservation
		 * @param none
		 * @return int created timestamp
		 */
		function getCreatedTimestamp() {
			return $this->created;
		}
		
		/**
		 * Returns the modified timestamp of this reservation
		 * @param none
		 * @return int modified timestamp
		 */
		function getModifiedTimestamp() {
			return $this->modified;
		}
		
		/**
		 * Returns the resource id of this reservation
		 * @param none
		 * @return string resource id
		 */
		function getResourceId() {
			return $this->resource_id;
		}
		
		/**
		 * Returns the resource id of this reservation
		 * @param none
		 * @return string resource id
		 */
		function getReservationStatus() {
			return intval($this->is_pending);
		}
		
		/**
		 * Returns the member id of this reservation
		 * @return string user_id
		 */
		function getUserId() {
			return $this->user_id;
		}
		
		/**
		 * Returns the account id of this reservation
		 * @return string account_id
		 */
		function getAccountId() {
			return $this->account_id;
		}
		
		/**
		 * @return array
		 */
		function getReservationAccountData() {
			return $this->db->get_res_account_data($this->account_id);
		}
		
		/**
		 * Returns the User object for this reservation
		 * @return User object for this reservation
		 */
		function getUser() {
			return $this->user;
		}
		
		/**
		 * Returns the id of the parent reservation
		 * This will only be set if this is a recurring reservation
		 *  and is not the first of the set
		 * @return string parentid
		 */
		function getParentId() {
			return $this->parent_id;
		}
		
		/**
		 * Returns the summary for this reservation
		 * @return string summary
		 */
		function getSummary() {
			return $this->summary;
		}
		
		/**
		 * Returns the lab_id for this reservation
		 * @param none
		 * @return string lab_id
		 */
		function getLabId() {
			return $this->lab_id;
		}
		
		/**
		 * Returns this reservations end date
		 * @param none
		 * @return int timestamp for this reservations end date
		 */
		function getEndDate() {
			return $this->end_date;
		}
		
		/**
		 * Returns this reservations billing note
		 * @param none
		 * @return string billing note
		 */
		function getBillingNote() {
			return $this->billing_note;
		}
		
		/**
		 * Returns this reservations billing note
		 * @param none
		 * @return string billing note
		 */
		function getBillingRate() {
			return $this->billing_rate;
		}
		
		
		/**
		 * Returns this reservations technical note
		 * @param none
		 * @return string technical note
		 */
		function getTechnicalNote() {
			return $this->technical_note;
		}
		
		/**
		 * Returns if this reservation is repeating or not
		 * @param none
		 * @return bool if this is a repeating reservation
		 */
		function isRepeat() {
			return ($this->parent_id != null);
		}
		
		/**
		 * Whether there were errors processing this reservation or not
		 * @param none
		 * @return if there were errors or not processing this reservation
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
		 * @param none
		 * @return the last error message
		 */
		function getLastError() {
			if ($this->hasErrors())
				return $this->errors(count($this->errors)-1);
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
		 * Sets the reservation type
		 * @param string type to set the reservation to
		 */
		function setReservationType($type) {
			$this->type = isset($type) ? $type : null;
		}
		
		/**
		 * Checks the current system time and determines if it clears
		 * the reservation horizon.  If the current time is in the future
		 * by more than the resource horizon, then it is editable
		 * @param $user_id
		 * @return boolean true if user is allowed to edit
		 */
		function checkHorizon($user_id) {
			global $auth;
			
			$mach_data = $this->db->get_equipment_data($this->resource_id);
			$utc_offset = 0;
			$isDST = 0; //date('I');
			$now = time() - (($utc_offset + $isDST) * 60 * 60);
			$res_time = $this->start_date + ($this->start_time * 60);
			$mod_time = $mach_data['edit_horizon'] * 60 * 60;
			
			if (CmnFns::dateDifference($now, $res_time + $mod_time, 0) > 0 || $auth->isAdmin()) {
				return true;
			} else {
				return false;
			}
		}
	}
