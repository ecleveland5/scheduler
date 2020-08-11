<?php
/**
* UserInfoDB class
* Provides database functions for userInfo.php
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 04-10-04
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
* Provide functionality for userInfo.php
*/
class UserInfoDB extends DBEngine {
	
	/**
	* Returns the previous (alphabetic) user_id
	* @param object $user current user object
	* @return previous user_id as string
	*/
	function get_prev_userid(&$user) {
		$data = array ($user->get_last_name(), $user->get_last_name(), $user->get_first_name(), $user->get_id());
		$result = $this->db->getRow('SELECT user_id FROM ' . $this->get_table('user')
				. ' WHERE ('
				. ' (last_name<?) '
				. ' OR (last_name=? AND first_name<=?)'
				. ') '
				. ' AND user_id <> ?'
				. ' ORDER BY last_name, first_name', $data);

		$this->check_for_error($result);
		
		if (count($result) <= 0)
			return $user->get_id();
		
		return $result['user_id'];
	}
	
	/**
	* Returns the next (alphabetic) user_id
	* @param object $user current user object
	* @return next user_id as string
	*/
	function get_next_userid(&$user) {
		$data = array ($user->get_last_name(), $user->get_last_name(), $user->get_first_name(), $user->get_id());
		$result = $this->db->getRow('SELECT user_id FROM ' . $this->get_table('user')
				. ' WHERE ('
				. ' (last_name>?) '
				. ' OR (last_name=? AND first_name>=?)'
				. ') '
				. ' AND user_id <> ?'
				. ' ORDER BY last_name, first_name', $data);

		$this->check_for_error($result);
		
		if (count($result) <= 0)
			return $user->get_id();
		
		return $result['user_id'];
	}
}
?>