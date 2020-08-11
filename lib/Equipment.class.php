<?php
/**
* This file provides output functions
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author Richard Cantzler <rmcii@users.sourceforge.net>
* @version 07-12-05
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
* Include Auth class
*/
include_once('db/EquipmentDB.class.php');
/**
* Provides functions for outputting template HTML
*/
class Equipment {
	var $machid;
	var $name;
	var $short_name;
	var $serial_id;
	var $model;
	var $usage;
	var $description;
	var $quality;
	var $value;
	var $width;
	var $height;
	var $depth;
	var $comments;
	var $size;
	var $visibility;
	var $category;
	var $type;
	var $manufacturer;
	var $owner;
	var $lab_name;
	var $lab_rm_building;
	var $timestamp;
	var $lab_id;
	var $status;
	var $minRes;
	var $maxRes;
	var $autoAssign;
	var $approval;
	var $allow_multi;
	var $rphone;
	var $location;
	var $notes;
	
	function __construct($id=NULL) {
		
		if(!is_null($id)) {
			$this->db = new EquipmentDB();
			$this->machid = $id;
			$this->load_by_id();
		}
	}
	
	function load_by_id() {
		$equipment = $this->db->get_equipment_data($this->machid);
		
		$this->machid			= $equipment['machid'];
		$this->name				= $equipment['name'];
		$this->short_name		= $equipment['short_name'];
		$this->serial_id		= $equipment['serial_id'];
		$this->model			= $equipment['model'];
		$this->usage			= $equipment['usage'];
		$this->description		= $equipment['description'];
		$this->quality			= $equipment['quality'];
		$this->value			= $equipment['value'];
		$this->width			= $equipment['width'];
		$this->height			= $equipment['height'];
		$this->depth			= $equipment['depth'];
		$this->comments			= $equipment['comments'];
		$this->size				= $equipment['size'];
		$this->visibility		= $equipment['visibility'];
		$this->category			= $equipment['category'];
		$this->type				= $equipment['type'];
		$this->manufacturer		= $equipment['manufacturer'];
		$this->owner			= $equipment['owner'];
		$this->lab_name			= $equipment['lab_name'];
		$this->lab_rm_building	= $equipment['lab_rm_building'];
		$this->timestamp		= $equipment['timestamp'];
		$this->lab_id			= $equipment['lab_id'];
		$this->status			= $equipment['status'];
		$this->minRes			= $equipment['minRes'];
		$this->maxRes			= $equipment['maxRes'];
		$this->autoAssign		= $equipment['autoAssign'];
		$this->approval			= $equipment['approval'];
		$this->allow_multi		= $equipment['allow_multi'];
		$this->rphone			= $equipment['rphone'];
		$this->location			= $equipment['location'];
		$this->notes			= $equipment['notes'];
	}

	public function get_field($fieldName) {
	    if (array_key_exists($fieldName, get_object_vars($this))) {
	        return $this->$fieldName;
        }
	    return null;
    }
}
