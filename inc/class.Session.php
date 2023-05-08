<?php

class Session {
    private $db;
    private $user_id;
  
    public function __construct($db, $user_id) {
      $this->db = $db;
      $this->user_id = $user_id;
    }
  
    public function generateToken() {
      return bin2hex(random_bytes(32));
    }
  
    public function create() {
        $token = $this->generateToken();
        $ip_address = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED'] ?? $_SERVER['REMOTE_ADDR'];

        $stmt = $this->db->prepare("INSERT INTO sessions (UserID, token, ip) VALUES (?, ?, INET_ATON(?))");
        $stmt->execute([$this->user_id, $token, $ip_address]);

        $stmt = $this->db->prepare("UPDATE User SET LoggedOn = ? WHERE UserID = ?");
        $stmt->execute([date('Y-m-d H:i:s'), $this->user_id]);

        return $token;
    }

    // Function to destroy current session or selected session
    public static function destroy($db, $others = null) {
        try {
            // Get the session token from the cookie
            $token = ($others ?: $_COOKIE['auth0']);

            // Prepare the SQL statement for selecting the session
            $sql = "UPDATE sessions SET active = 0 WHERE token = ?";
            $stmt = $db->prepare($sql);

            // Execute the prepared statement with the session token
            $stmt->execute([$token]);

            // Delete token in a cookie
            if (!$others) 
                setcookie('auth0', $token, time() - 3600, '/', '', false, true);

            return true;
        } catch (Exception $e) {
            // If an error occurs, log the error message
            error_log("Error in session destroy: " . $e->getMessage());
        }
    }

    public static function get($db, $user_id) {
        $sql = "SELECT id, UserID, token, INET_NTOA(ip) AS ip, active FROM sessions WHERE UserID = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user_id]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) 
            return $result;

        return false;
    }

    public static function getByToken($db, $token) {
        $sql = "SELECT UserID FROM sessions WHERE token = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$token]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) 
            return $result['UserID'];

        return false;
    }
}