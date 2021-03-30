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
//@define('BASE_DIR', dirname(__FILE__) . '/..');
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
* This class provides all authoritiative and verification
*  functionality, including login/logout, registration,
*  and user verification
*/
class Auth {
	protected $db;
	private $user_id;
	public $is_logged_in;
	public $is_attempt;
	public $login_msg;
	public $success;

	/**
	* Create a reference to the database class
	*  and start the session
	* @param none
	*/
	function __construct() {
		$this->db = new AuthDB();
		$this->is_logged_in = false;
		$this->user_id = null;
		$this->is_attempt = null;
		$this->login_msg = null;
		$this->success = false;
	}

	/**
	* Check if user is the administrator
	* This function checks to see if the currently
	*  logged in user is the administrator, granting
	*  them special permissions
	* @return boolean whether the user is the admin
	*/
	public function isAdmin() {
		$result =  $this->db->isAdmin($this->getCurrentID());
		return $result['is_admin'];
	}

	public function canCreateAccount() {
		$canCreate = $this->db->get_table_data('user', array('user_type.createAccount'), NULL, NULL, NULL, ' JOIN user_type ON `user`.type_id=user_type.user_type_id WHERE user_id=?', array($this->getCurrentID()));
		return $canCreate[0]['createAccount'];
	}

	/**
	* Check user login
	* This function checks to see if the user has
	* a valid session set (if they are logged in)
	* @return boolean whether the user is logged in
	*/
    public function isLoggedIn():bool {
	    if ($this->is_logged_in !== null && $this->is_logged_in !== false) {
		    return true;
	    }
	    return false;
	}

	/**
	* Returns the currently logged in user's userid
	* @return int userid, or null if the user is not logged in
	*/
	public function getCurrentID() {
		return $this->user_id;
	}
	
	/**
	 * Logs the user in
	 * @param string $uname username
	 * @param string $pass password
	 * @param string $cookie_id stored cookie for authenticating
	 * @param string $resume page to forward the user to after a login
	 * @param string $lang language code to set.  Unused until more translations
	 * @return string error message that occured during login
	 */
	function doLogin($uname, $pass, $cookie_id = null, $resume = 'ctrlpnl.php', $lang = 'en_US') {
		global $conf;
		$msg = '';
		$id = null;
		$valid_login = false;
		$use_logonname = (bool)$conf['app']['useLogonName'];
		
		// Look up saved session and authenticate with user's cookie
		if (!empty($cookie_id) && !empty($_COOKIE[$conf['app']['sessionName']])) {
			if ($this->db->verifyId($cookie_id) && $_SESSION[$conf['app']['sessionName']] === hash('sha256',$_COOKIE[$conf['app']['sessionName']])) {
				$valid_login = true;
				$id = $cookie_id;
			} else {
				
				// the user's supplied cookie for user id and session hash did not match
				
				// Clear out all cookies
				setcookie($conf['app']['cookieName'], '', 1, '/', false, true, true);
				setcookie($conf['app']['sessionName'], '', 1, '/', false, true, true);
				setcookie('sessionUsername', '', 1, '/', false, true, true);
				//$msg .= translate('That cookie seems to be invalid') . '<br>';
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
							// check if LDAP and local DB are in consistency.
							$updates = $ldap->getUserData();
							if( $this->db->checkUpdates( $id, $updates ) ) {
								$this->db->updateUser( $id, $updates );
							}
						} else {
							$data = $ldap->getUserData();
							$id = $this->doRegisterUser( $data );
						}
						$valid_login = true;
					} else {
						$msg .= translate('This system requires that you have an email address.');
					}
				}	else {
					$msg .= translate('Invalid User Name/Password.');
				}
				$ldap->disconnect();
				
			} else {
				
				// Database & HTML Authentication
				
				// We may be reaching login for the first time
				// if so, just return false to display login form.
				if (($uname === null || $pass === null) && $id === null) {
					return false;
				}
				
				// If we cant find email, set message and flag
				$id = $this->db->userExists($uname, $use_logonname);
				if ($id !== false) {
					if ($this->db->isPassword($uname, $pass, $use_logonname)) {
						$valid_login = true;
					}
				}
			}
		}
		
		// If the login failed, notify the user and quit the app
		if ($valid_login === true) {
			
			$this->is_logged_in = true;
			$user = new User($id);	// Get user info
			$expire = time() + 2592000;
			
			// Set cookie_id and first_name.  Expires in 30 days (2592000 seconds)
			// set the id of user
			$_SESSION[$conf['app']['cookieName']] = $id;
			setcookie($conf['app']['cookieName'], $id, $expire, '/', false, true, true);
			
			// set the session id of user (sha256 of user's id + IP)
			$sessionHash = hash('sha256', session_id().$_SERVER['REMOTE_ADDR']);
			
			$_SESSION[$conf['app']['sessionName']] = $sessionHash;
			setcookie($conf['app']['sessionName'], $sessionHash, $expire, '/', false, true, true);
			
			$_SESSION['sessionUsername'] = $user->getFirstName();
			setcookie('sessionUsername', $user->getFirstName(), $expire, '/', false, true, true);
			
			$user->recordLogin($sessionHash, $expire);
			
			CmnFns::redirect(urldecode($resume));
			
		} else {
			$msg .= translate('Invalid credentials');
			$this->login_msg = $msg;
			return false;
		}
	}

	/**
	* Log the user out of the system
	* @param none
	*/
	function doLogout($resume) {
		global $conf;
		
		// Destroy all session variables
		unset($_SESSION[$conf['app']['cookieName']]);
		unset($_SESSION[$conf['app']['sessionName']]);
		unset($_SESSION['sessionUsername']);
		session_unset();
		session_destroy();

		// Clear out all cookies
		setcookie($conf['app']['cookieName'], '', 1);
		setcookie($conf['app']['sessionName'], '', 1);
		setcookie($conf['app']['sessionUsername'], '', 1);

		// Refresh page
		//if (!$this->isLoggedIn()) {
		//	$this->printLoginMsg();
		//} else {
			CmnFns::redirect($conf['app']['weburi']);
		//}
	}

	/**
	* Records the user sign in and out of a lab, records into sign_log table.
	* It checks if user is already logged in.
	*
	* param $user_id string
	* param $password string
	* param $lab_id string record which lab the user is signing in to
	*
	* return $msg will give error message or blank
	*/
	function doSignin($user_id, $password, $lab_id, $signaction, $signid=''){
		$msg = '';
		if ($user_id == '' && $signid != ''){
			$user_id = $this->getSignedInUser($signid);
		}
		$user = new User($user_id);
		$email = $user->getEmail();
		$ok_user = false;
		if (!empty($user_id)) {
			if ($signaction == 'signin'){
				if ($this->db->isSignedIn($user_id, $lab_id)){
					$msg = $user->getFullName() . " is currently signed in.<br />";
				}else{

					if ( !$id = $this->db->userExists($email) ) {
						$msg .= translate('We could not find that user in our database.') . '<br />';
					}else{
						$ok_user = true;
					}
					// If password is incorrect, set message and flag
					if ($ok_user && !$this->db->isPassword($email, $password)) {
						$msg .= 'That password for ' . $user->getFullName() . ' did not match the one in our database.<br />';
					}else{
						// log the signin event
						$this->db->logSignIn($user_id, $lab_id);
						$msg = $user->getFullName() . " signed in.";
					}
				}
			}else if ($signaction == 'signout'){
				// Check if signed in first
				if ($user_id=='0'){
					$msg = "No one to sign out.";
				}else{
					if (!$this->db->isSignedIn($user_id, $lab_id)){
						$msg = $user->getFullName() . " is not signed in.<br />";
					}else{
						if ($this->isAdmin()){
								$this->db->logSignOut($signid);
								$msg = $user->getFullName() . " signed out.";
						}else{
							if (!$this->db->isPassword($email, $password)) {
								$msg .= 'That password for ' . $user->getFullName() . ' did not match the one in our database.<br />';
							}else{
								// log the signout event
								$this->db->logSignOut($signid);
								$msg = $user->getFullName() . " signed out.";
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
	*/
	function doResourceSignin($useid, $user_id, $password, $equipment_id, $frs, $signaction, $description, $notes, $problems){
		$user = new User($user_id);
		$email = $user->getEmail();
		$ok_user = false;
		$msg = '';

		if ($signaction == 'signin'){
				if ( !$id = $this->db->userExists($email) ) {
					$msg .= translate('We could not find that user in our database.') . '<br />';
				}else{
					$ok_user = true;
				}
				// If password is incorrect, set message and flag
				if ($ok_user && !$this->db->isPassword($email, $password)) {
					$msg .= 'That password for ' . $user->getFullName() . ' did not match the one in our database.<br />';
				}else{
					if ($user->hasResourcePermission($equipment_id)){
						// log the signin event
						$this->db->logEquipmentSignIn($user_id, $equipment_id, $frs);
						$msg = $user->getFullName() . " signed in.";
					}else{
						$msg = $user->getFullName() . " does not have permission on this resource.";
					}
			}
		}else if ($signaction == 'signout'){
			// Check if signed in first
			if ($user_id=='0' || $user_id ==''){
				$msg = "No one to sign out.";
			}else{
				if (!$this->db->isSignedInResource($equipment_id, $user_id)){
					$msg = $user->getFullName() . " is not signed in.<br />";
				}else{
					if ($this->isAdmin()){
							$this->db->logSignOut($user_id, $equipment_id);
							$msg = $user->getFullName() . " signed out.";
					}else{
						if (!$this->db->isPassword($email, $password)) {
							$msg .= 'That password for ' . $user->getFullName() . ' did not match the one in our database.<br />';
						}else{
							// log the signout event
							$this->db->logEquipmentSignOut($useid, $equipment_id, $description, $notes, $problems);
							$msg = $user->getFullName() . " signed out.";
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
	*/
	function doReservationSignin($user_id, $password, $resid, $frs, $signaction, &$msg){
		$user = new User($user_id);
		$email = $user->getEmail();
		$ok_user = false;
		$msg = '';
		$return = false;

		if ($signaction == 'signin'){
			if ( !$id = $this->db->userExists($email) ) {
				$msg .= translate('We could not find that user in our database.') . '<br />';
			}else{
				$ok_user = true;
			}

			// If password is incorrect, set message and flag
			if ($ok_user && !$this->db->isPassword($email, $password)) {
				$msg .= 'That password for ' . $user->getFullName() . ' did not match the one in our database.<br />';
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
	*/
	function doRegisterUser($data, $current_page='') {
		global $conf;
		global $link;

		// Verify user data
		$msg = $this->checkAllValues($data, false);

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

		$this->db->autoAssign($id);		// Give permission on auto-assigned resources

		$mailer = new PHPMailer();
		$mailer->IsHTML(false);

		// Email user informing about successful registration
		$subject = $conf['ui']['welcome'];
		$msg = translateEmail('register',
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
			$msg = translateEmail('register_admin',
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
	*/
	function doEditUser($data) {
		global $conf;

		// Verify user data
		$msg = $this->checkAllValues($data, true);
		if (!empty($msg)) {
			return $msg;
		}
		$this->db->updateUser($_SESSION['sessionID'], $data);

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
	*/
	function checkAllValues(&$data, $is_edit) {
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
					if ($this->db->userExists($data['emailaddress']) && ($data['emailaddress'] != $user->getEmail()) ) {
						$msg .= translate('That email is taken already.') . '<br/>';
					}
				} else {
					if ( $this->db->userExists($data['logon_name'], true) && ($data['logon_name'] != $user->getLogonName()) ) {
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
	* @param none
	* @return whether the user is attempting to log in
	*/
	function isAttempting() {
		return $this->is_attempt;
	}

	/**
	* Kills app
	* @param none
	*/
	function kill() {
		die;
	}

	/**
	* Destroy any lingering sessions
	* @param none
	*/
	function clean() {
		// Destroy all session variables
		unset($_SESSION['sessionID']);
		unset($_SESSION['sessionName']);
		if (isset($_SESSION['sessionAdmin'])) unset($_SESSION['sessionAdmin']);
		session_destroy();
	}

	/**
	 * Wrapper function to call template 'print_register_form' function
	 * @param boolean $edit whether this is an edit or a new register
	 * @param array $data values to auto fill
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
	*/
	function printSigninForm( $msg = '', $users, $signed_users, $lab_id) {
		printSigninForm($msg, $users, $signed_users, $lab_id);
	}

	/** TEMPORARY
	* Wrapper function to call template 'printSigninForm' function
	* @param string $msg error messages to display for user
	* @param array $users for array of users
	*/
	function printSigninFormAC( $msg = '', $users, $signed_users, $lab_id) {
		printSigninFormAC($msg, $users, $signed_users, $lab_id);
	}

	/**
	* Wrapper function to call template 'printResourceSigninForm' function
	* @param string $msg error messages to display for user
	* @param array $users for array of users
	*/
	function printResourceSigninForm( $msg = '', $resources='', $resources_users='', $lab_id, $signaction='', $user_id='') {
		printResourceSigninForm($msg, $resources, $resources_users, $lab_id, $signaction, $user_id);
	}

	/**
	* Wrapper function to call template 'printResourceLoginForm' function
	* @param string $msg error messages to display for user
	* @param array $users for array of users
	*/
	function printResourceLoginForm( $msg = '', $users='', $equipment_id='', $user_id='', $useid, $signaction='') {
		printResourceLoginForm($msg, $users, $equipment_id, $user_id, $useid, $signaction);
	}

	/**
	* Prints a message telling the user to log in
	* @param boolean $kill whether to end the program or not
	*/
	public static function printLoginMsg($kill = true) {
		CmnFns::redirect(CmnFns::getScriptURL() . '/');
	}

	/**
	* Prints out the latest success box
	* @param none
	*/
	function printSuccessBox() {
		CmnFns::do_message_box($this->success);
	}

	/**
	* Returns true if a user_id is in the user table
	* and false otherwise
	* @param $user_id
	*/
	function isUser($user_id){
		$user = new User($user_id);
		return $this->db->userExists($user->getEmail());
	}

	/**
	* Returns array of users
	*/
	function getUserList():array {
		return $this->db->getUserList();
	}
	
	/**
	 * Returns array of users
	 * @param $user_id
	 * @return array
	 */
	function getUserPerms($user_id){
		return $this->db->getUserPerms($user_id);
	}

	/**
	* Returns array of signed in users
	*/
	function getSignedInUserList($lab_id='', $order=''){
		return $this->db->getSignedInUserList($lab_id, $order);
	}

	/**
	* Returns array of resources
	*/
	function getEquipmentList($lab_id=''){
		return $this->db->getEquipmentList($lab_id);
	}

	/**
	* Returns array of signed in users signed into resources
	*/
	function getEquipmentSignedInUserList($lab_id='', $order=''){
		return $this->db->getEquipmentSignedInUserList($lab_id, $order);
	}
	
	/**
	 * Returns array of signed in users signed into resources
	 * @param string $user_id
	 * @return false|mixed|string
	 */
	function getSignedInUser($user_id=''){
		return $this->db->getSignedInUser($user_id);
	}

	function verifyUserID($user_id) {
		return $this->db->verifyId($user_id);
	}
	
	public function isLabAdmin() {
		$user_id = $this->getCurrentID();
		try {
			$perms = $this->getUserLabPermissions($user_id);
			foreach ($perms as $perm) {
				if ($perm['is_admin'] === "1") {
					return true;
				}
			}
		} catch (error $e) {
		
		}
		return false;
	}
	
	/**
	 * @param string $user_id
	 * @param string|null $lab_id
	 * @return array
	 */
	protected function getUserLabPermissions(string $user_id, string $lab_id = null): array {
		return $this->db->getUserLabPermissions($user_id, $lab_id);
	}
	
	/**
	 * @param string $user_id
	 * @param string|null $system_resource_id
	 * @return array
	 */
	protected function getUserSystemPermissions(string $user_id, $system_resource_id = null): array {
		return $this->db->getUserSystemPermissions($user_id, $system_resource_id);
	}
	
	/**
	 * Create new system permissions for a user
	 * @param string $user_id
	 * @param string $system_resource_id System resource ID
	 * @param array $permissions array ['create', 'read', 'update', 'delete']
	 */
	protected function createUserSystemPermissions(string $user_id, string $system_resource_id, array $permissions): void {
		$this->db->createUserSystemPermissions($user_id, $system_resource_id, $permissions);
	}
	
	/**
	 * Handles login and logout via form or cookie
	 */
	public function authenticate() {
		global $conf;
		
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
		$cookie = filter_input(INPUT_COOKIE, $conf['app']['cookieName'], FILTER_SANITIZE_STRING);
		$session_cookie = filter_input(INPUT_COOKIE, $conf['app']['sessionName'], FILTER_SANITIZE_STRING);
		$login = filter_input(INPUT_GET, 'login', FILTER_SANITIZE_STRING);
		$logout = filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING);
		if ($login === null) {
			$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
		}
		if ($logout === null) {
			$logout = filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING);
		}
		
		if (array_key_exists('resume', $_SESSION) && (strpos($_SESSION['resume'], 'index.php') === true)) {
				$resume = 'ctrlpnl.php';
		} else {
			$resume = $_SESSION['resume'] = $_SERVER['REQUEST_URI'];
		}
		
		if (!empty($logout)) {
			$this->doLogout($resume);
		} else if (!empty($login) && ($login === 'login' || $login === 'Log In')) {
			
			// perform login
			if ($this->doLogin($email, $password, $cookie, $resume)) {
				CmnFns::redirect($resume);
			} else {
				// login credentials failed
				//$this->printLoginForm($this->login_msg);
			}
			
		} else if (!empty($cookie)) {
			// authenticate with user provided cookie
			$saved_sessionHash = $this->db->getSessionHashByUserId($this->user_id);
			
			$session_id = session_id();
			
			$sessionHash = hash('sha256', $session_id . $_SERVER['REMOTE_ADDR']);
			
			if ($this->db->verifyId($cookie)) {
				
				if ($session_cookie === $sessionHash) {
					$ok_user = $ok_pass = true;
					$this->user_id = $cookie;
					$this->is_logged_in = true;
				} else {
					$ok_user = $ok_pass = false;
					setcookie($conf['app']['cookieName'], '', time() - 3600, '/');    // Clear out all cookies
					$this->login_msg .= translate('That cookie seems to be invalid') . '<br>';
				}
			}
		} else {
			// not authenticated
			$this->is_logged_in = false;
			//$this->printLoginForm($this->login_msg);
		}
	}
}