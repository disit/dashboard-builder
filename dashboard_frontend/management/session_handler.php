<?php 

// NOTE: DB variables must be available prior to use this object.

class DBSessionHandler implements SessionHandlerInterface {

    // Requires table sessions(id, access, data) on DB

    private static $get_session_query = "SELECT data FROM sessions WHERE id = ? LIMIT 1";
    private static $insert_session_query = "REPLACE INTO sessions VALUES (?, ?, ?)";
    private static $delete_session_query = "DELETE FROM sessions WHERE id = ? LIMIT 1";
    private static $gc_session_query = "DELETE FROM sessions WHERE access < ?";
    private $db;

    private $host, $username, $password, $dbname;

    public function __construct($host, $username, $password, $dbname) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
    }

    /**
     * Opens DB connection.
     */
    public function open($path, $name) {
        $this->db = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if($this->db->connect_errno) {
            return false;
        }
        $this->db->set_charset('utf8');
        return true;
    }

    /**
     * Closes the db connection.
     */
    public function close() {
        return $this->db->close();
    }

    /**
     * Retrive the session from DB.
     * If no session is found, then returns false, as requested by specs.
     */
    public function read($id) {
        $query = $this->db->prepare(self::$get_session_query);
        $query->bind_param('s', $id);
        
        if ($query->execute()) {
            $query->bind_result($data);
            if($query->fetch()) {
                return $data;
            }
        }
        return '';
    }

    /**
     * Updates a session, given its ID and data.
     */
    public function write($id, $data){ 
        $access = time();
        $query = $this->db->prepare(self::$insert_session_query);
        $query->bind_param('sis', $id, $access, $data);
        if ($query->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Removes a session, given its id.
     */
    public function destroy($id) {
        $query = $this->db->prepare(self::$delete_session_query);
        $query->bind_param("s", $id);
        if($query->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Destroy all session older than max_lifetime.
     * Called periodically by PHP.
     */
    public function gc($max_lifetime) {
        $old = time() - 0;
        $query = $this->db->prepare(self::$gc_session_query);
        $query->bind_param("i", $old);
        if ($query->execute()) {
            return true;
        }
        return false;
    }
}
