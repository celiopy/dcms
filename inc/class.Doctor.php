<?php

class Doctor {
    private $db;
    private $id;
  
    public function __construct($db, $id = null) {
        $this->db = $db;
        $this->id = $id;
    }

    public static function get($db, $id, $column = 'id') {
        $sql = "SELECT ${column} FROM Doctor WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if (! $doctor = $stmt->fetch(PDO::FETCH_ASSOC)) 
          return false;
  
        return $doctor[$column];
    }

    public static function list($db, $query = null) {
        $sql = "SELECT 
            Doctor.* 
        FROM Doctor";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}