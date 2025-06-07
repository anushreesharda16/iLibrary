<?php
include_once dirname(__DIR__) . '/config/dB.php';

class Category
{
    public $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function addCategory($name)
    {
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $stmt->close();

        header('Location:../manageCategories.php?msg=' . urlencode("Categoty added successfully."));
        exit;
    }

    public function updateCategory($id, $name)
    {
        $sql = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $name, $id);
        $stmt->execute();
        $stmt->close();

        header('Location:../manageCategories.php?msg=' . urlencode("Category updated successfully."));
        exit;
    }

    public function listCategory($search = '', $sort = '', $page = 1, $perPage = 10)
    {
        $sql = "SELECT * FROM categories";
        $offset = ($page - 1) * $perPage;
        $search = $this->conn->real_escape_string($search);

        if (!empty($search)) {
            $sql .= " WHERE name LIKE '%$search%'";
        }

        switch ($sort) {
            case 'az':
                $sql .= " ORDER BY name";
                break;
            case 'za':
                $sql .= " ORDER BY name DESC";
                break;
            default:
                $sql .= " ORDER BY id";
        }

        $sql .= " LIMIT $perPage OFFSET $offset";

        $categories = [];
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }

        return $categories;
    }

    public function getAllCategories()
    {
        $sql = "SELECT id, name FROM categories ORDER BY name ASC";
        $result = $this->conn->query($sql);

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }

    public function deleteCategory($id)
    {
        $sql = "SELECT i.id
                FROM issues i
                JOIN books b ON i.book_id = b.id
                WHERE b.category_id = ? AND i.status = 'issued'
               ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            header('Location:../manageCategories.php?msg=' . urlencode("Action failed: books are currently issued under this category."));
            exit;
        }
        $stmt->close();

        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        header('Location:../manageCategories.php?msg=' . urlencode("Category deleted successfully."));
        exit;
    }

    public function countCategories()
    {
        $sql = "SELECT COUNT(*) as total_categories From categories";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total_categories'];
        }
        return 0; //if count = 0 or error
    }
}
