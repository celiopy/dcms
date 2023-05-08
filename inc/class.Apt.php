<?php

class Apt {
    private $pdo;
    private $id;
  
    public function __construct($pdo, $id = null) {
      $this->pdo = $pdo;
      $this->id = $id;
    }
  
    public function get($slug, $column = 'id') {
      $query = "SELECT ${column} FROM Doctor WHERE Pretty = :slug";
      $stmt = $this->pdo->prepare($query);
      $stmt->bindParam(":slug", $slug);
      $stmt->execute();
      if (! $doctor = $stmt->fetch(PDO::FETCH_ASSOC)) 
        return false;

      return $doctor[$column];
    }

    public function add($data) {
        // Define your response structure
        $response = array(
            'success' => false,
            'message' => '',
            'data' => null
        );

        try {
            // Initialize the SQL statement
            $sql = "INSERT INTO appointment (";

            $data['UserID'] = User::is_logged_in($this->pdo);

            // Create two arrays to store column names and values separately
            $keys = [];
            $values = [];

            // Loop through the array keys and values
            foreach ($data as $key => $value) {
                // Remove the single quotes from the key
                $key = trim($key, "'");
                
                // Add the column name to the array
                $keys[] = $key;
                
                // Add the value to the array
                if ($value === null) {
                    // If the value is null, add the word NULL to the array
                    $values[] = "NULL";
                } else {
                    // Otherwise, add the value with single quotes around it
                    $values[] = "'" . $value . "'";
                }
            }

            // Combine the keys and values arrays into a string and add them to the SQL statement
            $sql .= implode(", ", $keys) . ") VALUES (" . implode(", ", $values) . ")";

            // Prepare the SQL statement
            $stmt = $this->pdo->prepare($sql);

            // Execute the statement
            $stmt->execute();

            // If everything goes well, set success to true and return any data you need
            $response['success'] = true;
            $response['message'] = 'Inserido com sucesso';
        } catch (PDOException $e) {
            // If an error occurs, set success to false and return the error message
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }

        // Return the response as JSON
        echo json_encode($response);
    }

    public function update($data) {
        // Define your response structure
        $response = array(
            'success' => false,
            'message' => '',
            'data' => null
        );

        try {
            // Get the Status value
            $status = $data['Status'];

            // Initialize the columns array
            $columns = ['CheckIn', 'AttendIn', 'FinishedIn'];

            // Get the current values of the columns from the database
            $sql = "SELECT CheckIn, AttendIn, FinishedIn FROM appointment WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            $currentValues = $stmt->fetch(PDO::FETCH_ASSOC);

            foreach ($columns as $column) {
                $data[$column] = $currentValues[$column];
            }

            // Set the value in the $data array based on the Status value
            if ($status == 1) {
                foreach ($columns as $column) {
                    $data[$column] = null;
                }
            } 
            elseif ($status == 2) {
                foreach ($columns as $column) {
                    if ($column != 'CheckIn') 
                        $data[$column] = null;
                }

                $data['CheckIn'] = ( $data['CheckIn'] ?? date('Y-m-d H:i:s') );
            } 
            elseif ($status == 3) {
                $data['CheckIn'] = ( $data['CheckIn'] ?? date('Y-m-d H:i:s') );
                $data['AttendIn'] = ( $data['AttendIn'] ?? date('Y-m-d H:i:s') );
                $data['FinishedIn'] = null;
            } 
            elseif ($status == 4) {
                $data['CheckIn'] = ( $data['CheckIn'] ?? date('Y-m-d H:i:s') );
                $data['AttendIn'] = ( $data['AttendIn'] ?? date('Y-m-d H:i:s') );
                $data['FinishedIn'] = ( $data['FinishedIn'] ?? date('Y-m-d H:i:s') );
            } 
            elseif ($status == 5) {
                foreach ($columns as $column) {
                    $data[$column] = null;
                }

                $data['FinishedIn'] = date('Y-m-d H:i:s');
            }

            unset($data['Status']);

            // Initialize the SQL statement
            $sql = "UPDATE appointment SET ";

            // Loop through the array keys and values
            foreach ($data as $key => $value) {
                // Remove the single quotes from the key
                $key = trim($key, "'");

                // Add the column name and bind parameter placeholder to the SQL statement
                $sql .= $key . " = :" . $key . ", ";
            }

            // Remove the last comma from the SQL statement
            $sql = rtrim($sql, ', ');

            $sql .= " WHERE id = :id";

            // Prepare the SQL statement
            $stmt = $this->pdo->prepare($sql);

            // Bind the parameters to the statement
            foreach ($data as $key => $value) {
                // Remove the single quotes from the key
                $key = trim($key, "'");

                // If the value is null, bind as NULL with explicit data type
                if ($value === null) {
                    $stmt->bindValue(":" . $key, null, PDO::PARAM_NULL);
                } else {
                    // Otherwise, bind as string with explicit data type
                    $stmt->bindValue(":" . $key, $value, PDO::PARAM_STR);
                }
            }

            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT); // Pass by reference and specify the parameter type

            // Execute the statement
            $stmt->execute();

            // If everything goes well, set success to true and return any data you need
            $response['success'] = true;
            $response['message'] = 'Atualizado com sucesso';
        } catch (PDOException $e) {
            // If an error occurs, set success to false and return the error message
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }

        // Return the response as JSON
        echo json_encode($response);
    }

    public function getAppointments($date = null, $slug = null, $id = null, $param = null) {
        $array = [
            'checkin' => ' AND CheckIn IS NOT NULL AND AttendIn IS NULL AND FinishedIn IS NULL', 
            'attendin' => ' AND AttendIn IS NOT NULL AND FinishedIn IS NULL', 
            'finishedin' => ' AND CheckIn IS NOT NULL AND FinishedIn IS NOT NULL', 
            'skip' => ' AND CheckIn IS NULL AND FinishedIn IS NOT NULL'
        ];
        $queue = "";

        $query = "
            SELECT 
                appointment.*, 
                Doctor.id AS doctor_id, 
                Doctor.Name AS doctor_name, 
                Doctor.Color AS doctor_color, 
                Patient.id AS patient_id, 
                Patient.Name AS patient_name  
            FROM appointment 
            JOIN Doctor ON Doctor.id = appointment.DoctorID 
            JOIN Patient ON Patient.id = appointment.PatientID 
            WHERE 
                1 = 1 ";

        $params = [];

        if ($date) {
            $params[':date'] = $date;
            $query .= "AND appointment.DT = :date ";
        }

        if ($slug) {
            $doctor_id = $this->get($slug);
            $params[':doctor_id'] = $doctor_id;
            $query .= "AND appointment.DoctorID = :doctor_id ";
        }

        if ($id) {
            $params[':id'] = $id;
            $query .= "AND appointment.id = :id";
        }

        if ($param) {
            $queue = $array[$param];
        }

        $query .= "{$queue} 
            ORDER BY 
                appointment.DT, appointment.Start, appointment.DoctorID";

        $stmt = $this->pdo->prepare($query);

        foreach ($params as $param => $value) {
            $this->pdo->bindParam($stmt, $param, $value);
            // echo $param . '>' . $value . '; ';
        }

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($id) {
            return $result[0];
        }

        return $result;
    }
}