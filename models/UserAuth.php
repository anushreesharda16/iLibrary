<?php
include_once dirname(__DIR__) . '/config/dB.php';
class User
{
    public $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function register($name, $email, $password, $code, $role = 'member')
    {
        if ($this->emailExists($email)) {
            return "Email already exists. Please login.";
        }

        if ($code === 'admin') {
            $role = 'admin';
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssss', $name, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            header('Location:../auth/login.php?msg=' . urlencode("Registered successfully"));
            exit;
        }
        return "Error: try again.";
    }

    public function login($email, $password)
    {
        if (!$this->emailExists($email)) {
            header('Location:../views/auth/register.php?msg=' . urlencode("Email does not exists. Please register."));
            exit;
        }
        $sql = "SELECT status FROM users WHERE email = ? AND status = ?";
        $status = 'inactive';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $email, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return "Can't login. Your status is inactive.";
        }

        $user = $this->validateUser($email, $password);

        if (!$user) {
            return "Invalid credentials.";
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        if ($_SESSION['user']['role'] === 'admin') {
            header('Location:../admin/dashboard.php?msg=' . urldecode("Login successful."));
        } else {
            header('Location:../member/dashboard.php?msg=' . urldecode("Login successful."));
        }
        exit;
    }

    private function emailExists($email)
    {
        $sql = "SELECT * FROM users WHERE email = ? ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    private function validateUser($email, $password)
    {
        $sql = "SELECT id, email, password, role FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $email, $hashedPassword, $role);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                return [
                    'id' => $id,
                    'email' => $email,
                    'role' => $role
                ];
            }
        }
        return false;
    }
}
