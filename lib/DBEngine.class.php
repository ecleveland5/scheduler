<?php
/**
* DBEngine class
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author Richard Cantzler <rmcii@users.sourceforge.net>
* @version 07-18-05
* @package DBEngine
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Base directory of application
*/
//@define('BASE_DIR', dirname(__FILE__) . '/..');
/**
* CmnFns class
*/
include_once('CmnFns.class.php');
/**
* Pear::DB
*/
include_once('pear/DB.php');

/**
* Provide all database access/manipulation functionality
*/
class DBEngine {

    var $db;                            // Reference to the database object
    var $dbs = array();                 // List of database names to use.  This will be used if more than one 
    																		// database is created and different tables are associated with multiple databases    
    var $table_to_database = array();   // Array associating tables to databases
    var $prefix;                        // Prefix to prepend to all primary keys
    
    var $err_msg = '';
    
    /**
    * DBEngine constructor to initialize object
    * @param none
    */
    function __construct() {
        $this->prefix = $GLOBALS['conf']['db']['pk_prefix'];
        $this->dbs = array ($GLOBALS['conf']['db']['dbName']);
        
        $this->db_connect();
        $this->define_tables();
        
    }
    
    /**
    * Create a persistent connection to the database
    * @param none
    * @global $conf
    */
    function db_connect() {
        global $conf;
    
        /***********************************************************
        / This uses PEAR::DB
        / See http://www.pear.php.net/manual/en/package.database.php#package.database.db
        / for more information and syntax on PEAR::DB
        /**********************************************************/
    
        // Data Source Name: This is the universal connection string
        // See http://www.pear.php.net/manual/en/package.database.php#package.database.db
        // for more information on DSN
        $dsn = $conf['db']['dbType'] . '://' . $conf['db']['dbUser'] . ':' . $conf['db']['dbPass'] . '@' . $conf['db']['hostSpec'] . '/' . $this->dbs[0];

        // Make persistant connection to database
        $db = DB::connect($dsn, true);
    
        // If there is an error, print to browser, print to logfile and kill app
        if (DB::isError($db)) {
            die ('Error connecting to database: ' . $db->getMessage() );
        }
        
        // Set fetch mode to return associatve array
        $db->setFetchMode(DB_FETCHMODE_ASSOC);
    
        $this->db = $db;
    }
    
    /////////////////////////////////////////////////////
    // Common functions
    /////////////////////////////////////////////////////
    /**
    * Defines the $table_to_database array
    * This array will relate each table to a database name,
    *  making it very easy to change all table associations
    *  if additional databases are added
    * @param none
    */
    function define_tables() {
        $this->table_to_database = array (
        		'login'					=> $this->dbs[0],
        		'reservations'	=> $this->dbs[0],
        		'resources'			=> $this->dbs[0],
        		'permission'		=> $this->dbs[0],
        		'labs'					=> $this->dbs[0],
        		'lab_permission'=> $this->dbs[0],
						'accounts'			=> $this->dbs[0],
						'account_users' => $this->dbs[0],
        		'resrouce_categories'	=> $this->dbs[0]					
        );
                        
    }
    
    /**
    * Returns the database and table name in form: database.table
    * @param string $table table to return
    * @global $conf
    * @return string fully qualified table name in form: database.table
    */
    function get_table($table) {
        global $conf;
        return $conf['db']['tbl_prefix'] . $table;
        //return $this->table_to_database[$table] . '.' . $table;
    }
    
    /**
    * Assigns a table to a database for SQL statements
    * @param string $table name of table to change
    * @param string $database name of database that this table belongs to
    * @return bool success of assignment
    */
    function set_table($table, $database) {
        if (!isset($this->table_to_database[$table]))
            return false;
        else
            $this->table_to_database[$table] = $database;
        return true;
    }
    
    /**
     * Generic database query function.
     * This will return specified fields from one table in a specified order
     * @param string $table name of table to return from
     * @param array $fields array of field values to return
     * @param array $orders sql order string
     * @param int $limit limit of query
     * @param int $offset offset of limit
     * @param string $where_clause
     * @param array $where_values
     * @return mixed all data found in query
     */
    function get_table_data($table, $fields = array('*'), $orders = array(), $limit = NULL, $offset = NULL, $where_clause = NULL, $where_values = array()) {
        $return = array();
        
        $order = CmnFns::get_value_order($orders);        // Get main order value    
        $vert = CmnFns::get_vert_order();                // Get vertical order

        $query = 'SELECT ' . join(', ', $fields)
            . ' FROM ' . $this->get_table($table)
            . ' ' . $where_clause . ' '
            . (!empty($order) ? " ORDER BY $order $vert" : '');        
        
        //echo $query;
            
        // Append any other sorting constraints    
        for ($i = 1; $i < count($orders); $i++)
            $query .= ', ' . $orders[$i];
        
        if (!is_null($limit) && !is_null($offset))        // Limit query
            $result = $this->db->limitQuery($query, $offset, $limit, $where_values);
        else                                        // Standard query
            $result = $this->db->query($query, $where_values);
        
        $this->check_for_error($result);
        
        if ($result->numRows() <= 0) {        // Check if any records exist
            $this->err_msg = translate('There are no records in the table.', array($table));
            return false;
        }
        
        while ($rs = $result->fetchRow())
            $return[] = $this->cleanRow($rs);
        
        $result->free();
        
        return $return;
    }
    
    /**
    * Deletes a list of rows from the database
    * @param string $table table name to delete rows from
    * @param string $field field name that items are in
    * @param array $to_delete array of items to delete
    */
    function deleteRecords($table, $field, $to_delete) {
        // Put into string, quoting each value
        $delete = join('","', $to_delete);
        $delete = '"'. $delete . '"';
        
        $result = $this->db->query('DELETE FROM ' . $this->get_table($table) . ' WHERE ' . $field . ' IN (' . $delete . ')');
        
        $this->check_for_error($result);        // Check for an error
        
        return true;
    }        

    
    /**
    * Return all reservations associated with a user
    * @param string $id user id
    * @return array of reservation data
    */
    function get_user_reservations($id, $order, $vert, $today = false, $all = false) {
        $return = array();
		
		// Clean out the duplicated order so that MSSQL is OK
		$orders = trim(preg_replace("/(res|rs).$order,?/", '', 'res.start_date, rs.name, res.startTime'));
		if (strrpos($orders, ',') == strlen($orders)-1) {
			$orders = substr($orders, 0, strlen($orders)-1);
		}
		
		/*
        $query = 'SELECT res.*, rs.*, resusers.* FROM '//, `usage`.signin, `usage`.signout FROM '
                    . $this->get_table('reservations') . ' as res,'
                    . $this->get_table('resources') . ' as rs,'
                    . $this->get_table('reservation_users') . ' as resusers'
					//. ' LEFT JOIN `usage` ON `usage`.resid = res.resid'
                    . ' WHERE resusers.user_id=?'
                    . ' AND resusers.resid=res.resid'
                    . ' AND rs.machid=res.machid'
                    . ' AND (res.start_date>=? OR (res.start_date<=? AND res.end_date>=?))'
                    . ' AND res.is_blackout <> 1'
                    . ' AND resusers.owner = 1';
		//$query		.= ' AND res.start_date >=';
		//($today) ? $query .= ' = ' : $query .= ' >= ';
		//$query	.= ' AND res.startTime > TIME(CURRENT_TIMESTAMP)';
		//$query		.= ' DATE(CURRENT_TIMESTAMP)';
		//$query		.= " ORDER BY $order $vert, res.start_date, rs.name, res.startTime";
		*/
		$query = 'SELECT reservations.*, resources.* FROM reservations'
			   . ' LEFT JOIN reservation_users on reservations.resid = reservation_users.resid'
			   . ' LEFT JOIN resources on reservations.machid = resources.machid'
			   . ' WHERE reservation_users.user_id = ?'
			   . ' AND reservations.is_blackout <> 1'
			   . ' AND reservation_users.owner = 1'
			   . ' AND reservations.deleted=0';
				if (!$all){
				   $query .= ' AND reservations.start_date >= DATE(CURRENT_TIMESTAMP)';
				}
			   $query .= ' AND (reservations.start_date>=? OR (reservations.start_date<=? AND reservations.end_date>=?))'
			   . " ORDER BY $order $vert, reservations.start_date, resources.name, reservations.startTime";
		
        $values = array($id, mktime(0,0,0), mktime(0,0,0), mktime(0,0,0));

        // Prepare query
        $q = $this->db->prepare($query);
        // Execute query
        $result = $this->db->execute($q, $values);
        // Check if error
        $this->check_for_error($result);

        if ($result->numRows() <= 0) {
        	$this->err_msg = "You do not have any reservations scheduled";
					if ($today) {
						$this->err_msg .= ' today.';
					}
          return false;
        }
        
        while ($rs = $result->fetchRow()) {
            $return[] = $this->cleanRow($rs);
        }
        $result->free();
        
        return $return;
    }
    

    /**
    * Gets all the resources that the user has permission to reserve
    * @param string $userid user id
    * @return array or resource data
    */
    function get_user_permissions($userid, $order = '') {
        $return = array();
        
        $sql = 'SELECT rs.*,'
					. ' labs.nickname as nickname'
					. ' FROM '
                    . $this->get_table('permission') . ' as pm,'
                    . $this->get_table('resources') . ' as rs'
					. ' JOIN labs ON rs.lab_id = labs.lab_id'
                    . ' WHERE pm.user_id=?'
                    . ' AND pm.machid=rs.machid';
		if($order!=''){
			$sql .= ' ORDER BY ' . $order;
		}else{
        	$sql .= ' ORDER BY rs.name';
		}
                    
        // Execute query
        $result = $this->db->query($sql, array($userid));
        // Check if error
        $this->check_for_error($result);
        
        if ($result->numRows() <= 0) {
            $this->err_msg = translate('You do not have permission to use any resources.');
            return false;
        }

        while ($rs = $result->fetchRow()) {
            $return[] = $this->cleanRow($rs);
        }
        
        $result->free();
        
        return $return;
    }
    
    /**
    * Get associative array with machID, resource name, and status
    * This function loops through all resources
    *  and constructs an associative array with the
    *  resource's machID, name and status as
    *  $array[x] => ('machid' => 'this_equipment_id', 'name' => 'Resource Name', 'status' => 'a')
    * @param none
    * @return array of machID, resource name, status
    */
    function get_mach_ids($lab_id = null) {
        $return = array();
        $values = array();
        
        $sql = 'SELECT machid, name, status, approval FROM ' . $this->get_table('resources');
        if ($lab_id != null) {
            $sql .= ' WHERE lab_id = ? AND deleted = 0';
            $values = array($lab_id);
        }
        $sql .= ' ORDER BY name';
        
        $result = $this->db->query($sql, $values);
        
        $this->check_for_error($result);
        
        if ($result->numRows() <= 0) {
            $this->err_msg = translate('No resources in the database.');
            return false;
        }        

        while ($rs = $result->fetchRow()) {
            $return[] = $this->cleanRow($rs);
        }
        
        $result->free();
        
        return $return;
    }

    /**
    * Get associative array with account_id, FRS, sub_FRS, name, status, pi_last_name
    * This function loops through all accounts
    *  and constructs an associative array with the
    *  account's account_id, name and status as
    *  $array[x] => ('account_id' => 'this_account_id', 'FRS' => 'FRS #', 
    *                'sub_FRS' => 'Sub FRS #', 'name' => 'Account Name', 
    *                'status' => 'a', 'pi_last_name' => 'The PIs Last Name')
    * @param none
    * @return array of account_id, FRS, sub_FRS, name, status, pi_last_name
    */
    function get_account_ids() {
        $return = array();
        $values = array();
        
        $sql = 'SELECT a.account_id, a.FRS, a.sub_FRS, a.name, a.status, a.pi_last_name, a.pi, u.last_name as pi_ln FROM ' . $this->get_table('accounts') . ' as a ';
        $sql .= ' LEFT JOIN `' . $this->get_table('user') . '` as u ON a.pi = u.user_id';
        $sql .= ' ORDER BY FRS';
        
        $result = $this->db->query($sql, $values);
        
        $this->check_for_error($result);
        
        if ($result->numRows() <= 0) {
            $this->err_msg = translate('No resources in the database.');
            return false;
        }        

        while ($rs = $result->fetchRow()) {
            $return[] = $this->cleanRow($rs);
        }
        
        $result->free();
        
        return $return;
    }    
    /**
    * Gets the default lab_id
    * @param none
    * @return string lab_id of default lab
    */
    function get_default_id() {
        $result = $this->db->getOne('SELECT lab_id FROM ' . $this->get_table('labs') . ' WHERE isDefault = 1 AND isHidden = 0 AND scheduler = 1');
        $this->check_for_error($result);

        if (empty($result)) {    // If default is hidden
            $result = $this->db->getOne('SELECT lab_id FROM ' . $this->get_table('labs') . ' WHERE isHidden = 0 AND scheduler = 1');
            $this->check_for_error($result);
        }

        return $result;
    }
    
    /**
    * Checks to see if the lab_id is valid
    * @param none
    * @return whether it is valid or not
    */
    function check_lab_id($lab_id) {
        $result = $this->db->getOne('SELECT COUNT(lab_id) AS num FROM ' . $this->get_table('labs') . ' WHERE lab_id = ? AND isHidden <> 1', array($lab_id));
        $this->check_for_error($result);

        return (intval($result) > 0);
    }
    
        
    /**
    * Gets all data for a given lab
    * @param string $lab_id id of lab
    * @param array of lab data
    */
    function get_lab_data($lab_id) {
        $result = $this->db->getRow('SELECT * FROM ' . $this->get_table('labs') . ' WHERE lab_id = ?', array($lab_id));
        $this->check_for_error($result);
        
        return $result;
    }
    
    /**
    * Gets the list of available labs
    * @param none
    */
    function get_lab_list() {
        $return = array();
        
        $result = $this->db->query('SELECT * FROM ' . $this->get_table('labs') . ' WHERE isHidden = 0 AND scheduler = 1 ORDER BY nickname');
        $this->check_for_error($result);
        
        while ($rs = $result->fetchRow())
            $return[] = $this->cleanRow($rs);
        
        return $return;
    }
    
    /**
    * Gets the list of available accounts
    * @param none
    */
    function get_accounts() {
        $return = array();
        
        //$result = $this->db->query('SELECT UNIQUE FRS FROM `usage` ORDER BY FRS');
        $result = $this->db->query('SELECT * FROM accounts ORDER BY FRS');
        $this->check_for_error($result);
        
        while ($rs = $result->fetchRow())
            $return[] = $this->cleanRow($rs);
        
        return $return;
    }
    
    /**
    * Return all announcements
    * @param string $order sort order
    * @param int $datetime the current datetime so we can only get the announcements that we should see
    * @return array of announcements
    */
    function get_announcements($datetime) {
        $return = array();
        
        $query = 'SELECT a.announcement, l.labTitle, l.nickname FROM '
                    . $this->get_table('announcements') .' a'
                    . ' LEFT JOIN ' . $this->get_table('labs') . ' l ON'
                    . ' a.lab_id = l.lab_id'
                    . ' WHERE (start_datetime <= ? AND end_datetime >= ?)'
                    . ' OR (start_datetime IS NULL AND end_datetime >= ?)'
                    . ' OR (start_datetime <= ? AND end_datetime IS NULL)'
                    . ' OR (start_datetime IS NULL AND end_datetime IS NULL)'
                    . " ORDER BY l.nickname, number";
    
        // Prepare query
        $q = $this->db->prepare($query);
        // Execute query
        $result = $this->db->execute($q, array($datetime, $datetime, $datetime, $datetime));
        // Check if error
        $this->check_for_error($result);
        
        if ($result->numRows() <= 0) {
            $this->err_msg = 'There are no announcements.';
            return false;
        }
        
        while ($rs = $result->fetchRow()) {
            $return[] = $this->cleanRow($rs);
        }
        
        $result->free();
        
        return $return;
    }
    
    /**
    * Return all reservations that the user has been invited to or accepted (where they are not the owner)
    * @param string $id user id
    * @param bool $invited_only if we should get only the reservations which the user has been invited and not responded to yet
    * @return array of reservation data
    */
    function get_user_invitations($id, $invited_only = true) {
        $return = array();
        
        $invited = ($invited_only) ? '1' : '0';
        
        $query = "SELECT ru.resid, ru.user_id, ru.accept_code, l.first_name, l.last_name, r.start_date, r.end_date, r.startTime, r.endTime, res.name FROM " . $this->get_table('reservation_users') . " AS ru
                    LEFT JOIN " . $this->get_table('reservations') . " AS r ON ru.resid = r.resid
                    LEFT JOIN " . $this->get_table('resources') . " AS res ON res.machid=r.machid
                    LEFT JOIN " . $this->get_table('reservation_users') . " AS ru2 ON ru.resid=ru2.resid
                    LEFT JOIN " . $this->get_table('user') . " AS l ON l.user_id = ru2.user_id
                    WHERE ru.user_id=?
                    AND (r.start_date>=? OR (r.start_date<=? AND r.end_date>=?))
                    AND ru2.owner=1
                    AND r.is_blackout <> 1
                    AND r.is_pending <> 1
                    AND ru.invited = $invited
                    AND ru.user_id <> ru2.user_id
                    ORDER BY r.start_date, res.name, r.startTime";
        $values = array($id, mktime(0,0,0), mktime(0,0,0), mktime(0,0,0));
        
        // Prepare query
        $q = $this->db->prepare($query);
        // Execute query
        $result = $this->db->execute($q, $values);
        // Check if error
        $this->check_for_error($result);
        
        if ($result->numRows() <= 0) {
            $this->err_msg = translate('You do not have any reservations scheduled.');
            return false;
        }
        
        while ($rs = $result->fetchRow()) {
            $return[] = $this->cleanRow($rs);
        }
        
        $result->free();
        
        return $return;
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
    * Checks to see if there was a database error and die if there was
    * @param object $result result object of query
    */
    function check_for_error($result, $custom_msg=null) {
        if (DB::isError($result))
            CmnFns::do_error_box(translate('There was an error executing your query') . '<br />'
                . $result->getMessage()
                . '<br />' . '<a href="javascript: history.back();">' . translate('Back') . '</a>')
                . '<br />' . $custom_msg . '<br />';
        return false;
    }
    
    /**
    * Generates a new random id for primary keys
    * @param string $prefix string to prefix to id
    * @return random id string
    */
    function get_new_id($prefix = '') {
        // Use the passed in prefix, if it exists
        if (!empty($prefix))
            $this->prefix = $prefix;
        
        // Only use first 3 letters
        $this->prefix = strlen($this->prefix) > 3 ? substr($this->prefix, 0, 3) : $this->prefix;
        
        return uniqid($this->prefix);
    }
    
    /**
    * Enodes a string into an encrypted password string
    * @param string $pass password to encrypt
    * @return encrypted password
    */
    function make_password($pass) {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    function make_old_password($pass) {
        return md5($pass);
    }

    /**
    * Strips out slashes for all data in the return row
    * - THIS MUST ONLY BE ONE ROW OF DATA -
    * @param array $data array of data to clean up
    * @return array with same key => value pairs (except slashes)
    */
    function cleanRow($data) {
        $return = array();
            
        foreach ($data as $key => $val)
            $return[$key] = stripslashes($val);
        return $return;
    }
    
    /**
    * Makes an array of ids in to a comma seperated string of values
    * @param array $data array of data to convert
    * @return string version of the array
    */
    function make_del_list($data) {
        //$c = join('","', $data);
        //return '"' . $c . '"';
        $c = join('\',\'', $data);
        return "'" . $c . "'";
    }
    
    /**
    * Returns the last database error message
    * @param none
    * @return last error message generated
    */
    function get_err() {
        return $this->err_msg;
    }
	
	/**
	* Returns an array of resources that are currently in use
	*/
	function get_resources_in_use(){
		$array = array();
		$sql = "SELECT machid FROM `usage` WHERE signout IS NULL";
		$rs = mysqli_query($sql);
		if(mysqli_num_rows($rs)>0){
			while($row = mysqli_fetch_assoc($rs)){
				array_push($array, $row['machid']);
			}
			return $array;
		}else{
			return false;
		}
	}
	
	/**
	* Method that dynamically adds values to a MYSQL database table using the $_POST vars
	*/
	function AddToDB($tbl, $debug=false)
	{
		// Set the arrays we'll need
		$sql_columns = array();
		$sql_columns_use = array();
		$sql_value_use = array(); 
		
		// Pull the column names from the table $tbl
		$pull_cols = mysqli_query("SHOW COLUMNS FROM ".$tbl) or die("MYSQL ERROR: ".mysqli_error());
	
		// Pull an associative array of the column names and put them into a non-associative array
		while ($columns = 
			mysqli_fetch_assoc($pull_cols))
			  $sql_columns[] = $columns["Field"]; 
			
			foreach( $_POST as $key => $value )
			{
				// Check to see if the variables match up with the column names
				if ( in_array($key, $sql_columns) && trim($value) )
				{
				// If this variable contains the string "DATESTAMP" then use MYSQL function NOW() 
				if ($value == "DATESTAMP") $sql_value_use[] = "NOW()";
				else
				{
					// If this variable contains a 
					// number, then don't add single 
					// quotes, otherwise check to see 
					// if magic quotes are on and use 
					// addslashes if they aren't
					if ( is_numeric($value) ) $sql_value_use[] = $value;
					else $sql_value_use[] = ( get_magic_quotes_gpc() ) ? "'".$value."'" : "'".addslashes($value)."'";
				}
				// Put the column name into the array
				$sql_columns_use[] = $key;
			} 
		} 
	
	// If $sql_columns_use or $sql_value_use 
	// are empty then that means no values 
	// matched
	if ( (sizeof($sql_columns_use) == 0) || 
	(sizeof($sql_value_use) == 0) )
	{
	// Set $Error if no values matched
	$this->Error = "Error: No values were 
	passed that matched any columns.";
	return false;
	}
	else
	{
		// Implode $sql_columns_use and 
		// $sql_value_use into an SQL insert 
		// sqlstatement
		$this->SQLStatement = "INSERT INTO 
		".$tbl." (".implode(",",$sql_columns_use).
		") VALUES (".implode(",",$sql_value_use).
		")"; 

		if($debug){
			echo "<br>SQL: " . $this->SQLStatement . "<br>";
		}
	
	// Execute the newly created statement
	if ( @mysqli_query($this->SQLStatement) )
	   return true;
	else
	{
		// Set $Error if the execution of the 
		// statement fails
		$this->Error = "Error: ".mysql_error();
			//echo "<br><font size='4'>" . $this->SQLStatement . "</font><br>";
			return false;
		}
	}
	}

	function isKFSAvailable($kfs) {
		$sql = 'SELECT count(account_id) AS count FROM accounts WHERE kfs = \''.$kfs.'\'';
		$result = $this->db->query($sql);
		$this->check_for_error($result);
		$rs = $result->fetchRow();
		if($rs['count']>0){
			return false;
		}else{
			return true;
		}
	}
}
?>