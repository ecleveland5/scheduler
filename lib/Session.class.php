<?php
/**
 * Created by PhpStorm.
 * User: erniecleveland
 * Date: 11/2/15
 * Time: 2:09 PM
 */

include_once('Database.class.php');

class Session {
    private $db;

    public function __construct(){
        // Set handler to overide SESSION
        session_set_save_handler(
            array($this, '_open'),
            array($this, '_close'),
            array($this, '_read'),
            array($this, '_write'),
            array($this, '_destroy'),
            array($this, '_gc')
        );
        
        register_shutdown_function('session_write_close');
    }
	
	public function start_session($session_name, $secure) {
		// Make sure the session cookie is not accessible via javascript.
		$httponly = true;
		
		// Hash algorithm to use for the session. (use hash_algos() to get a list of available hashes.)
		$session_hash = 'sha512';
		
		// Check if hash is available
		if (in_array($session_hash, hash_algos())) {
			// Set the has function.
			ini_set('session.hash_function', $session_hash);
		}
		// How many bits per character of the hash.
		// The possible values are '4' (0-9, a-f), '5' (0-9, a-v), and '6' (0-9, a-z, A-Z, "-", ",").
		ini_set('session.hash_bits_per_character', 5);
		
		// Force the session to only use cookies, not URL variables.
		ini_set('session.use_only_cookies', 1);
		
		// Get session cookie parameters
		$cookieParams = session_get_cookie_params();
		// Set the parameters
		session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
		// Change the session name
		session_name($session_name);
		// Now we cat start the session
		session_start();
		// This line regenerates the session and delete the old one.
		// It also generates a new encryption key in the database.
		session_regenerate_id(true);
	}
    
    public function _open(){
        $this->db = new Database();
        if ($this->db) {
            return true;
        } else {
	        echo 'could not open session.';
        }
    }
    public function _close(){
        // Close the database connection
        // If successful
        if($this->db->close()){
            // Return True
            session_write_close();
            return true;
        }
        // Return False
        return false;
    }
    
    public function _read($id){
    	$data = null;
    	
	    if(!isset($this->read_stmt)) {
		    $this->read_stmt = $this->db->prepare("SELECT data FROM sessions WHERE session_hash = ? LIMIT 1");
	    }
	    $this->read_stmt->bind_param('s', $id);
	    $this->read_stmt->execute();
	    $this->read_stmt->store_result();
	    $this->read_stmt->bind_result($data);
	    $this->read_stmt->fetch();
	    $key = $this->getkey($id);
	    $data = $this->decrypt($data, $key);
	    return $data;
    }
    
    public function _write($id, $data){
	    // Get unique key
	    $key = $this->getkey($id);
	    // Encrypt the data
	    $data = $this->encrypt($data, $key);
	
	    $time = time();
	    if(!isset($this->w_stmt)) {
		    $this->w_stmt = $this->db->prepare("REPLACE INTO sessions (session_hash, set_time, data, session_key) VALUES (?, ?, ?, ?)");
	    }
	
	    $this->w_stmt->bind_param('siss', $id, $time, $data, $key);
	    $this->w_stmt->execute();
	    return true;
    }
    public function _destroy($id){
        // Set query
        $this->db->query('DELETE FROM sessions WHERE session_hash = :id');
        // Bind data
        $this->db->bind(':id', $id);
        // Attempt execution
        // If successful
        if($this->db->execute()){
            // Return True
            return true;
        }
        // Return False
        return false;
    }
    public function _gc($max){
        // Calculate what is to be deemed old
        $old = time() - $max;
        // Set query
        $this->db->query('DELETE FROM sessions WHERE expire < :old');
        // Bind data
        $this->db->bind(':old', $old);
        // Attempt execution
        if($this->db->execute()){
            // Return True
            return true;
        }
        // Return False
        return false;
    }
}