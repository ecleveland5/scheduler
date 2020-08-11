<?php
	/**
	 * Authorization and login functionality
	 * @author Nick Korbel <lqqkout13@users.sourceforge.net>
	 * @author David Poole <David.Poole@fccc.edu>
	 * @version 04-26-05
	 * @package phpScheduleIt
	 *
	 * Copyright (C) 2003 - 2005 phpScheduleIt
	 * License: GPL, see LICENSE
	 */
	/**
	 * Base directory of application
	 */
	#@define('BASE_DIR', dirname(__FILE__) . '/..');
	/**
	 * Include AuthDB class
	 */
	
	include_once('db/AuthDB.class.php');
	/**
	 * Include User class
	 */
	include_once('User.class.php');
	/**
	 * PHPMailer
	 */
	include_once('PHPMailer.class.php');
	/**
	 * Include Auth template functions
	 */
	include_once(BASE_DIR . '/templates/auth.template.php');
	
	/**
	 * This class provides all authoritative and verification
	 *  functionality, including login/logout, registration,
	 *  and user verification
	 */
	class Auth {
		var $is_loggedin = false;
		var	$login_msg = '';
		var $is_attempt = false;
		var $db;
		var $success;
		
		/**
		 * Create a reference to the database class
		 *  and start the session
		 */
		function __construct() {
			$this->db = new AuthDB();
			
		}
		
		/**
		 * Check if user is the administrator
		 * This function checks to see if the currently
		 *  logged in user is the administrator, granting
		 *  them special permissions
		 * @return boolean whether the user is the admin
		 */
		public static function isAdmin() {
			return isset($_SESSION['sessionAdmin']);
		}
		
		function canCreateAccount() {
			$canCreate = $this->db->get_table_data('user', array('user_type.createAccount'), NULL, NULL, NULL, ' JOIN user_type ON `user`.type_id=user_type.user_type_id WHERE user_id=?', array($this->getCurrentID()));
			return $canCreate[0]['createAccount'];
		}
		
		/**
		 * Check user login
		 * This function checks to see if the user has
		 * a valid session set (if they are logged in)
		 * @return boolean whether the user is logged in
		 */
		public static function is_logged_in() {
			/*
					if (!isset($_SESSION['sessionID']) && ) {
						doLogin($uname, $pass, $cookieVal = 'y', $isCookie = false, $resume = '', $lang = '')
						return true;
					} else {
						return false;
					}
				*/
			return isset($_SESSION['sessionID']);
		}
		
		/**
		 * Returns the currently logged in user's userid
		 * @return int userid, or null if the user is not logged in
		 */
		public static function getCurrentID() {
			return isset($_SESSION['sessionID']) ? $_SESSION['sessionID'] : null;
		}
		
		/**
		 * Logs the user in
		 * @param string $uname username
		 * @param string $pass password
		 * @param string $cookieVal y or n if we are using cookie
		 * @param bool $isCookie id value of user stored in the cookie
		 * @param string $resume page to forward the user to after a login
		 * @param string $lang language code to set
		 * @return string|null error message that occured during login
		 */
		function doLogin($uname, $pass, $cookieVal = 'y', $isCookie = false, $resume = '', $lang = '') {
			global $conf;
			$msg = null;
			$_SESSION['sessionID'] = null;
			$_SESSION['sessionName'] = null;
			$_SESSION['sessionAdmin'] = null;
			$uname = stripslashes($uname);
			$pass = stripslashes($pass);
			$ok_user = $ok_pass = false;
			$use_logonname = (bool)$conf['app']['useLogonName'];
			$adminEmail = strtolower($conf['app']['adminEmail']);
			
			if (empty($resume)) {
				if (empty($_SESSION['resume']))
					$resume = 'ctrlpnl.php';		// Go to control panel by default
				else
					$resume = $_SESSION['resume'];
			}
			
			// Cookie Authentication
			if ($isCookie !== false) {
				$id = $isCookie;
				if ($this->db->verifyID($id)) {
					$ok_user = $ok_pass = true;
				} else {
					$ok_user = $ok_pass = false;
					setcookie('nanocenterID', '', time()-3600, '/');	// Clear out all cookies
					$msg .= translate('That cookie seems to be invalid') . '<br/>';
				}
				
			} else {
				
				// LDAP Authentication
				if( $conf['ldap']['authentication'] ) {
					include_once('LDAPEngine.class.php');
					$ldap = new LDAPEngine($uname, $pass);
					if( $ldap->connected() ) {
						$mail = $ldap->getUserEmail();
						if( $mail ) {
							$id = $this->db->userExists( $mail );
							if( $id ) {
								// check if LDAP and local DB are in consistancy.
								$updates = $ldap->getUserData();
								if( $this->db->check_updates( $id, $updates ) ) {
									$this->db->update_user( $id, $updates );
								}
							} else {
								$data = $ldap->getUserData();
								$id = $this->do_register_user( $data );
							}
							$ok_user = true; $ok_pass = true;
						} else {
							$msg .= translate('This system requires that you have an email address.');
						}
					}	else {
						$msg .= translate('Invalid User Name/Password.');
					}
					$ldap->disconnect();
					
				} else {
					
					// Database & HTML Authentication
					
					// If we cant find email, set message and flag
					if ( !$id = $this->db->userExists($uname, $use_logonname) ) {
						$msg .= translate('We could not find that logon in our database.') . '<br/>';
						$ok_user = false;
					} else {
						$ok_user = true;
					}
					
					// If password is incorrect, set message and flag
					if ($ok_user && !$this->db->isPassword($uname, $pass, $use_logonname)) {
						$msg .= translate('That password did not match the one in our database.') . '<br/>';
						$ok_pass = false;
					} else {
						$ok_pass = true;
					}
				}
			}
			
			// If the login failed, notify the user and quit the app
			if (!$ok_user || !$ok_pass) {
				$msg .= translate('You can try');
				$this->login_msg = $msg;
				return $msg;
			}	else {
				
				$this->is_loggedin = true;
				$user = new User($id);	// Get user info
				$user->record_login();
				
				// If the user wants to set a cookie, set it
				// for their ID and first_name.  Expires in 30 days (2592000 seconds)
				if (!empty($cookieVal)) {
					//die ('Setting cookie');
					setcookie('nanocenterID', $user->get_id(), time() + 2592000, '/');
				}
				
				// If it is the admin, set session variable
				if ($user->get_email() == $adminEmail || $user->get_isadmin()) {
					$_SESSION['sessionAdmin'] = $user->get_email();
				}
				
				// Set other session variables
				$_SESSION['sessionID'] = $user->get_id();
				$_SESSION['sessionName'] = $user->get_first_name();
				
				if ($lang != '') {
					set_language($lang);
				}
				
				$_SESSION[$conf['app']['sessionName']] = $user->get_id();
				
				// Send them to the control panel
				CmnFns::redirect(urldecode($resume));
			}
			return $msg;
		}
		
		/**
		 * Log the user out of the system
		 */
		function doLogout() {
			// Check for valid session
			if (!$this->is_logged_in()) {
				$this->print_login_msg();
				die;
			} else {
				// Destroy all session variables
				unset($_SESSION['sessionID']);
				unset($_SESSION['sessionName']);
				if (isset($_SESSION['sessionAdmin'])) unset($_SESSION['sessionAdmin']);
				session_destroy();
				
				// Clear out all cookies
				setcookie('nanocenterID', '', time()-3600, '/');
				
				// Refresh page
				CmnFns::redirect($_SERVER['PHP_SELF']);
			}
		}
		
		/**
		 * Records the user sign in and out of a lab, records into sign_log table.
		 * It checks if user is already logged in.
		 *
		 * @param string $user_id
		 * @param string $password
		 * @param string $lab_id record which lab the user is signing in to
		 * @param string $signaction
		 * @param string $signid
		 * @return string|null error message or blank
		 */
		function doSignin($user_id, $password, $lab_id, $signaction, $signid='') {
			global $conf;
			if ($user_id == '' && $signid != ''){
				$user_id = $this->get_signedin_user($signid);
			}
			$user = new User($user_id);
			$email = $user->get_email();
			$ok_user = false;
			$msg = null;
			if (!empty($user_id)) {
				if ($signaction == 'signin'){
					if ($this->db->is_signed_in($user_id, $lab_id)){
						$msg = $user->get_name() . " is currently signed in.<br />";
					}else{
						
						if ( !$id = $this->db->userExists($email, $conf['app']['useLogonName']) ) {
							$msg .= translate('We could not find that user in our database.') . '<br />';
						}else{
							$ok_user = true;
						}
						// If password is incorrect, set message and flag
						if ($ok_user && !$this->db->isPassword($email, $password, $conf['app']['useLogonName'])) {
							$msg .= 'That password for ' . $user->get_name() . ' did not match the one in our database.<br />';
						}else{
							// log the signin event
							$this->db->log_signin($user_id, $lab_id);
							$msg = $user->get_name() . " signed in.";
						}
					}
				}else if ($signaction == 'signout'){
					// Check if signed in first
					if ($user_id=='0'){
						$msg = "No one to sign out.";
					}else{
						if (!$this->db->is_signed_in($user_id, $lab_id)){
							$msg = $user->get_name() . " is not signed in.<br />";
						}else{
							if ($this->isAdmin()){
								$this->db->log_signout($signid);
								$msg = $user->get_name() . " signed out.";
							}else{
								if (!$this->db->isPassword($email, $password, $conf['app']['useLogonName'])) {
									$msg .= 'That password for ' . $user->get_name() . ' did not match the one in our database.<br />';
								}else{
									// log the signout event
									$this->db->log_signout($signid);
									$msg = $user->get_name() . " signed out.";
								}
							}
						}
					}
				}
			} else {
				$msg = "That user is not registered.";
			}
			unset($user);
			return $msg;
		}
		
		/**
		 * Signs a user in to a resource to record actual usage time
		 * @param string $useid
		 * @param string $user_id
		 * @param string $password
		 * @param string $equipment_id
		 * @param string $frs
		 * @param string $signaction
		 * @param string $description
		 * @param string $notes
		 * @param string $problems
		 * @return string|null error message
		 */
		function doResourceSignin($useid, $user_id, $password, $equipment_id, $frs, $signaction, $description, $notes, $problems) {
			global $conf;
			$user = new User($user_id);
			$email = $user->get_email();
			$ok_user = false;
			$msg = null;
			
			if ($signaction == 'signin'){
				if ( !$id = $this->db->userExists($email, $conf['app']['useLogonName']) ) {
					$msg .= translate('We could not find that user in our database.') . '<br />';
				}else{
					$ok_user = true;
				}
				// If password is incorrect, set message and flag
				if ($ok_user && !$this->db->isPassword($email, $password, $conf['app']['useLogonName'])) {
					$msg .= 'That password for ' . $user->get_name() . ' did not match the one in our database.<br />';
				}else{
					if ($user->has_perm($equipment_id)){
						// log the signin event
						$this->db->log_equipment_signin($user_id, $equipment_id, $frs);
						$msg = $user->get_name() . " signed in.";
					}else{
						$msg = $user->get_name() . " does not have permission on this resource.";
					}
				}
			}else if ($signaction == 'signout'){
				// Check if signed in first
				if ($user_id=='0' || $user_id ==''){
					$msg = "No one to sign out.";
				}else{
					if (!$this->db->is_signed_in_resource($equipment_id, $user_id)){
						$msg = $user->get_name() . " is not signed in.<br />";
					}else{
						if ($this->isAdmin()){
							$this->db->log_signout($user_id);
							$msg = $user->get_name() . " signed out.";
						}else{
							if (!$this->db->isPassword($email, $password, $conf['app']['useLogonName'])) {
								$msg .= 'That password for ' . $user->get_name() . ' did not match the one in our database.<br />';
							}else{
								// log the signout event
								$this->db->log_equipment_signout($useid, $equipment_id, $description, $notes, $problems);
								$msg = $user->get_name() . " signed out.";
							}
						}
					}
				}
			}
			unset($user);
			return $msg;
		}
		
		/**
		 * Signs a user in to a reservation to record actual usage time and fulfill the
		 * reservation requirement.
		 * @param string $user_id
		 * @param string $password
		 * @param string $resid
		 * @param string $account_id
		 * @param string $signaction
		 * @param string $msg
		 * @return bool
		 */
		function doReservationSignin($user_id, $password, $resid, $account_id, $signaction, &$msg) {
			global $conf;
			$user = new User($user_id);
			$email = $user->get_email();
			$ok_user = false;
			$msg = '';
			$return = false;
			
			if ($signaction == 'signin'){
				if ( !$id = $this->db->userExists($email, $conf['app']['useLogonName']) ) {
					$msg .= translate('We could not find that user in our database.') . '<br />';
				}else{
					$ok_user = true;
				}
				
				// If password is incorrect, set message and flag
				if ($ok_user && !$this->db->isPassword($email, $password, $conf['app']['useLogonName'])) {
					$msg .= 'That password for ' . $user->get_name() . ' did not match the one in our database.<br />';
				}else{
					$return = true;
				}
			}
			return $return;
		}
		
		/**
		 * Register a new user
		 * This function will allow a new user to register.
		 * It checks to make sure the email does not already
		 * exist and then stores all user data in the login table.
		 * It will also set a cookie if the user wants
		 * @param array $data array of user data
		 * @param string $current_page
		 * @return false|new|string
		 * @throws phpmailerException
		 */
		function do_register_user($data, $current_page='') {
			global $conf;
			global $link;
			
			// Verify user data
			$msg = $this->check_all_values($data, false);
			
			if ($conf['app']['useReCaptcha']) {
				$privatekey = "6LdHdDUUAAAAADw3SIP2T4L9o8S7llXUV0s_8sv9";
				//$privatekey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
				$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$privatekey."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']);
				$obj = json_decode($response);
				if($obj->success !== true) {
					$msg .= "The reCAPTCHA wasn't entered correctly. Go back and try it again.";
					return $msg;
				}
			}
			
			$adminEmail = strtolower($conf['app']['adminEmail']);
			$techEmail  = empty($conf['app']['techEmail']) ? translate('N/A') : $conf['app']['techEmail'];
			$url        = CmnFns::getScriptURL();
			
			// Register the new member
			$id = $this->db->insertMember($data);
			
			$this->db->auto_assign($id);		// Give permission on auto-assigned resources
			
			$mailer = new PHPMailer();
			$mailer->IsHTML(false);
			
			// Email user informing about successful registration
			$subject = $conf['ui']['welcome'];
			$msg = translate_email('register',
				$data['first_name'], $conf['ui']['welcome'],
				$data['first_name'], $data['last_name'],
				(isset($data['logon_name']) ? $data['logon_name'] : $data['email']),
				$data['work_phone'],
				$data['organization'],
				$data['position'],
				$url,
				$adminEmail);
			
			$mailer->AddAddress($data['emailaddress'], $data['first_name'] . ' ' . $data['last_name']);
			$mailer->From = $adminEmail;
			$mailer->FromName = $conf['app']['title'];
			$mailer->Subject = $subject;
			$mailer->Body = $msg;
			$mailer->Send();
			
			// Email the admin informing about new user
			if ($conf['app']['emailAdmin']) {
				$subject = translate('A new user has been added');
				$msg = translate_email('register_admin',
					$data['email'],
					$data['first_name'], $data['last_name'],
					$data['work_phone'],
					$data['organization'],
					$data['position']);
				
				$mailer->ClearAllRecipients();
				$mailer->AddAddress($adminEmail);
				$mailer->Subject = $subject;
				$mailer->Body = $msg;
				$mailer->Send();
			}
			
			// If the user wants to set a cookie, set it
			// for their ID and first_name.  Expires in 30 days (2592000 seconds)
			if (isset($data['setCookie'])) {
				setcookie('nanocenterID', $id, time() + 2592000, '/');
			}
			
			// If it is the admin, set session variable
			if ($data['email'] == $adminEmail) {
				$_SESSION['sessionAdmin'] = $adminEmail;
			}
			
			// Set other session variables
			$_SESSION['sessionID'] = $id;
			$_SESSION['sessionName'] = $data['first_name'];
			
			// Write log file
			CmnFns::write_log('New user registered. Data provided: first_name- ' . $data['first_name'] . ' last_name- ' . $data['last_name']
				. ' email- '. $data['email'] . ' work phone- ' . $data['work_phone'] . ' organization- ' . $data['organization']
				. ' position- ' . $data['position'], $id);
			
			if( !$conf['ldap']['authentication'] ) {
				
				if($current_page!=''){
					CmnFns::redirect($current_page, 1, false);
				}else{
					CmnFns::redirect('ctrlpnl.php', 1, false);
				}
				$this->success = translate('You have successfully registered') . '<br/>'; // . $link->getLink('ctrlpnl.php', translate('Continue'));
			} else {
				// return DB id from entry created if using LDAP
				return $id;
			}
			
		}
		
		/**
		 * Edits user data
		 * @param array $data array of user data
		 * @return string
		 */
		function do_edit_user($data) {
			global $conf;
			
			// Verify user data
			$msg = $this->check_all_values($data, true);
			if (!empty($msg)) {
				return $msg;
			}
			$this->db->update_user($_SESSION['sessionID'], $data);
			
			$adminEmail = strtolower($conf['app']['adminEmail']);
			// If it is the admin, set session variable
			if ($data['emailaddress'] == $adminEmail) {
				$_SESSION['sessionAdmin'] = $adminEmail;
			}
			
			// Set other session variables
			$_SESSION['sessionName'] = $data['first_name'];
			
			// Write log file
			CmnFns::write_log('User data modified. Data provided: first_name- ' . $data['first_name'] . ' last_name- ' . $data['last_name']
				. ' email- '. $data['emailaddress'] . ' phone- ' . $data['phone'] . ' institution- ' . $data['institution']
				. ' position- ' . $data['position'], $_SESSION['sessionID']);
			
			$link = CmnFns::getNewLink();
			
			$this->success = translate('Your profile has been successfully updated!') . '<br/>'
				. $link->getLink('ctrlpnl.php', translate('Please return to My Control Panel'));
			
		}
		
		/**
		 * Verify that the user entered all data properly
		 * @param array $data array of data to check
		 * @param boolean $is_edit whether this is an edit or not
		 * @return string
		 */
		function check_all_values(&$data, $is_edit) {
			global $conf;
			$use_logonname = (bool)$conf['app']['useLogonName'];
			$msg = '';
			
			if ($use_logonname && empty($data['logon_name'])) {
				$msg .= translate('Valid username is required') . '<br/>';
			}	else if ($use_logonname) {
				$data['logon_name'] = htmlspecialchars($data['logon_name']);
			}
			
			if (empty($data['email']) || !preg_match("/^[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/", $data['email']))
				$msg .= translate('Valid email address is required.') . '<br/>';
			
			if (empty($data['first_name'])) {
				$msg .= translate('First name is required.') . '<br/>';
			}	else {
				$data['first_name'] = htmlspecialchars($data['first_name']);
			}
			
			if (empty($data['last_name'])) {
				$msg .= translate('Last name is required.') . '<br/>';
			}	else {
				$data['last_name'] = htmlspecialchars($data['last_name']);
			}
			
			if(!empty($data['phone'])) {
				$data['phone'] = htmlspecialchars($data['phone']);
			}
			
			if (!empty($data['institution'])) {
				$data['institution'] = htmlspecialchars($data['institution']);
			}
			
			if (!empty($data['position'])) {
				$data['position'] = htmlspecialchars($data['position']);
			}
			
			if( !$conf['ldap']['authentication'] ) {
				// Make sure email isnt in database (and is not current users email)
				if ($is_edit) {
					$user = new User($_SESSION['sessionID']);
					if (!$use_logonname) {
						if ($this->db->userExists($data['emailaddress']) && ($data['emailaddress'] != $user->get_email()) ) {
							$msg .= translate('That email is taken already.') . '<br/>';
						}
					} else {
						if ( $this->db->userExists($data['logon_name'], true) && ($data['logon_name'] != $user->get_logon_name()) ) {
							$msg .= translate('That logon name is taken already.') . '<br/>';
						}
					}
					
					if (!empty($data['password'])) {
						if (strlen($data['password']) < $conf['app']['minPasswordLength']) {
							$msg .= translate('Min 6 character password is required.', array($conf['app']['minPasswordLength'])) . '<br/>';
						}
						if ($data['password'] != $data['password2']) {
							$msg .= translate('Passwords do not match.') . '<br/>';
						}
					}
					unset($user);
				} else {
					if (empty($data['password']) || strlen($data['password']) < $conf['app']['minPasswordLength']) {
						$msg .= translate('Min 6 character password is required.', array($conf['app']['minPasswordLength'])) . '<br/>';
					}
					if ($data['password'] != $data['password2']) {
						$msg .= translate('Passwords do not match.') . '<br/>';
					}
					if ($this->db->userExists($data['email'])) {
						$msg .= translate('That email is taken already.') . '<br/>';
					}
					if ($use_logonname && $this->db->userExists($data['logon_name'], true)) {
						$msg .= translate('That logon name is taken already.') . '<br/>';
					}
				}
			}
			
			return $msg;
		}
		
		/**
		 * Returns whether the user is attempting to log in
		 * @return bool the user is attempting to log in
		 */
		function isAttempting() {
			return $this->is_attempt;
		}
		
		/**
		 * Kills app
		 */
		function kill() {
			die;
		}
		
		/**
		 * Destroy any lingering sessions
		 */
		function clean() {
			// Destroy all session variables
			unset($_SESSION['sessionID']);
			unset($_SESSION['sessionName']);
			if (isset($_SESSION['sessionAdmin'])) unset($_SESSION['sessionAdmin']);
			session_destroy();
		}
		
		/**
		 * DEPRECIATED, use printRegisterForm below
		 *
		 * Wrapper function to call template 'print_register_form' function
		 * @param boolean $edit whether this is an edit or a new register
		 * @param array $data values to auto fill
		 * @param string $msg
		 * @param string $signin
		 */
		function print_register_form($edit, $data, $msg = '', $signin='') {
			//print_register_form($edit, $data, $msg, $signin);		// Function in auth.template.php
		}
		
		/**
		 * Wrapper function to call template 'print_register_form' function
		 * @param boolean $edit whether this is an edit or a new register
		 * @param array $data values to auto fill
		 * @param string $msg
		 * @param string $signin
		 */
		function printRegisterForm($edit, $data, $msg = '', $signin='') {
			printRegisterForm($edit, $data, $msg, $signin, $this);		// Function in auth.template.php
		}
		
		/**
		 * Wrapper function to call template 'printLoginForm' function
		 * @param string $msg error messages to display for user
		 * @param string $resume page to resume after a login
		 */
		function printLoginForm($msg = '', $resume = '') {
			printLoginForm($msg, $resume);
		}
		
		/**
		 * Wrapper function to call template 'printSigninForm' function
		 * @param string $msg error messages to display for user
		 * @param array $users for array of users
		 * @param array $signed_users
		 * @param string $lab_id
		 */
		function printSigninForm( $msg, $users, $signed_users, $lab_id) {
			printSigninForm($msg, $users, $signed_users, $lab_id);
		}
		
		/** TEMPORARY
		 * Wrapper function to call template 'printSigninForm' function
		 * @param string $msg error messages to display for user
		 * @param array $users for array of users
		 * @param array $signed_users
		 * @param string $lab_id
		 */
		function printSigninFormAC( $msg, $users, $signed_users, $lab_id) {
			printSigninFormAC($msg, $users, $signed_users, $lab_id);
		}
		
		/**
		 * Wrapper function to call template 'printResourceSigninForm' function
		 * @param string $msg error messages to display for user
		 * @param array $resources
		 * @param array $resources_users
		 * @param string $lab_id
		 * @param string $user_id
		 */
		function printResourceSigninForm( $msg, $resources, $resources_users, $lab_id, $user_id='') {
			printResourceSigninForm($msg, $resources, $resources_users, $lab_id, $user_id);
		}
		
		/**
		 * Wrapper function to call template 'printResourceLoginForm' function
		 * @param string $msg error messages to display for user
		 * @param array $users for array of users
		 * @param string $equipment_id
		 * @param string $user_id
		 * @param string $useid
		 * @param string $signaction
		 */
		function printResourceLoginForm( $msg, $users, $equipment_id, $user_id, $useid, $signaction='') {
			printResourceLoginForm($msg, $users, $equipment_id, $user_id, $useid, $signaction);
		}
		
		/**
		 * Prints a message telling the user to log in
		 * @param boolean $kill whether to end the program or not
		 */
		public static function print_login_msg($kill = true) {
			CmnFns::redirect(CmnFns::getScriptURL() . '/');
		}
		
		/**
		 * Prints out the latest success box
		 */
		function print_success_box() {
			CmnFns::do_message_box($this->success);
		}
		
		/**
		 * Returns true if a user_id is in the user table
		 * and false otherwise
		 * @param string $user_id
		 */
		function is_user($user_id){
			$user = new User($user_id);
			
			return $this->db->userExists($user->get_email());
		}
		
		/**
		 * Returns array of users
		 */
		function get_user_list(){
			return $this->db->get_user_list();
		}
		
		/**
		 * Returns array of users
		 * @param string $userid
		 * @return array
		 */
		function get_user_perms($userid){
			return $this->db->get_user_perms($userid);
		}
		
		/**
		 * Returns array of signed in users
		 * @param string $lab_id
		 * @param string $order
		 * @return array|false
		 */
		function get_signedin_user_list($lab_id='', $order=''){
			return $this->db->get_signedin_user_list($lab_id, $order);
		}
		
		/**
		 * Returns array of resources
		 * @param string $lab_id
		 * @return bool|resource
		 */
		function get_equipment_list($lab_id=''){
			return $this->db->get_equipment_list($lab_id);
		}
		
		/**
		 * Returns array of signed in users signed into resources
		 * @param string $lab_id
		 * @param string $order
		 * @return bool|resource
		 */
		function get_equipment_signedin_user_list($lab_id='', $order=''){
			return $this->db->get_equipment_signedin_user_list($lab_id, $order);
		}
		
		function get_equipment_signed_in_user(){
		
		}
		
		/**
		 * Returns array of signed in users signed into resources
		 * @param string $signid
		 * @return false|mixed
		 */
		function get_signedin_user($signid=''){
			return $this->db->get_signedin_user($signid);
		}
		
		/**
		 * Returns true if supplied user id is valid, false otherwise.
		 * @param string $userID
		 * @return bool
		 */
		function verifyUserID($userID) {
			return $this->db->verifyID($userID);
		}
	}
?>