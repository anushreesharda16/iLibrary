<?php
include_once dirname(__DIR__) . '/config/dB.php';

class UserManage
{
    public $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function listUsers($search = '', $sort = '', $page = 1, $perPage = 10)
    {
        $sql = "SELECT * FROM users";

        $search = $this->conn->real_escape_string($search);
        $offset = ($page - 1) * $perPage;

        if (!empty($search)) {
            $sql .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR role LIKE '%$search%'";
        }

        switch ($sort) {
            case 'az':
                $sql .= " ORDER BY name";
                break;
            case 'za':
                $sql .= " ORDER BY name DESC";
                break;
            case 'latest':
                $sql .= " ORDER BY created_at DESC";
                break;
            case 'oldest':
                $sql .= " ORDER BY created_at";
                break;
            default:
                $sql .= " ORDER BY id";
        }

        $sql .= " LIMIT $perPage OFFSET $offset";

        $users = [];
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }

        return $users;
    }

    public function addUser($name, $email, $password, $role, $status = 'active')
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssss', $name, $email, $hashedPassword, $role, $status);
        $stmt->execute();
        $stmt->close();
    }
    
    public function updateUser($id, $name)
    {
        $sql = "UPDATE users SET name = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $name, $id);
        $stmt->execute();
        $stmt->close();

        print_r(base_url);
        // exit;
        header('Location:../manageUsers.php?msg=' . urlencode("Category updated successfully."));
        exit;
    }

    public function deleteUser($id) 
    {   // check if user has currently issued some book or not
        $sql = "SELECT * FROM issues WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if($result->num_rows > 0)
        {
            return "Action failed: user has currently issued some book.";
        }

        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    public function setStatus($id, $status = 'active')
    {
        $sql = "UPDATE users SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $status, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function userById($id)
    {
        $userId = (int)$id;
        $sql = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $sql->bind_param('i', $userId);
        $sql->execute();
        return $sql->get_result()->fetch_assoc();
    }

    public function totalUsers()
    {
        $sql = "SELECT COUNT(*) as total_users From users";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total_users'];
        }
        return 0; //if count = 0 or error
    }
    
    public function totalActiveUsers()
    {
        $sql = "SELECT COUNT(*) as total_users From users WHERE status = 'active' AND role = 'member'";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total_users'];
        }
        return 0; //if count = 0 or error
    }
    public function totalInactiveUsers()
    {
        $sql = "SELECT COUNT(*) as total_users From users WHERE status = 'inactive' AND role = 'member'";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total_users'];
        }
        return 0; //if count = 0 or error
    }
}
