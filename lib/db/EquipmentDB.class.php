<?php
//@define('BASE_DIR', dirname(__FILE__) . '/../..');
/**
* DBEngine class
*/
include_once(BASE_DIR . '/lib/DBEngine.class.php');

/**
* Provide functionality for getting and setting user data
*/
class EquipmentDB extends DBEngine {

	function get_equipment_data($machid) {
		$result = $this->db->getRow('SELECT * FROM ' . $this->get_table('resources') . ' WHERE machid=? ', array($machid));
		$this->check_for_error($result);
		
		if (count($result) <= 0) {
			$this->err_msg = translate('That record could not be found.');
			return false;
		}
		
		return $this->cleanRow($result);
	}
}
