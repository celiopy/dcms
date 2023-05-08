<?php

class Patient {
    private $db;
    private $pid;
  
    public function __construct($db, $pid = null) {
        $this->db = $db;
        $this->pid = $pid;
    }

    private function genID($plus = false) {
        $sql = "SELECT id, year FROM PatientIndex ORDER BY year DESC, id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $id = 0;
        $year = date('y');

        if ($result) {
            $id = (int) $result['id'];
            $year = (int) $result['year'];
        }

        if ($plus) {
            $id = $id + 1;
            $sql = "INSERT INTO PatientIndex (id, year) VALUES (:id, :year)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':year', $year);
            $stmt->execute();
        }

        $matID = str_pad($id, 4, '0', STR_PAD_LEFT) . '-' . $year;
        return $matID;
    }

    public function save() {
        try {
            $matID = $this->genID(true);

            $name = $_POST['name'] ?? null;
            $cep = $_POST['cep'] ?? null;
            $birthdate = $_POST['birthdate'] ?? null;
            $rg = $_POST['rg'] ?? null;
            $pf = $_POST['pf'] ?? null;
            $email = $_POST['email'] ?? null;
            $phone = $_POST['phone'] ?? null;
            $phone_res = $_POST['phone_res'] ?? null;

            $sql = "INSERT INTO Patient (MatID, Name, CEP, BirthDate, RG, CPF, Email, Phone, PhoneRes) 
                    VALUES (:matid, :name, :cep, :birthdate, :rg, :cpf, :email, :phone, :phone_res)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':matid', $matID);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':cep', $cep);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':rg', $rg);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':phone_res', $phone_res);
            $stmt->execute();

            $this->pid = $this->db->lastInsertId();
            return $this->pid;
        } catch (PDOException $e) {
            // Handle the error gracefully, e.g. log it or display a user-friendly error message
            echo "An error occurred: " . $e->getMessage();
            return false;
        }
    }
    
    public function get() {
        try {
            $sql = "SELECT * FROM Patient WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $this->pid);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                header ('Location: ' . BASEURL . 'patients');
                exit;
            }

            return $result;
        } catch (PDOException $e) {
            // Handle the error gracefully, e.g. log it or display a user-friendly error message
            echo "An error occurred: " . $e->getMessage();
            return array();
        }
    }

    public static function search($db, $page = 1, $limit = 20, $query = null) {
        try {
            $offset = ($page - 1) * $limit;
            $sql = "SELECT * FROM Patient WHERE 1 = 1";
    
            if ($query) {
                $matid = '%' . $query . '%';
                $name = '%' . $query . '%';
                $cpf = '%' . $query . '%';
                $email = '%' . $query . '%';
                $phone = '%' . $query . '%';
                $phoneRes = '%' . $query . '%';
                $sql .= " AND (MatID LIKE :matid OR Name LIKE :name OR CPF LIKE :cpf OR Email LIKE :email OR Phone LIKE :phone OR PhoneRes LIKE :phoneRes)";
            }

            if (! $query) 
                $sql .= " ORDER BY UpdateAt DESC";

            $stmt = $db->prepare($sql);
    
            if ($query) {
                $stmt->bindParam(':matid', $matid);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':cpf', $cpf);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':phoneRes', $phoneRes);
            }

            $stmt->execute();

            // Get total number of rows
            $totalRows = $stmt->rowCount();

            // add limit and offset
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($sql);

            if ($query) {
                $stmt->bindParam(':matid', $matid);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':cpf', $cpf);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':phoneRes', $phoneRes);
            }
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
    
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array(
                'results' => $results, 
                'totalResults' => $totalRows
            );
        } catch (PDOException $e) {
            // Handle the error gracefully, e.g. log it or display a user-friendly error message
            echo "An error occurred: " . $e->getMessage();
            return array();
        }
    }
}