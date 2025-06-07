<?php
include_once dirname(__DIR__) . '/config/dB.php';

class UserIssueBook
{
    public $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function issueBook($userId, $bookId)
    {
        $sql = "INSERT INTO issues (book_id, user_id, status) VALUES(? ,? , 'pending')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $bookId, $userId);
        return $stmt->execute();
    }

    public function returnBook($userId, $bookId)
    {
        $this->conn->begin_transaction();
        try {

            $sql1 = "UPDATE issues SET status = 'returned' , return_date = NOW() WHERE user_id = ? AND book_id = ? AND status = 'issued'";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->bind_param('ii', $userId, $bookId);
            $stmt1->execute();

            $sql2 = "UPDATE books SET status = 'available' WHERE id = ?";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bind_param('i', $bookId);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function listIssuedBooks()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user']['id'];

        $sql = "SELECT i.book_id as book_id, b.title as title, b.author as author, c.name as category, i.due_date as due_date
                FROM issues i
                JOIN books b ON i.book_id = b.id
                JOIN categories c ON b.category_id = c.id
                WHERE i.user_id = ? AND i.status = 'issued'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $issuedBooks = [];

        while ($row = $result->fetch_assoc()) {
            $issuedBooks[] = $row;
        }

        return $issuedBooks;
    }

    public function countIssuedBooks()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user']['id'];

        $sql = "SELECT COUNT(*) FROM issues WHERE user_id = ? AND status = 'issued'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count;
    }

    public function countOverdueBooks()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['id'];

        $sql = "SELECT COUNT(*) FROM issues 
            WHERE user_id = ? 
            AND status = 'issued' 
            AND due_date < CURDATE()";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count;
    }

    public function listAvailableBooks($search = '', $sort = '', $categoryId = null, $perPage = 10, $page = 1)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT b.*, c.name AS category
            FROM books b
            JOIN categories c ON b.category_id = c.id
            WHERE b.status = 'available'";

        $params = [];
        $types = '';

        if (!empty($search)) {
            $sql .= " AND (b.title LIKE ? OR b.author LIKE ? OR c.name LIKE ?)";
            $likeSearch = '%' . $search . '%';
            $params[] = $likeSearch;
            $params[] = $likeSearch;
            $params[] = $likeSearch;
            $types .= 'sss';
        }

        if (!is_null($categoryId)) {
            $sql .= " AND b.category_id = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }

        switch ($sort) {
            case 'az':
                $sql .= " ORDER BY b.title ASC";
                break;
            case 'za':
                $sql .= " ORDER BY b.title DESC";
                break;
            default:
                $sql .= " ORDER BY b.id DESC";
                break;
        }

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $books = [];
        while ($row = $result->fetch_assoc()) {
            $bookId = $row['id'];
            $row['images'] = $this->getBookImages($bookId);
            $books[] = $row;
        }

        return $books;
    }

    public function getBookImages($bookId)
    {
        $sql = "SELECT image_path FROM book_images WHERE book_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $result = $stmt->get_result();

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['image_path'];
        }
        return $images;
    }
}
