<?php
/**
* AuthDB class
* Provides all login and registration functionality
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @version 09-28-05
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
* Provide all database access/manipulation functionality
* @see DBEngine
*/
class AuthDB extends DBEngine {
	
	/**
	* Returns whether a user exists or not
	* @param string $email users email address
	* @param bool $use_logonname if we are using a logonname instead of the email address for logon
	* @return user's id or false if user does not exist
	*/
	function userExists($uname, $use_logonname = false) {
		$data = array (strtolower($uname));
		if ($use_logonname) {
			// Can be logonname or email address
			$where = '(email=? OR logon_name=?)';
			$data[] = $data[0];
		}
		else {
			// Can only be email address
			$where = '(email=?)';
		}
		$email_or_login = ($use_logonname) ? 'logon_name' : 'email';
		$result = $this->db->getRow('SELECT user_id FROM ' . $this->get_table('user') . " WHERE $where AND deleted=0", $data);
		$this->check_for_error($result);

		return (!empty($result['user_id'])) ? $result['user_id'] : false;
	}
	
	/**
	* Returns whether the password associated with this username
	*  is correct or not
	* @param string $uname user name
	* @param string $pass password
	* @param bool $use_logonname if we are using a logonname instead of the email address for logon
	* @return bool whether password is correct or not
	*/
	function isPassword($uname, $password, $use_logonname = false) {
		$valid = false;
		$pass = $this->make_password($password);
		$data = array (strtolower($uname), strtolower($uname));
		//$email_or_login = ($use_logonname) ? 'logon_name' : 'email';
		$result = $this->db->getRow('SELECT password FROM ' . $this->get_table('user') . " WHERE (email=? OR logon_name=?)", $data);
		$this->check_for_error($result);

		$valid = password_verify($password, $result['password']);
        
		if ($valid) {
			return true;
		} else {
			// check if password matched old algorithm
			$pass = $this->make_old_password($password);
			$data = array (strtolower($uname), strtolower($uname), $pass);
			//$email_or_login = ($use_logonname) ? 'logon_name' : 'email';
			$result = $this->db->getRow('SELECT count(*) as num FROM ' . $this->get_table('user') . " WHERE (email=? OR logon_name=?) AND password=?", $data);
			$this->check_for_error($result);

			if ($result['num'] > 0) {
				// update password using new algorithm
				$stmt = $this->db->prepare('UPDATE `user` SET password = ? WHERE email = ?');
				$pass = $this->make_password($password);
				$result = $this->db->execute($stmt, array($pass, strtolower($uname)));
				$this->check_for_error($result);

				return true;
			}

		}

		return false;
	}

	/**
	* Inserts a new user into the database
	* @param array $data user information to insert
	* @return new users id
	*/
	function insertMember($data) {
		//$id = $this->get_new_id();
		include_once(__DIR__ . '/../Database.class.php');

		$db = new Database();
		$db->query('INSERT INTO `user` (email, password, first_name, last_name, type_id, advisor, receive_announcements,
					organization, work_address, work_address2, work_city, work_state, work_zip, work_country,
					work_phone, researcher_id, webpage, biography, umd_uid) VALUES (:email, :password, :first_name,
					:last_name, :type_id, :advisor, :receive_announcements, :organization, :work_address, :work_address2,
					:work_city, :work_state, :work_zip, :work_country, :work_phone, :researcher_id, :webpage,
					:biography, :umd_uid)');

		$db->bind(':email', strtolower($data['email']));
		$db->bind(':password', $this->make_password($data['password']));
		$db->bind(':first_name', $data['first_name']);
		$db->bind(':last_name', $data['last_name']);
		$db->bind(':type_id', $data['type_id']);

		$db->bind(':advisor', $data['advisor']);
		$db->bind(':receive_announcements', $data['receive_announcements']);
		$db->bind(':organization', $data['organization']);
		$db->bind(':work_address', $data['work_address']);
		$db->bind(':work_address2', $data['work_address2']);

		$db->bind(':work_city', $data['work_city']);
		$db->bind(':work_state', $data['work_state']);
		$db->bind(':work_zip', $data['work_zip']);
		$db->bind(':work_country', $data['work_country']);
		$db->bind(':work_phone', $data['work_phone']);

		$db->bind(':researcher_id', $data['researcher_id']);
		//$db->bind(':orcid', $data['orcid']);
		$db->bind(':webpage', $data['webpage']);
		$db->bind(':biography', $data['biography']);
		$db->bind(':umd_uid', $data['umd_uid']);

		if ($db->execute()) {
			return $db->lastInsertId();
		} else {
			return false;
		}

		// Put data into a properly formatted array for insertion
		/*
		$to_insert = array(strtolower($data['email']), $this->make_password($data['password']), $data['first_name'], $data['last_name'],
			$data['type_id'], $data['advisor'], $data['receive_announcements'], $data['organization'], $data['work_address'], $data['work_address2'],
			$data['work_city'], $data['work_state'], $data['work_zip'], $data['work_country'], $data['work_phone'],
			$data['researcher_id'], $data['orcid'], $data['webpage'], $data['biography'], $data['umd_uid']
		);

		$sql = 'INSERT INTO ' . $this->get_table('user') .
			' (email, password, first_name, last_name,' .
			' type_id, advisor, receive_announcements, organization, work_address, work_address2,' .
			' work_city, work_state, work_zip, work_country, work_phone,' .
			' researcher_id, orcid, webpage, biography, umd_uid)' .
			' VALUES (?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?)';
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $to_insert);
		$this->check_for_error($result);

		//$_POST['password'] = $this->make_password($_POST['password']);
		//$this->AddToDB('user');
		$id = $this->lastInsertId();
		echo "id: ".$id;
		*/
		//$sql = "UPDATE ".$this->get_table(`user`)." SET timestamp_added = NOW() WHERE user_id = ?";
		//$q = $this->db->prepare($sql);
		//$result = $this->db->execute($q, array($id));

        /*
		if (!empty($data['lab_access']) && is_array($data['lab_access'])) {
			foreach ($data['lab_access'] as $d=>$labid) {
				$sql = "INSERT INTO lab_permission (lab_id, user_id) VALUES (?,?)";
				$q = $this->db->prepare($sql);
				$result = $this->db->execute($q, array($labid,$id));
				$this->check_for_error($result);
			}
		}
		*/
		//return $id;
	}
	
	/**
	* Updates user data
	* @param string $userid id of user to update
	* @param array $data array of new data
	*/
	function update_user($userid, $data) {
		//echo "vardump from AuthDB::update_user()<br />";
		//var_dump($data);
		$to_insert = array();

        //array_push($to_insert, $data['user_id'];
        array_push($to_insert, $data['umd_uid']);
        //array_push($to_insert, $data['username']);
        array_push($to_insert, $data['salutation']);
        array_push($to_insert, $data['first_name']);
        array_push($to_insert, $data['last_name']);
        //array_push($to_insert, $data['rank']);
        array_push($to_insert, $data['email']);

        //array_push($to_insert, $data['cell_phone']);
        //array_push($to_insert, $data['home_phone']);
        array_push($to_insert, $data['work_title']);
        array_push($to_insert, $data['work_phone']);
        array_push($to_insert, $data['work_address']);
        array_push($to_insert, $data['work_address2']);
        array_push($to_insert, $data['work_city']);
        array_push($to_insert, $data['work_state']);
        array_push($to_insert, $data['work_country']);
        array_push($to_insert, $data['work_zip']);

        //array_push($to_insert, $data['timestamp_added']);
        //array_push($to_insert, $data['last_login']);
        //array_push($to_insert, $data['university']);
        array_push($to_insert, $data['biography']);
        array_push($to_insert, $data['affiliations']);
        //array_push($to_insert, $data['visibility']);
        array_push($to_insert, $data['webpage']);
        array_push($to_insert, $data['organization']);
        array_push($to_insert, $data['department']);
        array_push($to_insert, $data['advisor']);
        //array_push($to_insert, $data['comments']);

        //array_push($to_insert, $data['ps']);
        array_push($to_insert, $data['type_id']);
        //array_push($to_insert, $data['exec_type_id']);
        array_push($to_insert, $data['research_interests']);
        //array_push($to_insert, $data['rights']);
        array_push($to_insert, $data['department_id']);
        array_push($to_insert, $data['relationship']);
        //array_push($to_insert, $data['supervisor']);
        //array_push($to_insert, $data['register_status']);
        //array_push($to_insert, $data['intranet_access']);
        array_push($to_insert, $data['receive_announcements']);
        array_push($to_insert, $data['publish_email_on_site']);
        array_push($to_insert, $data['is_collaborator']);
        array_push($to_insert, $data['collaboration_info']);
        //array_push($to_insert, $data['login_count']);
        //array_push($to_insert, $data['memberid']);
        //array_push($to_insert, $data['institution']);


		$sql = 'UPDATE ' . $this->get_table('user') 
			. ' SET'
			. ' umd_uid=?,'
			. ' salutation=?,'
      . ' first_name=?,'
      . ' last_name=?,'
      . ' email=?,'
      . ' work_title=?,'
      . ' work_phone=?,'
      . ' work_address=?,'
      . ' work_address2=?,'
      . ' work_city=?,'
      . ' work_state=?,'
      . ' work_country=?,'
      . ' work_zip=?,'
      . ' biography=?,'
      . ' affiliations=?,'
      . ' webpage=?,'
      . ' organization=?,'
      . ' department=?,'
      . ' advisor=?,'
      . ' type_id=?,'
      . ' research_interests=?,'
      . ' department_id=?,'
      . ' relationship=?,'
      . ' receive_announcements=?,'
      . ' publish_email_on_site=?,'
      . ' is_collaborator=?,'
      . ' collaboration_info=?';
		
		if (isset($data['password']) && !empty($data['password'])) {	// If they are changing passwords
			$sql .= ', password=?';
			array_push($to_insert, $this->make_password($data['password']));
		}
		
		array_push($to_insert, $userid);
		
		$sql .= ' WHERE user_id=?';
		
		//echo "<br /><br />".$sql."<br />";
		//var_dump($data);
		
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $to_insert);
		$this->check_for_error($result);		
		
		if (!empty($data['lab_access']) && is_array($data['lab_access'])) {
			foreach ($data['lab_access'] as $d=>$labid) {
				$sql = "INSERT INTO lab_permission (lab_id, user_id) VALUES (?,?)";
				$q = $this->db->prepare($sql);
				$result = $this->db->execute($q, array($labid,$userid));
				$this->check_for_error($result);
			}
		}
	}
		
    /**
	* Checks to see if User information in DB is synched with LDAP Info
	* @param string $id user to check
	* @param array $ldap array of user's LDAP information
	* @author FCCC
	*/
	function check_updates( $id, $ldap ) {
		
		$result = $this->db->getRow('SELECT email, first_name, last_name, phone FROM ' . $this->get_table('user') . ' WHERE user_id=?', array($id));
		$this->check_for_error($result);
		
        if( $result['email'] != $ldap['emailaddress'] ) {
           return true;
       } elseif( $result['first_name'] != $ldap['first_name'] ) {
            return true;
       } elseif( $result['last_name'] != $ldap['last_name'] ) {
           return true;
       } elseif( $result['phone'] != $ldap['phone'] ) {
	               return true;
        }

        return false;
        
	}

	/**
	* Checks to make sure the user has a valid ID stored in a cookie
	* @param string $id id to check
	* @return whether the id is valid
	*/
	function verifyID($id) {
		$result = $this->db->getRow('SELECT count(*) as num FROM ' . $this->get_table('user') . ' WHERE user_id=?', array($id));
		$this->check_for_error($result);
		
		return ($result['num'] > 0 );
	}
	
	/**
	* Gives full resource permissions to a user upon registration
	* @param string $id id of user to auto assign
	*/ 
	function auto_assign($id) {
		$values = array();
		$resources = $this->db->query('SELECT machid FROM ' . $this->get_table('resources') . ' WHERE autoAssign=1');
		$this->check_for_error($resources);
		while ($rs = $resources->fetchRow()) {
			array_push($values, array($id, $rs['machid']));
		}

		if (count($values) > 0 ) {
			$q = $this->db->prepare('INSERT INTO ' . $this->get_table('permission') . ' VALUES (?,?)');
			$result = $this->db->executeMultiple($q, $values);
			
			$this->check_for_error($result);
		}
		
		$resources->free();

		//$sql = 'insert into ' . $this->get_table('permission') . ' (user_id, machid) select "' . $id . '", machid from ' . $this->get_table('resources') . ' where autoAssign=1';
		//$q = $this->db->prepare($sql);
		//$result = $this->db->execute($q);
		//$this->check_for_error($result);
	}

	/**
	* Checks to see if user is signed in
	* @param string $user_id
	* returns true if signed in
	*/
	function is_signed_in($user_id, $lab_id){
		$sql = "SELECT count(*) AS num FROM sign_log WHERE user_id = '" . $user_id . "' AND lab_id = '" . $lab_id ."' AND signin IS NOT NULL AND signout IS NULL";
		//echo $user_id;
		$result = $this->db->getRow($sql);
		$this->check_for_error($result);
		return ($result['num'] > 0 );
	}

	/**
	* Signs user into lab
	* @param string $user_id user's id
	*/
	function log_signin($user_id, $lab_id){
		$sql = 'INSERT INTO sign_log (signin, user_id, lab_id) VALUES (DATE_SUB(NOW(), INTERVAL \'1:20\' HOUR_MINUTE),"' . $user_id . '","' . $lab_id . '")';
		//echo "log_signin sql: ".$sql."<br>";
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q);
		$this->check_for_error($result);
	}
	
	/**
	* Signs user out of lab
	* @param string $user_id user's id
	*/
	function log_signout($signid){
		$sql = 'UPDATE sign_log SET signout = DATE_SUB(NOW(), INTERVAL \'1:20\' HOUR_MINUTE) WHERE signid = "' . $signid . '"';
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q);
		$this->check_for_error($result);
	}
	
	function is_signed_in_resource($equipment_id, $user_id=''){
		$sql = "SELECT count(*) AS num FROM `usage` WHERE machid = '" . $equipment_id . "'";
		if($user_id !=''){
			$sql .= " AND user_id = " . $user_id;
		}
		$sql .= " AND signin IS NOT NULL AND signout IS NULL";
		//echo $sql;
		$result = $this->db->getRow($sql);
		$this->check_for_error($result);
		return ($result['num'] > 0 );
	}
	
	function log_equipment_signin($user_id, $equipment_id, $frs){
		$sql = 'INSERT INTO `usage` (signin, user_id, machid, frs) VALUES (now(),"' . $user_id . '","' . $equipment_id . '","' . $frs . '")';
		//echo "log_signin sql: ".$sql."<br>";
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q);
		$this->check_for_error($result);
	}
	
	function log_equipment_signout($useid, $equipment_id, $description, $notes, $problems){
		$sql = 'UPDATE `usage` SET signout = now(), description = "' . $description . '", notes = "' . $notes . '", problems = "' . $problems . '" WHERE useid = "' . $useid . '"';
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q);
		$this->check_for_error($result);
	}
	
	/**
	* Returns user list for sign in page
	* @param array $type User type id
	*/
	function get_user_list($type=NULL, $all=FALSE){
		$users = array();
		$sql = 'SELECT user_id, first_name, last_name, email FROM `user` ';
		
		if($type!=NULL || !$all){
			$sql .= 'WHERE ';
			if($type!=NULL)
				$sql .= 'type_id = ' . $type;
			if(!$all)
				if($type!=NULL)
					$sql .= ' AND ';
				$sql .= 'deleted = 0';
		}
		$sql .= ' ORDER BY last_name, first_name';
		$result = $this->db->query($sql);
		$this->check_for_error($result);
		while($row = $result->fetchRow()){
			array_push($users,$row);
		}
		
		return $users;
	}

	/**
	* Returns user list for sign in page
	* @param 2-d array $users id, full name
	*/
	function get_user_perms($userid){
		include_once('UserDB.class.php');
		$user = new UserDB;
		$perms = array();
		$perms = $user->get_user_perms($userid);
		return $perms;
	}

	/**
	* Returns signed in users list for lab sign in page
	* @param 2-d array $users id, full name
	*/
	function get_signedin_user_list($lab_id=1, $order=''){
		$users = array();
		$sql = 'SELECT sign_log.*, user.first_name AS first_name, user.last_name AS last_name FROM `sign_log` LEFT JOIN `user` ON sign_log.user_id = user.user_id WHERE signout IS NULL';
		if($lab_id!=''){
			$sql .= ' AND sign_log.lab_id = ' . $lab_id;
		}
		if ($order!=''){
			$sql .= ' ORDER BY ' . $order;
		}else{
			$sql .= ' ORDER BY last_name, first_name';
		}
		
		//echo 'sql : '.$sql.'<br>';
		
		$result = $this->db->query($sql);
		$this->check_for_error($result);

		if ($result->numRows()>0){
			while($row = $result->fetchRow()){
				array_push($users,$row);
			}
			return $users;
		}else{
			return false;
		}
	}

	/**
	* Returns signed in users list for sign in page
	* @param 2-d array $users id, full name
	*/
	function get_equipment_list($lab_id=''){
		$sql = 'SELECT machid, name FROM `resources`';
		if($lab_id != ''){
			$sql .= ' WHERE lab_id = ' . $lab_id;
		}
		$sql .= ' ORDER BY name';

		$resources = mysqli_query($sql);
		if(mysqli_num_rows($resources)>0){
			return $resources;
		}else{
			return false;
		}
	}
	
	/**
	* Returns signed in users list for resource sign in page
	* @param 2-d array $users id, full name
	*/
	function get_equipment_signedin_user_list($lab_id='', $order=''){
		$sql = 'SELECT u.*, 
						user.first_name AS first_name, 
						user.last_name AS last_name,
						u.user_id AS user_id
				FROM `usage` as u
				LEFT JOIN `user` ON u.user_id = user.user_id
				WHERE u.signout IS NULL';
		if ($order!=''){
			$sql .= ' ORDER BY ' . $order;
		}else{
			$sql .= ' ORDER BY last_name, first_name';
		}
		
		//echo 'sql : '.$sql.'<br>';

		$result = mysqli_query($sql);
		if (mysqli_num_rows($result)>0){
			return $result;
		}else{
			return false;
		}
	}
	
	function get_signedin_user($signid=''){
		$sql = "SELECT s.user_id
				FROM sign_log AS s
				WHERE s.signid = " . $signid;

		$result = mysqli_query($sql);

		if (mysqli_num_rows($result)>0){
			$rs = mysqli_fetch_assoc($result);
			return $rs['user_id'];
		}else{
			return false;
		}
	}
	
	public function getUserLabPermissions(string $user_id, string $lab_id = null) {
		$return = array();
		$values = array($user_id);
		
		$sql = "SELECT * FROM lab_permission WHERE user_id = ?";
		if ($lab_id !== null) {
			$sql .= " AND lab_id = ?";
			$values[] = $lab_id;
		}
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, $values);
		$this->check_for_error($result);
		
		if ($result->numRows()>0) {
			while($row = $result->fetchRow()) {
				array_push($return,$row);
			}
		}
		
		return $return;
	}
	
	public function getUserSystemPermissions(string $user_id, string $system_resource_id) {
		$return = array();
		
		$sql = "SELECT * FROM system_permissions WHERE user_id = ? AND system_resource_id = ?";
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, array($user_id, $system_resource_id));
		$this->check_for_error($result);
		
		if ($result->numRows()>0) {
			while($row = $result->fetchRow()) {
				array_push($return,$row);
			}
		}
		
		return $return;
	}
	
	public function createUserSystemPermissions(string $user_id, string $system_resource_id, array $permissions) {
	
	}
	
	public function isAdmin(string $user_id) {
		$sql = "SELECT is_admin FROM `user` WHERE user_id = ?";
		$q = $this->db->prepare($sql);
		$result = $this->db->execute($q, array($user_id));
		$this->check_for_error($result);
		
		return $result['is_admin'];
	}
	
	public function getSessionHashByUserID($user_id) {
		$query = "SELECT session_hash FROM `sessions` WHERE user_id = ?";
		$result = $this->db->query($query, array($user_id));
		$this->check_for_error($result);
		
		return $result['session_hash'];
	}
	
	public function getSessionHash($sessionHash) {
		$query = "SELECT * FROM `sessions` WHERE session_hash = ?";
		$result = $this->db->query($query, array($sessionHash));
		$this->check_for_error($result);
		
		return $result['session_hash'];
	}
	
	public function deleteSessionHash($sessionHash) {
	
	}
}
