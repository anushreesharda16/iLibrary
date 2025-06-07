<?php
include_once '../config/dB.php';

class Book {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function browseBooks() {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'title_asc';

        // Base SQL query
        $sql = "SELECT b.id, b.title, b.author, c.name as category 
                FROM books b
                JOIN category c ON c.id = b.category_id 
                WHERE 1";

        // Add search condition if search is provided
        if (!empty($search)) {
            // Using prepared statements to prevent SQL injection
            $search = "%".$this->conn->real_escape_string($search)."%";
            $sql .= " AND (title LIKE ? OR author LIKE ?)";
        }

        // Determine ORDER BY clause based on $sort value
        switch ($sort) {
            case 'title_asc':
                $orderBy = "ORDER BY title ASC";
                break;
            case 'title_desc':
                $orderBy = "ORDER BY title DESC";
                break;
            case 'author_asc':
                $orderBy = "ORDER BY author ASC";
                break;
            case 'author_desc':
                $orderBy = "ORDER BY author DESC";
                break;
            case 'newest':
                $orderBy = "ORDER BY created_at DESC";
                break;
            default:
                $orderBy = "ORDER BY title ASC";
        }

        $sql .= " $orderBy";

        // Prepare and execute query
        if (!empty($search)) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $search, $search);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        // Fetch all books as associative array
        $books = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }

        return $books;
    }
}

?>