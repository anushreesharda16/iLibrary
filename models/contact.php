<?php
include_once dirname(__DIR__) . '/config/dB.php';
include_once dirname(__DIR__) . '/config/constants.php';

class Contact 
{   
    public $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function contactStore($email, $subject, $message)
    {
        $sql = "INSERT INTO contact_us (email, subject, message) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $email, $subject, $message);
        $result = $stmt->execute();
        return $result;
    }

    public function listEnquiries($sort = '', $perPage = 10, $page = 1)
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT id, email, subject, message, DATE_FORMAT(contacted_at, '%d-%m-%Y %h:%i %p') AS Date FROM contact_us";

        switch ($sort){
            case 'oldest' :
                $sql .= " ORDER BY DATE(contacted_at) ASC";
                break;
            default :
                $sql .= " ORDER BY DATE(contacted_at) DESC";
        }

        $sql .= " LIMIT $perPage OFFSET $offset";

        $result = $this->conn->query($sql);
       
        $enquiries = [];
        
        if($result && $result->num_rows > 0)
        {
            while($row = $result->fetch_assoc()){
                $enquiries[] = $row;
            }
            return $enquiries;
        }
        return 0;
    }

    public function countEnquiry()
    {
        $sql = "SELECT COUNT(*) as total_enquiries FROM contact_us";
        $result = $this->conn->query($sql);
        if($result && $row = $result->fetch_assoc())
        {
            return $row['total_enquiries'];
        }
        else {
            return 0; // If count = 0 or in cae of error
        }
    }
}


?>