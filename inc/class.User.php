<?php

class User {
    private $db;
    private $username;
    private $password;
  
    public function __construct($db, $username, $password) {
        $this->db = $db;
        $this->username = $username;
        $this->password = $password;
    }
  
    public function authenticate() {
        $stmt = $this->db->prepare("SELECT * FROM User WHERE UserID = ?");
        $stmt->execute([$this->username]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->password, $user['Password'])) {
                return $user['id'];
            } else {
                throw new Exception("Senha incorreta");
            }
        } else {
            throw new Exception("Usuário inválido");
        }
    }

    // Function to check if the user is already logged in
    public static function is_logged_in($db) {
        // Check if a session token is set in a cookie
        if (isset($_COOKIE['auth0'])) {
            // Get the session token from the cookie
            $token = Session::getByToken($db, $_COOKIE['auth0']);
            if ($token) {
                return $token;
            }

            return false;
        }

        // If no session token is set, or the session is invalid, return false
        return false;
    }

    public static function get($db, $id, $column = 'id') {
        $query = "SELECT ${column} FROM User WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if (! $user = $stmt->fetch(PDO::FETCH_ASSOC)) 
          return false;
  
        return $user[$column];
    }
}