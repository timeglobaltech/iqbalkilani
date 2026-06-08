<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid request. Please go back and try again.");
    }

    // Rate limiting: max 5 orders per 30 minutes
    if (!rate_limit('order_submit', 5, 1800)) {
        die("You are submitting too frequently. Please wait and try again.");
    }

    $book_id = (int)($_POST['book_id'] ?? 0);
    $name = sanitize(trim($_POST['customer_name'] ?? ''));
    $email = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $address = sanitize(trim($_POST['shipping_address'] ?? ''));

    // Validate email if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format. Please go back and correct it.");
    }

    // Validate phone if provided (digits, spaces, dashes, plus only)
    if (!empty($phone) && !preg_match('/^[\d\s\-\+]{5,20}$/', $phone)) {
        die("Invalid phone number format.");
    }

    if ($book_id > 0 && !empty($name) && !empty($address)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO orders (book_id, customer_name, customer_email, customer_phone, shipping_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$book_id, $name, $email, $phone, $address]);
            header("Location: books.php?order=success");
            exit;
        } catch(PDOException $e) {
            error_log("Order submit error: " . $e->getMessage());
            die("Something went wrong. Please try again later.");
        }
    } else {
        die("Please fill in all required fields (name, address, and select a book).");
    }
}
die("Invalid request.");
exit;
?>
