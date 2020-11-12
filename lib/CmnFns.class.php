<?php
/**
* This functions common to most pages
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 09-01-04
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
* Include configuration file
**/
include_once(BASE_DIR . '/config/config.php');
/**
* Include Link class
*/
include_once('Link.class.php');
/**
* Include Pager class
*/
include_once('Pager.class.php');

/**
* Provides functions common to most pages
*/
class CmnFns {

	/**
	* Convert minutes to hours
	* @param double $time time to convert in minutes
	* @return string time in 12 hour time
	*/
	static function formatTime($time) {
		global $conf;

		// Set up time array with $timeArray[0]=hour, $timeArray[1]=minute
		// If time does not contain decimal point
		// then set time array manually
		// else explode on the decimal point
		$hour = intval($time / 60);
		$min = $time % 60;
		if ($conf['app']['timeFormat'] == 24) {
			$a = '';									// AM/PM does not exist
			if ($hour < 10) $hour = '0' . $hour;
		}
		else {
			$a = ($hour < 12 || $hour == 24) ? translate('am') : translate('pm');			// Set am/pm
			if ($hour > 12) $hour = $hour - 12;			// Take out of 24hr clock
			if ($hour == 0) $hour = 12;					// Don't show 0hr, show 12 am
		}
		// Set proper minutes (the same for 12/24 format)
		if ($min < 10) $min = 0 . $min;
		// Put into a string and return
		return $hour . ':' . $min . $a;
	}


	/**
	* Convert timestamp to date format
	* @param string $date timestamp
	* @param string $format format to put datestamp into
	* @return string date as $format or as default format
	*/
    static function formatDate($date, $format = '') {
		global $dates;

		if (empty($format)) $format = $dates['general_date'];
		return strftime($format, $date);
	}


	/**
	* Convert UNIX timestamp to datetime format
	* @param string $ts MySQL timestamp
	* @param string $format format to put datestamp into
	* @return string date/time as $format or as default format
	*/
    static function formatDateTime($ts, $format = '') {
		global $conf;
		global $dates;

		if (empty($format))
			$format = $dates['general_datetime'] . ' ' . (($conf['app']['timeFormat'] ==24) ? '%H' : '%I') . ':%M:%S' . (($conf['app']['timeFormat'] == 24) ? '' : ' %p');
		return strftime($format, $ts);
	}


	/**
	* Convert minutes to hours/minutes
	* @param int $minutes minutes to convert
	* @return string version of hours and minutes
	*/
    static function minutes_to_hours($minutes) {
		if ($minutes == 0)
			return '0 ' . translate('hours');

		$hours = (intval($minutes / 60) != 0) ? intval($minutes / 60) . ' ' . translate('hours') : '';
		$min = (intval($minutes % 60) != 0) ? intval($minutes % 60) . ' ' . translate('minutes') : '';
		return ($hours . ' ' . $min);
	}

	/**
	* Return the current script URL directory
	* @param none
	* @return string url of curent script directory
	*/
    static function getScriptURL() {
		global $conf;
		$uri = $conf['app']['weburi'];
		return (strrpos($uri, '/') === false) ? $uri : substr($uri, 0, strlen($uri));
	}


	/**
	* Prints an error message box and kills the app
	* @param string $msg error message to print
	* @param string $style inline CSS style definition to apply to box
	* @param boolean $die whether to kill the app or not
	*/
    static function do_error_box($msg, $style='', $die = true) {
		global $conf;

		echo '<table border="0" cellspacing="0" cellpadding="0" align="center" class="alert" style="' . $style . '"><tr><td>' . $msg . '</td></tr></table>';

		if ($die) {
			echo '</td></tr></table>';		// endMain() in Template
			echo '<p align="center"><a href="'.$conf['app']['weburi'].'">' . $conf['app']['title'] . '</a></p></body></html>';	// printHTMLFooter() in Template
		 	die();
		}
	}

	/**
	* Prints out a box with notification message
	* @param string $msg message to print out
	* @param string $style inline CSS style definition to apply to box
	*/
    static function do_message_box($msg, $style='') {
		echo '<table border="0" cellspacing="0" cellpadding="0" align="center" class="message" style="' . $style . '"><tr><td>' . $msg . '</td></tr></table>';
	}

	/**
	* Returns a reference to a new Link object
	* Used to make HTML links
	* @param none
	* @return Link object
	*/
    static function getNewLink() {
		return new Link();
	}

	/**
	* Returns a reference to a new Pager object
	* Used to iterate over limited recordesets
	* @param none
	* @return Pager object
	*/
    static function getNewPager() {
		return new Pager();
	}

	/**
	* Strip out slahses from POST values
	* @param none
	* @return array of cleaned up POST values
	*/
    static function cleanPostVals() {
		$return = array();

		foreach ($_POST as $key => $val)
			$return[$key] = stripslashes(trim($val));

		return $return;
	}

	/**
	* Strip out slahses from an array of data
	* @param none
	* @return array of cleaned up data
	*/
	function cleanVals($data) {
		$return = array();

		foreach ($data as $key => $val)
			$return[$key] = stripslashes($val);

		return $return;
	}

	/**
	* Verifies vertical order and returns value
	* @param string $vert value of vertical order
	* @return string vertical order
	*/
    static function get_vert_order($get_name = 'vert') {
		// If no vertical value is specified, use ASC
		$vert = isset($_GET[$get_name]) ? $_GET[$get_name] : 'ASC';

		// Validate vert value, default to DESC if invalid
		switch($vert) {
			case 'DESC';
			case 'ASC';
			break;
			default :
				$vert = 'DESC';
			break;
		}

		return $vert;
	}

	/**
	* Verifies and returns the order to list recordset results by
	* If none of the values are valid, it will return the 1st element in the array
	* @param array $orders all valid order names
	* @return string order of recorset
	*/
    static function get_value_order($orders = array(), $get_name = 'order') {
		if (empty($orders))		// Return null if the order array is empty
			return NULL;

		// Set default order value
		// If a value is specifed in GET, use that.  Else use the first element in the array
		$order = isset($_GET[$get_name]) ? $_GET[$get_name] : $orders[0];

		if (in_array($order, $orders))
			$order = $order;
		else
			$order = $orders[0];

		return $order;
	}


	/**
	* Opposite of php's nl2br function.
	* Subs in a newline for all brs
	* @param string $subject line to make subs on
	* @return reformatted line
	*/
    static function br2nl($subject) {
		return str_replace('<br>', "\n", $subject);
	}

	/**
	* Writes a log string to the log file specified in config.php
	* @param string $string log entry to write to file
	* @param string $userid memeber id of user performing the action
	* @param string $ip ip address of user performing the action
	*/
    static function write_log($string, $userid = NULL, $ip = NULL) {
		global $conf;
		$delim = "\t";
		$file = $conf['app']['logfile'];
		$values = '';

		if (!$conf['app']['use_log'])	// Return if we aren't going to log
			return;

		if (empty($ip))
			$ip = $_SERVER['REMOTE_ADDR'];

		clearstatcache();				// Clear cached results

		if (!is_dir(dirname($file)))
			mkdir(dirname($file), 0777);		// Create the directory

		if (!touch($file))
			return;					// Return if we cant touch the file

		if (!$fp = fopen($file, 'a'))
			return;					// Return if the fopen fails

		flock($fp, LOCK_EX);		// Lock file for writing
		if (!fwrite($fp, '[' . date('D, d M Y H:i:s') . ']' . $delim . $string . $delim . $userid . $delim . $ip . "\r\n"))	// Write log entry
        	return;					// Return if we cant write to the file
		flock($fp, LOCK_UN);		// Unlock file
		fclose($fp);
	}

	/**
	* Returns the day name
	* @param int $day_of_week day of the week
	* @param int $type how to return the day name (0 = full, 1 = one letter, 2 = two letter, 3 = three letter)
	*/
    static function get_day_name($day_of_week, $type = 0) {
		global $days_full;
		global $days_abbr;
		global $days_letter;
		global $days_two;

		$names = array (
			$days_full, $days_letter, $days_two, $days_letter
			);

		return $names[$type][$day_of_week];
	}

	/**
	* Redirects a user to a new location
	* @param string $location new http location
	* @param int $time time in seconds to wait before redirect
	*/
    static function redirect($location, $time = 0, $die = true) {
		header("Refresh: $time; URL=$location");
		if ($die) exit;
	}

	/**
	* Prints out the HTML to choose a language
	* @param none
	*/
    static function print_language_pulldown() {
		global $conf;
		?>
		<select name="language" class="textbox" onchange="changeLanguage(this);">
		<?php
			$languages = get_language_list();
			foreach ($languages as $lang => $conf) {
				echo '<option value="' . $lang . '"'
					. ((determine_language() == $lang) ? ' selected="selected"' : '' )
					. '>' . $conf[3] . ($lang == $conf['app']['defaultLanguage'] ? ' ' . translate('(Default)') : '') . "</option>\n";
			}
		?>
		</select>
		<?php
	}

	/**
	* Searches the input string and creates links out of any properly formatted 'URL-like' text
	* Written by Fredrik Kristiansen (russlndr at online.no)
	* and Albrecht Guenther (ag at phprojekt.de).
	* @param string $str string to search for links to create
	* @return string with 'URL-like' text changed into clickable links
	*/
    static function html_activate_links($str) {
		$str = preg_replace('/(((f|ht){1}tps?:\/\/)[-a-zA-Z0-9@:%_+.~#?&\/=]+)/i', '<a href="\1" target="_blank">\1</a>', $str);
		$str = preg_replace('/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_+.~#?&\/=]+)/i', '\1<a href="http://\2" target="_blank">\2</a>', $str);
		$str = preg_replace('/([_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3})/i','<a href="mailto:\1">\1</a>', $str);
		return $str;
	}

	/**
	 * Calculates the Difference between two timestamps
	 *
	 * @param integer $start_timestamp
	 * @param integer $end_timestamp
	 * @param integer $unit (default 0)
	 * @return string
	 * @access public
	 */
    static function dateDifference($start_timestamp,$end_timestamp,$unit= 0){
	  $days_seconds_star= (23 * 56 * 60) + 4.091; // Star Day
	  $days_seconds_sun= 24 * 60 * 60; // Sun Day
	  $difference_seconds= $end_timestamp - $start_timestamp;
	  switch($unit){
	   case 3: // Days
		 $difference_days= round(($difference_seconds / $days_seconds_sun),2);
		 return 'approx. '.$difference_days.' Days';
	   case 2: // Hours
		 $difference_hours= round(($difference_seconds / 3600),2);
		 return 'approx. '.$difference_hours.' Hours';
	   break;
	   case 1: // Minutes
		 $difference_minutes= round(($difference_seconds / 60),2);
		 return 'approx. '.$difference_minutes.' Minutes';
	   break;
	   default: // Seconds
		 if($difference_seconds > 1){
		   return $difference_seconds.' Seconds';
		 }
		 else{
		   return $difference_seconds.' Second';
		 }
	  }
	}

	/*	Author: Raju Mazumder
	 *	Email: rajuniit@gmail.com
	 *	Class: A simple class to export mysql query and whole html and php page to excel, doc, etc.
	 */
    static function exportToExcel($data,$excel_file_name) {
    header("Pragma: no-cache");	//Prevent Caching
    header("Expires: 0");	//Expires and 0 mean that the browser will not cache the page on your hard drive
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=$excel_file_name");
		header("Content-Transfer-Encoding: binary");

		$body = "";
		foreach($data[0] as $fieldName=>$val) {
			//$body.="<th>".$fieldName."</th>";
			if (!empty($body)) {
				$body .= ",";
			}
			$body .= $fieldName;
		}
		$body .= "\r\n";
    foreach($data as $row) {
    	$newRow = true;
			foreach ($row as $field=>$value) {
				if (!$newRow) {
					$body .=",";
				} else {
					$newRow = false;
				}
				$body.=$value;
			}
			$body .= "\r\n";
    }
    echo $body;
	}

    /**
     * Prints out the HTML for a state select box
     * @param string $name		Name of the select box form element.
     * @param string $selected	Full text OR 2-letter code of pre-selected state.
     */
    static function printStateSelectBox($name='state', $selected='') {
        $state_list = array('AL'=>"Alabama",
            'AK'=>"Alaska",
            'AZ'=>"Arizona",
            'AR'=>"Arkansas",
            'CA'=>"California",
            'CO'=>"Colorado",
            'CT'=>"Connecticut",
            'DE'=>"Delaware",
            'DC'=>"District Of Columbia",
            'FL'=>"Florida",
            'GA'=>"Georgia",
            'HI'=>"Hawaii",
            'ID'=>"Idaho",
            'IL'=>"Illinois",
            'IN'=>"Indiana",
            'IA'=>"Iowa",
            'KS'=>"Kansas",
            'KY'=>"Kentucky",
            'LA'=>"Louisiana",
            'ME'=>"Maine",
            'MD'=>"Maryland",
            'MA'=>"Massachusetts",
            'MI'=>"Michigan",
            'MN'=>"Minnesota",
            'MS'=>"Mississippi",
            'MO'=>"Missouri",
            'MT'=>"Montana",
            'NE'=>"Nebraska",
            'NV'=>"Nevada",
            'NH'=>"New Hampshire",
            'NJ'=>"New Jersey",
            'NM'=>"New Mexico",
            'NY'=>"New York",
            'NC'=>"North Carolina",
            'ND'=>"North Dakota",
            'OH'=>"Ohio",
            'OK'=>"Oklahoma",
            'OR'=>"Oregon",
            'PA'=>"Pennsylvania",
            'RI'=>"Rhode Island",
            'SC'=>"South Carolina",
            'SD'=>"South Dakota",
            'TN'=>"Tennessee",
            'TX'=>"Texas",
            'UT'=>"Utah",
            'VT'=>"Vermont",
            'VA'=>"Virginia",
            'WA'=>"Washington",
            'WV'=>"West Virginia",
            'WI'=>"Wisconsin",
            'WY'=>"Wyoming");

        echo '<select name="'.$name.'" title="State">';
        foreach ($state_list as $state=>$name) {
            echo '<option value="'.$state.'"';
            if ($selected == $state || $selected == $name) {
                echo 'selected="selected"';
            }
            echo '>'.$name.'</option>';
        }
        echo '</select>';
    }

    /**
     * Prints the necessary HTML code for a country select box.
     * @param string $name 		The name of the select box form element.
     * @param string $selected 	The full text OR 2-letter code of
     * 							the country to be selected.
     */
    static function printCountrySelectBox($name='country', $selected='') {
        $country_list = array(	'' => 'Please select a country',
            'US' => 'United States',
            'AF' => 'Afganistan',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegowina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Congo, the Democratic Republic of the',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote d\'Ivoire',
            'HR' => 'Croatia (Hrvatska)',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'TP' => 'East Timor',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'FX' => 'France, Metropolitan',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran (Islamic Republic of)',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KP' => 'Korea, Democratic People\'s Republic of',
            'KR' => 'Korea, Republic of',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Laos',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macau',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint LUCIA',
            'VC' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia (Slovak Republic)',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SH' => 'St. Helena',
            'PM' => 'St. Pierre and Miquelon',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen Islands',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan, Province of China',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania, United Republic of',
            'TH' => 'Thailand',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands (British)',
            'VI' => 'Virgin Islands (U.S.)',
            'WF' => 'Wallis and Futuna Islands',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'YU' => 'Yugoslavia',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe'
        );
        echo '<select name="'.$name.'" id="'.$name.'" title="Country">';
        foreach ($country_list as $country=>$name) {
            echo '<option value="'.$country.'"';
            if ($selected == $country || $selected == $name) {
                echo 'selected="selected"';
            }
            echo '>'.$name.'</option>';
        }
        echo '</select>';
    }

    public function msword_replacements($string) {
        $search = [                 // www.fileformat.info/info/unicode/<NUM>/ <NUM> = 2018
            "\xC2\xAB",     // « (U+00AB) in UTF-8
            "\xC2\xBB",     // » (U+00BB) in UTF-8
            "\xE2\x80\x98", // ‘ (U+2018) in UTF-8
            "\xE2\x80\x99", // ’ (U+2019) in UTF-8
            "\xE2\x80\x9A", // ‚ (U+201A) in UTF-8
            "\xE2\x80\x9B", // ‛ (U+201B) in UTF-8
            "\xE2\x80\x9C", // “ (U+201C) in UTF-8
            "\xE2\x80\x9D", // ” (U+201D) in UTF-8
            "\xE2\x80\x9E", // „ (U+201E) in UTF-8
            "\xE2\x80\x9F", // ‟ (U+201F) in UTF-8
            "\xE2\x80\xB9", // ‹ (U+2039) in UTF-8
            "\xE2\x80\xBA", // › (U+203A) in UTF-8
            "\xE2\x80\x93", // – (U+2013) in UTF-8
            "\xE2\x80\x94", // — (U+2014) in UTF-8
            "\xE2\x80\xA6"  // … (U+2026) in UTF-8
        ];

        $replacements = [
            "<<",
            ">>",
            "'",
            "'",
            "'",
            "'",
            '"',
            '"',
            '"',
            '"',
            "<",
            ">",
            "-",
            "-",
            "..."
        ];

        return str_replace($search, $replacements, $string);
    }
}


/**
 * A class for making time periods readable.
 *
 * This class allows for the conversion of an integer
 * number of seconds into a readable string.
 * For example, '121' into '2 minutes, 1 second'.
 *
 * If an array is passed to the class, the associative
 * keys are used for the names of the time segments.
 * For example, array('seconds' => 12, 'minutes' => 1)
 * into '1 minute, 12 seconds'.
 *
 * This class is plural aware. Time segments with values
 * other than 1 will have an 's' appended.
 * For example, '1 second' not '1 seconds'.
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.2.1
 * @link        http://aidanlister.com/repos/v/Duration.php
 */
class Duration
{
    /**
     * All in one method
     *
     * @param   int|array  $duration  Array of time segments or a number of seconds
     * @return  string
     */
    function toString ($duration, $periods = null)
    {
        if (!is_array($duration)) {
            $duration = Duration::int2array($duration, $periods);
        }

        return Duration::array2string($duration);
    }


    /**
     * Return an array of date segments.
     *
     * @param        int $seconds Number of seconds to be parsed
     * @return       mixed An array containing named segments
     */
    function int2array ($seconds, $periods = null)
    {
        // Define time periods
        if (!is_array($periods)) {
            $periods = array (
                    'years'     => 31556926,
                    'months'    => 2629743,
                    'weeks'     => 604800,
                    'days'      => 86400,
                    'hours'     => 3600,
                    'minutes'   => 60,
                    'seconds'   => 1
                    );
        }

        // Loop
        $seconds = (float) $seconds;
        foreach ($periods as $period => $value) {
            $count = floor($seconds / $value);

            if ($count == 0) {
                continue;
            }

            $values[$period] = $count;
            $seconds = $seconds % $value;
        }

        // Return
        if (empty($values)) {
            $values = null;
        }

        return $values;
    }


    /**
     * Return a string of time periods.
     *
     * @package      Duration
     * @param        mixed $duration An array of named segments
     * @return       string
     */
    function array2string ($duration)
    {
        if (!is_array($duration)) {
            return false;
        }

        foreach ($duration as $key => $value) {
            $segment_name = substr($key, 0, -1);
            $segment = $value . ' ' . $segment_name;

            // Plural
            if ($value != 1) {
                $segment .= 's';
            }

            $array[] = $segment;
        }

        $str = implode(', ', $array);
        return $str;
    }
}
?>
