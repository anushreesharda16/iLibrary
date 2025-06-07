<?php
include_once dirname(__DIR__) . '/config/dB.php';
include_once dirname(__DIR__) . '/config/constants.php';

class Book
{
    public $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function listBook($search = '', $sort = '', $filter = [], $perPage = 10, $page = 1)
    {
        $sql = "SELECT b.id as id, b.title, b.author, b.isbn, c.name as category, b.status
                FROM books b
                JOIN categories c ON b.category_id = c.id";

        $conditions = [];
        $params = [];
        $types = '';

        if (!empty($search)) {
            $conditions[] = "(b.title LIKE ? OR b.author LIKE ? OR c.name LIKE ? OR  b.status LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params = array_merge($params, array_fill(0, 4, $searchTerm));
            $types .= 'ssss';
        }

        // filter based on category
        if (!empty($filter['category_name'])) {
            $conditions[] = 'c.name = ?';
            $params[] = $filter['category_name'];
            $types .= 's';
        }
        //filter based on status
        if (!empty($filter['status'])) {
            $conditions[] = 'b.status = ?';
            $params[] = $filter['status'];
            $types .= 's';
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        switch ($sort) {
            case 'title_asc':
                $sql .= " ORDER BY b.title ASC";
                break;
            case 'title_desc':
                $sql .= " ORDER BY b.title DESC";
                break;
            case 'latest':
                $sql .= " ORDER BY b.id DESC";
                break;
            default:
                $sql .= " ORDER BY b.id";
        }

        $offset = ($page - 1) * $perPage;
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
            $row['images'] = $this->getBookImages($row['id']);
            $books[] = $row;
        }
        $stmt->close();
        return $books;
    }

    public function addBook($title, $image, $author, $category_id, $status = 'available')
    {
        $isbn = uniqid();

        $sql = "INSERT INTO books(title, author, isbn, category_id, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssis', $title, $author, $isbn, $category_id, $status);
        $stmt->execute();

        $book_id = $this->conn->insert_id; //get the inserted book id that was last inserted
        $stmt->close();

        if (!empty($image['tmp_name'][0])) {
            $uploadResult = $this->addBookImages($book_id, $image);
            if ($uploadResult !== true) {
                return $uploadResult; // return error message
            }
        }

        header('Location:../manageBooks.php?msg=' . urlencode("Book added successfully."));
        exit;
    }

    public function updateBook($book_id, $title, $author, $category_id, $status, $newImages = [], $deleteImages = [])
    {
        $sql = "UPDATE books SET title = ?, author = ?, category_id = ?, status = ? WHERE id = ? ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssis', $title, $author, $category_id, $status, $book_id);
        $stmt->execute();
        $stmt->close();

        if (!empty($deleteImages)) {
            foreach ($deleteImages as $singleImgPath) {
                $sql = "DELETE FROM book_images WHERE book_id = ? AND image_path = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('is', $book_id, $singleImgPath);
                $stmt->execute();
                $stmt->close();
            }

            // delete images locally from the system
            $fullPath = dirname(__DIR__) . '/uploads/book_cover_images/' . $singleImgPath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        if (!empty($newImages['tmp_name'][0])) {
            $uploadResult = $this->addBookImages($book_id, $newImages);
            if ($uploadResult !== true) {
                return $uploadResult; // Error message if fails
            }
        }

        header('Location:../manageBooks.php?msg=' . urlencode("Book updated successfully."));
        exit;
    }

    public function deleteBook($book_id)
    {
        //check if book is issued corresponding to the following book_id from issue table
        $sql = "SELECT book_id FROM issues WHERE book_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            return "Action failed: book is currently issued.";
        }

        //deleting image files locally
        $sql = "Select image_path FROM book_images WHERE book_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $book_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $imageDir = dirname(__DIR__) . '/uploads/book_cover_images/';
        while ($row = $result->fetch_assoc()) {
            $imageFile = $imageDir . $row['image_path'];
            if (file_exists($imageFile)) {
                unlink($imageFile);
            }
        }
        $stmt->close();

        //delete image data from table
        $stmt = $this->conn->prepare("DELETE FROM book_images WHERE book_id = ?");
        $stmt->bind_param('i', $book_id);
        $stmt->execute();
        $stmt->close();

        //finally, delete the book from the table
        $stmt = $this->conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $stmt->close();
        return true;

        // header("Location: ../manageBooks.php?msg=" . urlencode("Book deleted successfully."));
        // exit;
    }

    public function countBooks()
    {
        $sql = "SELECT COUNT(*) as total_books From books";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total_books'];
        }
        return 0; //if count = 0 or error
    }

    public function addBookImages($book_id, $images)
    {
        $allowedExt = ['jpg', 'jpeg', 'png'];
        $uploadDir = dirname(__DIR__) . '/uploads/book_cover_images/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($images['tmp_name'] as $index => $tmpName) {
            $originalName = basename($images['name'][$index]);
            $imageExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (!in_array($imageExt, $allowedExt)) {
                return "Only jpg, jpeg and png files are allowed.";
            }

            $imageName = uniqid() . '.' . $imageExt;
            $imagePath = $uploadDir . $imageName;

            if (!move_uploaded_file($tmpName, $imagePath)) {
                return "Failed to upload image: " . $originalName;
            }

            $sql = "INSERT INTO book_images(book_id, image_path) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('is', $book_id, $imageName);
            $stmt->execute();
            $stmt->close();
        }

        return true;
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
        $stmt->close();
        return $images;
    }

    public function getBookById($id)
    {
        $sql = "SELECT b.*, c.name as category FROM books b
            JOIN categories c ON b.category_id = c.id
            WHERE b.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        $stmt->close();

        if ($book) {
            // Fetch images
            $sql = "SELECT image_path FROM book_images WHERE book_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $images = [];
            while ($row = $res->fetch_assoc()) {
                $images[] = $row['image_path'];
            }
            $book['images'] = $images;
            $stmt->close();
        }

        return $book;
    }


    //managing pending request for issueing a book also after approving issue request updating the book table's status from available to issued 
    public function approveIssueRequest($bookId, $userId)
    {
        // start the transaction, will perform both the queries in the transaction
        $this->conn->begin_transaction();

        try {
            // 1. Update the issue record where status = pending
            $sql1 = "UPDATE issues SET status = 'issued', issue_date = NOW(), due_date = DATE_ADD(NOW(), INTERVAL 14 DAY)  
                     WHERE book_id = ? AND user_id = ? AND status = 'pending'";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->bind_param('ii', $bookId, $userId);
            $stmt1->execute();

            // 2. Update the book's status to 'issued' from 'available'
            $sql2 = "UPDATE books SET status = 'issued' WHERE id = ?";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bind_param('i', $bookId);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // if error found then rollback
            $this->conn->rollback();
            return false;
        }
    }

    //list all the books with pending status
    public function listPendingRequests()
    {
        $status = 'pending';
        $sql = "SELECT i.*, b.title as book_title, u.name as user_name 
                FROM issues i
                JOIN books b ON i.book_id = b.id
                JOIN users u ON i.user_id = u.id
                WHERE i.status = ?
                ORDER BY i.request_date";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();

        $requestBooks = [];
        if ($result->num_rows > 0) {
            while ($rows = $result->fetch_assoc()) {
                $requestBooks[] = $rows;
            }
        }
        return $requestBooks;
    }

    public function listRecentPendingRequests() // for dahboard purpose here we will be showing the top 5 recent requests
    {
        $status = 'pending';
        $sql = "SELECT i.*, b.title as book_title, u.name as user_name 
                FROM issues i
                JOIN books b ON i.book_id = b.id
                JOIN users u ON i.user_id = u.id
                WHERE i.status = ?
                ORDER BY i.request_date DESC
                LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();

        $requestBooks = [];
        if ($result->num_rows > 0) {
            while ($rows = $result->fetch_assoc()) {
                $requestBooks[] = $rows;
            }
        }
        return $requestBooks;
    }

    public function totalPendingRequests()
    {
        $sql = "SELECT COUNT(*) as pending_requests From issues WHERE status = 'pending'";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['pending_requests'];
        }
        return 0; //if count = 0 or error
    }


    //list of all the currently issued books
    public function listIssuedBooks()
    {
        $status = 'issued';
        $sql = "SELECT i.*, b.title as book_title, u.name as user_name 
                FROM issues i
                JOIN books b ON i.book_id = b.id
                JOIN users u ON i.user_id = u.id
                WHERE i.status = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();

        $issuedBooks = [];
        if ($result->num_rows > 0) {
            while ($rows = $result->fetch_assoc()) {
                $issuedBooks[] = $rows;
            }
        }
        return $issuedBooks;
    }

    public function totalIssuedBooks()
    {
        $sql = "SELECT COUNT(*) as issued_books From issues WHERE status = 'issued'";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['issued_books'];
        }
        return 0; //if count = 0 or error
    }

    public function listOverdueBooks()
    {
        $status = 'issued';
        $sql = "SELECT i.*, b.title AS book_title, u.name AS user_name 
            FROM issues i
            JOIN books b ON i.book_id = b.id
            JOIN users u ON i.user_id = u.id
            WHERE i.status = ? AND i.due_date < CURDATE()
            ORDER BY i.due_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();

        $overdueBooks = [];
        while ($row = $result->fetch_assoc()) {
            $overdueBooks[] = $row;
        }

        return $overdueBooks;
    }

    public function totalOverdueBooks()
    {
        $sql = "SELECT COUNT(*) as overdue_books From issues WHERE due_date < CURDATE()";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['overdue_books'];
        }
        return 0; //if count = 0 or error
    }

    public function getAllAvailableBooks($search = '', $categoryId = null)
    {
        $search = $this->conn->real_escape_string($search);
        $keyword = '%' . $search . '%';

        $sql = "SELECT COUNT(*) as total FROM books WHERE status = 'available'";

        if (!empty($search)) {
            $sql .= " AND (title LIKE '$keyword' OR author LIKE '$keyword')";
        }

        if (!empty($categoryId)) {
            $sql .= " AND category_id = " . (int)$categoryId;
        }

        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }
}
