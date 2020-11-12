<?php
/**
* SelectUser class
* Allow searching and selection of a user
* Perform user specified function when selected
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 04-02-05
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
* Include AdminDB class
*/
include_once('db/AdminDB.class.php');
/**
* Include SelectUser template files
*/
include_once(BASE_DIR . '/templates/selectuser.template.php');

class SelectUser {
	var $db;
	
	var $first_name;
	var $last_name;
	var $pager;
	var $users;
	
	var $javascript = '';
	
	/**
	 * Sets up initial variable values
	 * @param string $first_name
	 * @param string $last_name
	 * @param bool $show_deleted
	 */
	function __construct($first_name = '', $last_name = '', $show_deleted = false) {
		$orders = array('last_name', 'first_name', 'email');
		$this->db = new AdminDB();
		$this->pager = new Pager(0, 10);
		$this->pager->setViewLimitSelect(false);

		if (!empty($first_name) || !empty($last_name)) {
			$num   = $this->db->get_num_search_recs($first_name, $last_name);
			$this->pager->setTotRecords($num);
			$this->users = $this->db->search_users($first_name, $last_name, $show_deleted,$this->pager, $orders);
		}
		else {
			$num = $this->db->get_num_admin_recs('user');	// Get number of records
			$this->pager->setTotRecords($num);					
			$this->users = $this->db->get_all_admin_data($this->pager, 'user', $orders, true);
		}
	}
	
	function printUserTable() {
		print_user_list($this->pager, $this->users, $this->db->get_err(), $this->javascript);
		$this->pager->text_style = 'font-size:11px;';
		$this->pager->printPages();
	}
}
?>