<?php
/**
* Initialization file.  Please do not edit.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author Richard Cantzler <rmcii@users.sourceforge.net>
* @version 07-08-05
* @package phpScheduleIt
*/
/**
* Please refer to readme.html and LICENSE for any additional information
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or (at your option)
* any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with
* this program; if not, write to the
* Free Software Foundation, Inc.
* 59 Temple Place
* Suite 330
* Boston, MA
* 02111-1307
* USA
*/
/********************************************************************/
/*                   DO NOT CHANGE THIS SECTION                     */
/********************************************************************/
global $conf;

ini_set('include_path', ( __DIR__ . '/../lib/'));
ini_set('session.name', $conf['app']['sessionName']);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly',1);
ini_set('session.use_only_cookies',1);
ini_set('session.gc_probability',1);
ini_set('session.cookie_secure',1);

//include_once(__DIR__ . '/../lib/Session.class.php');
//$session = new Session();

$conf['app']['version'] = '1.1.2';

include_once('constants.php');
include_once('langs.php');

if ($lang = determine_language()) {    // Functions exist in the langs.php file
    set_language($lang);
    load_language_file();
}

/********************************************************************/

if ($conf['app']['showErrors']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}